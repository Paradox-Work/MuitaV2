<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

// Public welcome page
Route::get('/', function () {
    return view('welcome');
});

// Database login
Route::get('/login-db', function () {
    return view('login-db', [
        'demo_users' => [
            'admin@system.com' => 'admin123',
            'inspector@system.com' => 'insp123', 
            'broker@system.com' => 'broker123',
            'analyst@system.com' => 'analyst123'
        ]
    ]);
});

Route::post('/login-db', function (Request $request) {
    if (Auth::attempt($request->only('email', 'password'))) {
        $user = Auth::user();
        
        session([
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'user_role' => $user->getRoleNames()->first() ?? 'user'
        ]);
        
        return redirect('/dashboard-demo');
    }
    
    return back()->with('error', 'Invalid database credentials');
});

// Session demo login
Route::get('/login-demo', function () {
    return view('login-demo');
});

Route::post('/login-demo', function (Request $request) {
    // Try real database auth first
    if (Auth::attempt($request->only('email', 'password'))) {
        $user = Auth::user();
        
        session([
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'user_role' => $user->getRoleNames()->first() ?? 'user'
        ]);
        
        return redirect('/dashboard-demo');
    }
    
    // Fallback to session demo
    $demoUsers = [
        'admin@demo.com' => ['name' => 'Admin Demo', 'role' => 'admin', 'password' => 'admin123'],
        'inspector@demo.com' => ['name' => 'Inspector Demo', 'role' => 'inspector', 'password' => 'insp123'],
        'broker@demo.com' => ['name' => 'Broker Demo', 'role' => 'broker', 'password' => 'broker123'],
        'analyst@demo.com' => ['name' => 'Analyst Demo', 'role' => 'analyst', 'password' => 'analyst123'],
    ];
    
    $email = $request->email;
    $password = $request->password;
    
    if (isset($demoUsers[$email]) && $demoUsers[$email]['password'] === $password) {
        session([
            'user_email' => $email,
            'user_name' => $demoUsers[$email]['name'],
            'user_role' => $demoUsers[$email]['role']
        ]);
        
        return redirect('/dashboard-demo');
    }
    
    return back()->with('error', 'Invalid credentials');
});

// Dashboard
Route::get('/dashboard-demo', function () {
    if (!session('user_email')) {
        return redirect('/login-demo');
    }
    
    $dbUser = null;
    $permissions = collect();
    
    if ($userId = session('user_id')) {
        $dbUser = User::find($userId);
        if ($dbUser) {
            $permissions = $dbUser->getPermissionsViaRoles()->pluck('name');
        }
    }
    
    return view('dashboard-demo', [
        'user_name' => session('user_name'),
        'user_email' => session('user_email'),
        'user_role' => session('user_role'),
        'db_user' => $dbUser,
        'has_spatie_role' => $dbUser ? $dbUser->hasRole(session('user_role')) : false,
        'permissions' => $permissions
    ]);
});

// Logout
Route::post('/logout-demo', function () {
    session()->flush();
    Auth::logout();
    return redirect('/');
});

// Test Spatie
Route::get('/test-spatie', function () {
    return response()->json([
        'spatie_status' => 'installed',
        'classes_exist' => [
            'Role' => class_exists(Spatie\Permission\Models\Role::class),
            'Permission' => class_exists(Spatie\Permission\Models\Permission::class),
        ],
        'message' => 'Spatie is ready'
    ]);
});


// Add to routes/web.php
Route::get('/admin/cases', function () {
    if (!session('user_email') || session('user_role') !== 'admin') {
        return redirect('/login-demo');
    }
    
    $cases = \App\Models\Cases::with(['vehicle', 'declarant', 'inspections'])
                ->latest()
                ->get();
    
    return view('admin.cases', ['cases' => $cases]);
});
// Admin - System Users
Route::get('/admin/users', function () {
    if (!session('user_email') || session('user_role') !== 'admin') {
        return redirect('/login-demo');
    }
    
    return view('admin.users');
});

