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

Route::get('/inspector/inspections', function () {
    if (!session('user_email') || session('user_role') !== 'inspector') {
        return redirect('/login-demo');
    }
    
    // Get cases that need inspection
    $cases = \App\Models\Cases::where('status', 'in_inspection')
                ->with(['vehicle', 'documents'])
                ->get();
    
    return view('inspector.inspections', ['cases' => $cases]);
});

Route::get('/broker/my-cases', function () {
    if (!session('user_email') || session('user_role') !== 'broker') {
        return redirect('/login-demo');
    }
    
    // In real app, filter by logged-in broker's party ID
    // For demo: show all cases
    $cases = \App\Models\Cases::where('status', 'new')
                ->with(['vehicle', 'declarant'])
                ->get();
    
    return view('broker.cases', ['cases' => $cases]);
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