Route::get('/inspector/inspections', function (Request $request) {
    if (!session('user_email') || session('user_role') !== 'inspector') {
        return redirect('/login-demo');
    }
    
    $query = \App\Models\Cases::where('status', 'in_inspection')
                ->with(['vehicle', 'documents', 'inspections']);
    
    // Filter by assigned_to if you have that field
    // $query->whereHas('inspections', function($q) {
    //     $q->where('assigned_to', session('user_id'));
    // });
    
    if ($search = $request->input('search')) {
        $query->where(function($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
              ->orWhereHas('vehicle', function($q) use ($search) {
                  $q->where('plate_no', 'like', "%{$search}%");
              });
        });
    }
    
    $cases = $query->paginate(10);
    
    return view('inspector.inspections', [
        'cases' => $cases,
        'stats' => [
            'pending' => \App\Models\Cases::where('status', 'in_inspection')->count(),
            'completed_today' => \App\Models\Inspection::whereDate('completed_at', today())->count(),
            'high_risk' => \App\Models\Cases::whereJsonContains('risk_flags', 'high')->count()
        ]
    ]);
});

// Start new inspection
Route::get('/inspector/case/{id}/start', function ($id) {
    if (!session('user_email') || session('user_role') !== 'inspector') {
        return redirect('/login-demo');
    }
    
    $case = \App\Models\Cases::with(['vehicle', 'documents'])->findOrFail($id);
    
    // Create inspection record
    $inspection = \App\Models\Inspection::create([
        'id' => 'ins-' . uniqid(),
        'case_id' => $id,
        'type' => 'physical',
        'assigned_to' => session('user_name'),
        'location' => 'Checkpoint ' . $case->checkpoint_id,
        'started_at' => now(),
        'checks' => ['document_verification', 'physical_check', 'risk_assessment']
    ]);
    
    // Update case status
    $case->update(['status' => 'in_inspection']);
    
    return redirect("/inspector/inspection/{$inspection->id}");
});

// Inspection detail page
Route::get('/inspector/inspection/{id}', function ($id) {
    if (!session('user_email') || session('user_role') !== 'inspector') {
        return redirect('/login-demo');
    }
    
    $inspection = \App\Models\Inspection::with(['case.vehicle', 'case.documents'])->findOrFail($id);
    
    return view('inspector.inspection-detail', [
        'inspection' => $inspection,
        'case' => $inspection->case
    ]);
});

// KEEP THIS - for submitting/completing inspections (POST method)
Route::post('/inspector/inspection/{id}/complete', function (Request $request, $id) {
    if (!session('user_email') || session('user_role') !== 'inspector') {
        return redirect('/login-demo');
    }
    
    $inspection = \App\Models\Inspection::findOrFail($id);
    $case = $inspection->case;
    
    $request->validate([
        'result' => 'required|in:passed,failed,hold',
        'notes' => 'nullable|string'
    ]);
    
    // Update inspection
    $inspection->update([
        'result' => $request->result,
        'completed_at' => now(),
        'checks' => array_merge($inspection->checks ?? [], ['notes' => $request->notes])
    ]);
    
    // Update case status based on result
    $newStatus = match($request->result) {
        'passed' => 'released',
        'failed' => 'closed',
        'hold' => 'on_hold',
        default => 'closed'
    };
    
    $case->update(['status' => $newStatus]);
    
    return redirect('/inspector/inspections')->with('success', "Inspection completed. Case marked as {$newStatus}.");
});

// ADD THIS - for continuing an in-progress inspection (GET method)
Route::get('/inspector/inspection/{id}/continue', function ($id) {
    if (!session('user_email') || session('user_role') !== 'inspector') {
        return redirect('/login-demo');
    }
    
    $inspection = \App\Models\Inspection::with(['case.vehicle', 'case.documents'])->findOrFail($id);
    
    // Redirect to the inspection detail page
    return redirect("/inspector/inspection/{$inspection->id}");
});

// AND KEEP THIS - the main inspection detail page
Route::get('/inspector/inspection/{id}', function ($id) {
    if (!session('user_email') || session('user_role') !== 'inspector') {
        return redirect('/login-demo');
    }
    
    $inspection = \App\Models\Inspection::with(['case.vehicle', 'case.documents'])->findOrFail($id);
    
    return view('inspector.inspection-detail', [
        'inspection' => $inspection,
        'case' => $inspection->case
    ]);
});

Route::get('/inspector/completed', function (Request $request) {
    if (!session('user_email') || session('user_role') !== 'inspector') {
        return redirect('/login-demo');
    }
    
    $query = \App\Models\Inspection::whereNotNull('completed_at')
                ->with(['case.vehicle'])
                ->orderBy('completed_at', 'desc');
    
    // Filter by date
    if ($date = $request->input('date')) {
        $query->whereDate('completed_at', $date);
    }
    
    // Filter by result
    if ($result = $request->input('result')) {
        $query->where('result', $result);
    }
    
    $inspections = $query->paginate(15);
    
    $stats = [
        'total' => \App\Models\Inspection::whereNotNull('completed_at')->count(),
        'today' => \App\Models\Inspection::whereDate('completed_at', today())->count(),
        'passed' => \App\Models\Inspection::where('result', 'passed')->count(),
        'failed' => \App\Models\Inspection::where('result', 'failed')->count(),
        'hold' => \App\Models\Inspection::where('result', 'hold')->count(),
    ];
    
    return view('inspector.completed', [
        'inspections' => $inspections,
        'stats' => $stats
    ]);
});

// View completed inspection details
Route::get('/inspector/inspection/{id}/view', function ($id) {
    if (!session('user_email') || session('user_role') !== 'inspector') {
        return redirect('/login-demo');
    }
    
    $inspection = \App\Models\Inspection::with(['case.vehicle', 'case.documents', 'case.declarant'])
                    ->findOrFail($id);
    
    return view('inspector.inspection-view', [
        'inspection' => $inspection,
        'case' => $inspection->case
    ]);
});

// Add this route for inspector case view (after your other inspector routes):
Route::get('/inspector/case/{id}', function ($id) {
    if (!session('user_email') || session('user_role') !== 'inspector') {
        return redirect('/login-demo');
    }
    
    $case = \App\Models\Cases::with(['vehicle', 'declarant', 'consignee', 'documents', 'inspections'])
                ->findOrFail($id);
    
    return view('inspector.case-detail', ['case' => $case]);
});

Route::get('/broker/my-cases', function (Request $request) {
    if (!session('user_email') || session('user_role') !== 'broker') {
        return redirect('/login-demo');
    }
    
    $query = \App\Models\Cases::query()->with(['vehicle', 'declarant', 'documents']);
    
    // Search filter
    if ($search = $request->input('search')) {
        $query->where(function($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
              ->orWhereHas('vehicle', function($q) use ($search) {
                  $q->where('plate_no', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
              })
              ->orWhereHas('declarant', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              });
        });
    }
    
    // Status filter
    if ($status = $request->input('status')) {
        $query->where('status', $status);
    }
    
    // Sorting
    switch ($request->input('sort', 'newest')) {
        case 'oldest':
            $query->orderBy('arrival_ts', 'asc');
            break;
        case 'id':
            $query->orderBy('id', 'asc');
            break;
        case 'newest':
        default:
            $query->orderBy('arrival_ts', 'desc');
            break;
    }
    
    $cases = $query->paginate(20)->withQueryString();
    
    // Get stats
    $stats = [
        'total' => \App\Models\Cases::count(),
        'new' => \App\Models\Cases::where('status', 'new')->count(),
        'in_inspection' => \App\Models\Cases::where('status', 'in_inspection')->count(),
        'released' => \App\Models\Cases::where('status', 'released')->count(),
    ];
    
    return view('broker.cases', [
        'cases' => $cases,
        'stats' => $stats,
        'totalCases' => $stats['total']
    ]);
});
// Broker - New Case Form
Route::get('/broker/new-case', function () {
    if (!session('user_email') || session('user_role') !== 'broker') {
        return redirect('/login-demo');
    }
    
    // Get ALL parties (not just type="broker")
    $parties = \App\Models\Party::all();
    $vehicles = \App\Models\Vehicle::limit(50)->get();
    
    return view('broker.new-case', [
        'parties' => $parties,
        'vehicles' => $vehicles
    ]);
});

// Broker - Submit New Case
Route::post('/broker/new-case', function (Request $request) {
    if (!session('user_email') || session('user_role') !== 'broker') {
        return redirect('/login-demo');
    }
    
    // Simple validation
    $request->validate([
        'vehicle_id' => 'required',
        'declarant_id' => 'required',
        'origin_country' => 'required|size:2',
        'destination_country' => 'required|size:2'
    ]);
    
    // Create case
    $case = \App\Models\Cases::create([
        'id' => 'case-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT),
        'vehicle_id' => $request->vehicle_id,
        'declarant_id' => $request->declarant_id,
        'consignee_id' => $request->consignee_id,
        'origin_country' => strtoupper($request->origin_country),
        'destination_country' => strtoupper($request->destination_country),
        'status' => 'new',
        'priority' => $request->priority ?? 'normal',
        'arrival_ts' => now(),
        'checkpoint_id' => 'CP-' . mt_rand(100, 999)
    ]);
    
    return redirect('/broker/my-cases')->with('success', 'Case ' . $case->id . ' created successfully!');
});

Route::get('/analyst/reports', function () {
    if (!session('user_email') || session('user_role') !== 'analyst') {
        return redirect('/login-demo');
    }
    
    $stats = [
        'total_cases' => \App\Models\Cases::count(),
        'pending_inspections' => \App\Models\Cases::where('status', 'in_inspection')->count(),
        'high_risk_cases' => \App\Models\Cases::whereJsonContains('risk_flags', 'high')->count(),
        'completed_today' => \App\Models\Inspection::whereDate('completed_at', today())->count()
    ];
    
    return view('analyst.reports', ['stats' => $stats]);
});

// Add a debug route
Route::get('/debug-data', function () {
    return [
        'total_cases' => \App\Models\Cases::count(),
        'sample_case' => \App\Models\Cases::first(),
        'vehicles' => \App\Models\Vehicle::count()
    ];
});

// Add these routes for document operations
Route::post('/broker/case/{id}/documents', function (Request $request, $id) {
    if (!session('user_email') || session('user_role') !== 'broker') {
        return redirect('/login-demo');
    }
    
    // Simulate document upload (in real app, handle file upload)
    $doc = \App\Models\Document::create([
        'id' => 'doc-' . uniqid(),
        'case_id' => $id,
        'filename' => $request->filename ?? 'document.pdf',
        'mime_type' => $request->mime_type ?? 'application/pdf',
        'category' => $request->category ?? 'invoice',
        'pages' => $request->pages ?? 1,
        'uploaded_by' => session('user_name')
    ]);
    
    return back()->with('success', 'Document uploaded successfully!');
});

Route::delete('/broker/document/{id}', function ($id) {
    if (!session('user_email') || session('user_role') !== 'broker') {
        return redirect('/login-demo');
    }
    
    $doc = \App\Models\Document::find($id);
    if ($doc) {
        $doc->delete();
        return back()->with('success', 'Document deleted!');
    }
    
    return back()->with('error', 'Document not found');
});



// Add route
Route::get('/broker/case/{id}', function ($id) {
    if (!session('user_email') || session('user_role') !== 'broker') {
        return redirect('/login-demo');
    }
    
    $case = \App\Models\Cases::with(['vehicle', 'declarant', 'consignee', 'documents', 'inspections'])
                ->findOrFail($id);
    
    return view('broker.case-detail', ['case' => $case]);
});

// Add route
Route::get('/broker/case/{id}/documents', function ($id) {
    if (!session('user_email') || session('user_role') !== 'broker') {
        return redirect('/login-demo');
    }
    
    $case = \App\Models\Cases::with('documents')->findOrFail($id);
    
    return view('broker.documents', ['case' => $case]);
});

Route::get('/debug-parties', function () {
    return [
        'all_parties' => \App\Models\Party::all(),
        'broker_parties' => \App\Models\Party::where('type', 'broker')->get(),
        'consignee_parties' => \App\Models\Party::where('type', 'consignee')->orWhere('type', 'receiver')->get(),
        'total_count' => \App\Models\Party::count()
    ];
});