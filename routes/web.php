<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
// Helper functions for SQLite compatibility
function calculateAvgInspectionTime() {
    $inspections = \App\Models\Inspection::whereNotNull('completed_at')
                    ->whereNotNull('started_at')
                    ->get();
    
    if ($inspections->isEmpty()) {
        return 0;
    }
    
    $totalMinutes = 0;
    $count = 0;
    
    foreach ($inspections as $inspection) {
        if ($inspection->started_at && $inspection->completed_at) {
            $totalMinutes += $inspection->started_at->diffInMinutes($inspection->completed_at);
            $count++;
        }
    }
    
    return $count > 0 ? round($totalMinutes / $count) : 0;
}

function calculatePassRate() {
    $totalCompleted = \App\Models\Inspection::whereNotNull('completed_at')->count();
    $passed = \App\Models\Inspection::where('result', 'passed')->count();
    
    return $totalCompleted > 0 ? round(($passed / $totalCompleted) * 100, 1) : 0;
}


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

// Add to routes/web.php
Route::get('/admin/dashboard', function () {
    if (!session('user_email') || session('user_role') !== 'admin') {
        return redirect('/login-demo');
    }
    
    // System statistics
    $stats = [
        'total_users' => \App\Models\User::count(),
        'total_cases' => \App\Models\Cases::count(),
        'total_inspections' => \App\Models\Inspection::count(),
        'active_inspectors' => \App\Models\SystemUser::where('role', 'inspector')->where('active', true)->count(),
        'pending_cases' => \App\Models\Cases::where('status', 'new')->count(),
        'inspections_today' => \App\Models\Inspection::whereDate('created_at', today())->count(),
        'released_today' => \App\Models\Cases::where('status', 'released')->whereDate('updated_at', today())->count(),
        'high_risk_cases' => \App\Models\Cases::whereJsonContains('risk_flags', 'high')->count(),
    ];
    
    // Recent activity
    $recentCases = \App\Models\Cases::with(['vehicle', 'declarant'])
        ->latest()
        ->take(10)
        ->get();
    
    $recentUsers = \App\Models\User::latest()
        ->take(5)
        ->get();
    
    return view('admin.dashboard', [
        'stats' => $stats,
        'recentCases' => $recentCases,
        'recentUsers' => $recentUsers
    ]);
});

// Add to routes/web.php
Route::get('/admin/users', function () {
    if (!session('user_email') || session('user_role') !== 'admin') {
        return redirect('/login-demo');
    }
    
    $users = \App\Models\User::with('roles')->latest()->get();
    $systemUsers = \App\Models\SystemUser::all();
    
    return view('admin.users', [
        'users' => $users,
        'systemUsers' => $systemUsers
    ]);
});

Route::get('/admin/users/create', function () {
    if (!session('user_email') || session('user_role') !== 'admin') {
        return redirect('/login-demo');
    }
    
    $roles = \Spatie\Permission\Models\Role::all();
    
    return view('admin.create-user', ['roles' => $roles]);
});

Route::post('/admin/users', function (Request $request) {
    if (!session('user_email') || session('user_role') !== 'admin') {
        return redirect('/login-demo');
    }
    
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
        'role' => 'required|exists:roles,name'
    ]);
    
    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password)
    ]);
    
    $user->assignRole($request->role);
    
    return redirect('/admin/users')->with('success', 'User created successfully!');
});

Route::delete('/admin/users/{id}', function ($id) {
    if (!session('user_email') || session('user_role') !== 'admin') {
        return redirect('/login-demo');
    }
    
    // Prevent deleting yourself
    if ($id == session('user_id')) {
        return back()->with('error', 'Cannot delete your own account');
    }
    
    $user = \App\Models\User::findOrFail($id);
    $user->delete();
    
    return back()->with('success', 'User deleted successfully!');
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

Route::get('/analyst/reports', function (Request $request) {
    if (!session('user_email') || session('user_role') !== 'analyst') {
        return redirect('/login-demo');
    }
    
    $period = $request->get('period', 'today');
    
    // Calculate dates based on period
    $startDate = now()->startOfDay();
    $endDate = now()->endOfDay();
    
    if ($period === 'week') {
        $startDate = now()->startOfWeek();
        $endDate = now()->endOfWeek();
    } elseif ($period === 'month') {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
    } elseif ($period === 'year') {
        $startDate = now()->startOfYear();
        $endDate = now()->endOfYear();
    }
    
    // Get stats
    $stats = [
        'total_cases' => \App\Models\Cases::count(),
        'cases_today' => \App\Models\Cases::whereDate('created_at', today())->count(),
        'pending_inspections' => \App\Models\Cases::where('status', 'in_inspection')->count(),
        'inspections_today' => \App\Models\Inspection::whereDate('started_at', today())->count(),
        'released_today' => \App\Models\Cases::where('status', 'released')
            ->whereDate('updated_at', today())->count(),
        'pass_rate' => \App\Models\Inspection::where('result', 'passed')->count() > 0 ?
            (\App\Models\Inspection::where('result', 'passed')->count() / 
             \App\Models\Inspection::whereNotNull('result')->count() * 100) : 0,
        'avg_inspection_time' => \App\Models\Inspection::avg('inspection_duration') ?? 45,
        'high_risk_cases' => \App\Models\Cases::whereNotNull('risk_flags')
            ->where(function($query) {
                $query->whereJsonContains('risk_flags', 'high_financial')
                      ->orWhereJsonContains('risk_flags', 'high_security');
            })->count(),
        'medium_risk_cases' => \App\Models\Cases::whereNotNull('risk_flags')
            ->where(function($query) {
                $query->whereJsonContains('risk_flags', 'medium')
                      ->orWhereJsonContains('risk_flags', 'compliance');
            })->count(),
        'low_risk_cases' => \App\Models\Cases::whereNotNull('risk_flags')
            ->whereJsonContains('risk_flags', 'low')
            ->orWhereJsonContains('risk_flags', 'routine')
            ->count(),
        'top_origin_countries' => \App\Models\Cases::select('origin_country', \DB::raw('count(*) as total'))
            ->groupBy('origin_country')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
    ];
    
    return view('analyst.reports', [
        'stats' => $stats,
        'period' => $period,
        'startDate' => $startDate,
        'endDate' => $endDate
    ]);
});

// Add to routes
Route::get('/analyst/risk-analysis', function () {
    if (!session('user_email') || !in_array(session('user_role'), ['analyst', 'admin'])) {
        return redirect('/login-demo');
    }
    
    $cases = \App\Models\Cases::with(['vehicle', 'inspections'])->get();
    
    $riskAnalysis = [
        'by_level' => [
            'high' => $cases->where('risk_level', 'high')->count(),
            'medium' => $cases->where('risk_level', 'medium')->count(),
            'low' => $cases->where('risk_level', 'low')->count(),
            'none' => $cases->where('risk_level', 'none')->count(),
        ],
        'by_status' => [],
        'common_flags' => [],
        'high_risk_cases' => $cases->where('is_high_risk', true)->take(10)
    ];
    
    // Count flags
    $flagCounts = [];
    foreach ($cases as $case) {
        if ($case->risk_flags) {
            foreach ($case->risk_flags as $flag) {
                $flagCounts[$flag] = ($flagCounts[$flag] ?? 0) + 1;
            }
        }
    }
    arsort($flagCounts);
    $riskAnalysis['common_flags'] = array_slice($flagCounts, 0, 10, true);
    
    return view('analyst.risk-analysis', [
        'analysis' => $riskAnalysis,
        'total_cases' => $cases->count()
    ]);
});

// Replace the /analyst/reports route (around line 505-550) with this:

Route::get('/analyst/reports', function (Request $request) {
    if (!session('user_email') || session('user_role') !== 'analyst') {
        return redirect('/login-demo');
    }
    
    // Get period filter
    $period = $request->get('period', 'today');
    $now = now();
    
    switch ($period) {
        case 'week':
            $startDate = $now->startOfWeek();
            $endDate = $now->endOfWeek();
            break;
        case 'month':
            $startDate = $now->startOfMonth();
            $endDate = $now->endOfMonth();
            break;
        case 'year':
            $startDate = $now->startOfYear();
            $endDate = $now->endOfYear();
            break;
        default: // today
            $startDate = $now->startOfDay();
            $endDate = $now->endOfDay();
    }
    
    // Calculate REAL statistics
    $totalCases = \App\Models\Cases::count();
    
    // Count cases by risk flags - based on YOUR actual risk flag values
    $highRiskCases = \App\Models\Cases::where(function($query) {
        // Check for ANY risk flag that indicates high risk
        $query->whereJsonContains('risk_flags', 'high_financial')
              ->orWhereJsonContains('risk_flags', 'high_security')
              ->orWhereJsonContains('risk_flags', 'high_value')
              ->orWhereJsonContains('risk_flags', 'VALUE_ANOMALY')
              ->orWhereJsonContains('risk_flags', 'restricted_goods');
    })->get();
    
    $mediumRiskCases = \App\Models\Cases::where(function($query) {
        $query->whereJsonContains('risk_flags', 'suspicious_docs')
              ->orWhereJsonContains('risk_flags', 'unusual_routing')
              ->orWhereJsonContains('risk_flags', 'new_declarant');
    })->get();
    
    $lowRiskCases = \App\Models\Cases::where(function($query) {
        $query->whereJsonContains('risk_flags', 'routine')
              ->orWhereJsonContains('risk_flags', 'compliance');
    })->get();
    
    // Get cases with ANY risk flags
    $casesWithRiskFlags = \App\Models\Cases::whereNotNull('risk_flags')->get();
    
    // Get top origin countries
    $topOriginCountries = \App\Models\Cases::select('origin_country', \DB::raw('count(*) as total'))
        ->groupBy('origin_country')
        ->orderBy('total', 'desc')
        ->limit(5)
        ->get();
    
    // Inspection statistics
    $pendingInspections = \App\Models\Cases::where('status', 'in_inspection')->count();
    $inspectionsToday = \App\Models\Inspection::whereDate('started_at', today())->count();
    $releasedToday = \App\Models\Cases::where('status', 'released')
        ->whereDate('updated_at', today())
        ->count();
    
    // Calculate pass rate from inspections
    $totalInspections = \App\Models\Inspection::whereNotNull('result')->count();
    $passedInspections = \App\Models\Inspection::where('result', 'passed')->count();
    $passRate = $totalInspections > 0 ? ($passedInspections / $totalInspections * 100) : 0;
    
    // Calculate average inspection time (in minutes) - SQLite compatible
    $avgInspectionTime = 45; // Default fallback
    
    try {
        // Try to calculate for inspections with both dates
        $inspections = \App\Models\Inspection::whereNotNull('completed_at')
            ->whereNotNull('started_at')
            ->get();
        
        if ($inspections->count() > 0) {
            $totalMinutes = 0;
            foreach ($inspections as $inspection) {
                $minutes = $inspection->started_at->diffInMinutes($inspection->completed_at);
                $totalMinutes += $minutes;
            }
            $avgInspectionTime = round($totalMinutes / $inspections->count());
        }
    } catch (\Exception $e) {
        // Fallback to default
        $avgInspectionTime = 45;
    }
    
    $stats = [
        'total_cases' => $totalCases,
        'cases_today' => \App\Models\Cases::whereDate('created_at', today())->count(),
        'pending_inspections' => $pendingInspections,
        'inspections_today' => $inspectionsToday,
        'released_today' => $releasedToday,
        'pass_rate' => $passRate,
        'avg_inspection_time' => $avgInspectionTime,
        'high_risk_cases' => $highRiskCases->count(),
        'medium_risk_cases' => $mediumRiskCases->count(),
        'low_risk_cases' => $lowRiskCases->count(),
        'top_origin_countries' => $topOriginCountries,
    ];
    
    return view('analyst.reports', [
        'stats' => $stats,
        'highRiskCases' => $highRiskCases,
        'period' => $period,
        'startDate' => $startDate,
        'endDate' => $endDate
    ]);
});

// Add these helper functions at the TOP of your routes file, after the use statements:


// Risk Matrix View - Fixed for SQLite
Route::get('/analyst/risk-matrix', function () {
    if (!session('user_email') || session('user_role') !== 'analyst') {
        return redirect('/login-demo');
    }
    
    // Get actual risk flag counts from YOUR data
    $riskFlagCounts = [
        'high_financial' => \App\Models\Cases::whereJsonContains('risk_flags', 'high_financial')->count(),
        'high_security' => \App\Models\Cases::whereJsonContains('risk_flags', 'high_security')->count(),
        'suspicious_docs' => \App\Models\Cases::whereJsonContains('risk_flags', 'suspicious_docs')->count(),
        'unusual_routing' => \App\Models\Cases::whereJsonContains('risk_flags', 'unusual_routing')->count(),
        'new_declarant' => \App\Models\Cases::whereJsonContains('risk_flags', 'new_declarant')->count(),
        'high_value' => \App\Models\Cases::whereJsonContains('risk_flags', 'high_value')->count(),
        'VALUE_ANOMALY' => \App\Models\Cases::whereJsonContains('risk_flags', 'VALUE_ANOMALY')->count(),
        'restricted_goods' => \App\Models\Cases::whereJsonContains('risk_flags', 'restricted_goods')->count(),
    ];
    
    return view('analyst.risk-matrix', [
        'riskFlagCounts' => $riskFlagCounts,
        'allRiskFlags' => \App\Models\Cases::whereNotNull('risk_flags')
            ->pluck('risk_flags')
            ->flatten()
            ->unique()
            ->values()
    ]);
});

// Performance Analysis - Fixed for SQLite
Route::get('/analyst/performance', function (Request $request) {
    if (!session('user_email') || session('user_role') !== 'analyst') {
        return redirect('/login-demo');
    }
    
    // Get inspection performance data
    $inspections = \App\Models\Inspection::whereNotNull('completed_at')
                    ->select('completed_at', 'result')
                    ->orderBy('completed_at', 'desc')
                    ->limit(30)
                    ->get()
                    ->groupBy(function($item) {
                        return $item->completed_at->format('Y-m-d');
                    })
                    ->map(function($group) {
                        return [
                            'passed' => $group->where('result', 'passed')->count(),
                            'failed' => $group->where('result', 'failed')->count(),
                            'hold' => $group->where('result', 'hold')->count(),
                            'total' => $group->count()
                        ];
                    });
    
    // Inspector performance - SQLite compatible
    $inspectorPerformance = \App\Models\Inspection::whereNotNull('completed_at')
                             ->select('assigned_to')
                             ->get()
                             ->groupBy('assigned_to')
                             ->map(function($group, $inspector) {
                                 $total = $group->count();
                                 $passed = $group->where('result', 'passed')->count();
                                 
                                 // Calculate average time for this inspector
                                 $times = $group->filter(function($item) {
                                     return $item->started_at && $item->completed_at;
                                 })->map(function($item) {
                                     return $item->started_at->diffInMinutes($item->completed_at);
                                 });
                                 
                                 $avgTime = $times->isNotEmpty() ? round($times->avg()) : 0;
                                 
                                 return [
                                     'total' => $total,
                                     'passed' => $passed,
                                     'pass_rate' => $total > 0 ? round(($passed / $total) * 100, 1) : 0,
                                     'avg_time' => $avgTime
                                 ];
                             });
    
    return view('analyst.performance', [
        'inspections' => $inspections,
        'inspectorPerformance' => $inspectorPerformance
    ]);
});

// Data Export (simplified)
Route::get('/analyst/export', function () {
    if (!session('user_email') || session('user_role') !== 'analyst') {
        return redirect('/login-demo');
    }
    
    // Return JSON data for export
    $data = [
        'cases' => \App\Models\Cases::with(['vehicle', 'declarant'])->limit(100)->get(),
        'inspections' => \App\Models\Inspection::with('case')->limit(100)->get(),
        'exported_at' => now()->toDateTimeString()
    ];
    
    return response()->json($data);
});

// Risk Analysis Page
Route::get('/analyst/risk-analysis', function () {
    if (!session('user_email') || session('user_role') !== 'analyst') {
        return redirect('/login-demo');
    }
    
    $cases = \App\Models\Cases::with(['vehicle', 'inspections'])->get();
    
    // Count risk levels from risk_flags JSON
    $riskLevels = [
        'high' => 0,
        'medium' => 0,
        'low' => 0,
        'none' => 0
    ];
    
    foreach ($cases as $case) {
        if ($case->risk_flags) {
            if (in_array('high', $case->risk_flags)) {
                $riskLevels['high']++;
            } elseif (in_array('medium', $case->risk_flags)) {
                $riskLevels['medium']++;
            } elseif (in_array('low', $case->risk_flags)) {
                $riskLevels['low']++;
            } else {
                $riskLevels['none']++;
            }
        } else {
            $riskLevels['none']++;
        }
    }
    
    // Count common flags
    $flagCounts = [];
    foreach ($cases as $case) {
        if ($case->risk_flags) {
            foreach ($case->risk_flags as $flag) {
                $flagCounts[$flag] = ($flagCounts[$flag] ?? 0) + 1;
            }
        }
    }
    arsort($flagCounts);
    
    $riskAnalysis = [
        'by_level' => $riskLevels,
        'by_status' => $cases->groupBy('status')->map->count(),
        'common_flags' => array_slice($flagCounts, 0, 10, true),
        'high_risk_cases' => $cases->filter(function($case) {
            return $case->risk_flags && in_array('high', $case->risk_flags);
        })->take(10)
    ];
    
    return view('analyst.risk-analysis', [
        'analysis' => $riskAnalysis,
        'total_cases' => $cases->count()
    ]);
});

// REPLACE this route (around line ~815-835):
Route::get('/analyst/risk-matrix', function (Request $request) {
    if (!session('user_email') || session('user_role') !== 'analyst') {
        return redirect('/login-demo');
    }
    
    // Calculate risk statistics
    $highRisk = \App\Models\Cases::where(function($query) {
        $query->whereJsonContains('risk_flags', 'high_financial')
              ->orWhereJsonContains('risk_flags', 'high_security')
              ->orWhereJsonContains('risk_flags', 'high_value')
              ->orWhereJsonContains('risk_flags', 'restricted_goods');
    })->count();
    
    $mediumRisk = \App\Models\Cases::where(function($query) {
        $query->whereJsonContains('risk_flags', 'suspicious_docs')
              ->orWhereJsonContains('risk_flags', 'unusual_routing')
              ->orWhereJsonContains('risk_flags', 'new_declarant')
              ->orWhereJsonContains('risk_flags', 'VALUE_ANOMALY');
    })->count();
    
    $lowRisk = \App\Models\Cases::where(function($query) {
        $query->whereJsonContains('risk_flags', 'routine')
              ->orWhereJsonContains('risk_flags', 'compliance');
    })->count();
    
    // Get paginated cases with risk flags
    $cases = \App\Models\Cases::whereNotNull('risk_flags')
        ->with(['vehicle', 'declarant'])
        ->orderBy('created_at', 'desc')
        ->paginate(20); // 20 per page
    
    $stats = [
        'high_risk' => $highRisk,
        'medium_risk' => $mediumRisk,
        'low_risk' => $lowRisk,
        'total_cases' => \App\Models\Cases::count()
    ];
    
    return view('analyst.risk-matrix', [
        'stats' => $stats,
        'cases' => $cases
    ]);
});

Route::get('/analyst/risk-matrix', function (Request $request) {
    if (!session('user_email') || session('user_role') !== 'analyst') {
        return redirect('/login-demo');
    }
    
    // Calculate risk statistics
    $totalCases = \App\Models\Cases::count();
    
    // Cases with risk flags
    $casesWithFlags = \App\Models\Cases::whereNotNull('risk_flags')
        ->where('risk_flags', '!=', '[]')
        ->count();
    
    // Risk level counts
    $highRisk = \App\Models\Cases::where(function($query) {
        $query->whereJsonContains('risk_flags', 'high_financial')
              ->orWhereJsonContains('risk_flags', 'high_security')
              ->orWhereJsonContains('risk_flags', 'high_value')
              ->orWhereJsonContains('risk_flags', 'restricted_goods');
    })->count();
    
    $mediumRisk = \App\Models\Cases::where(function($query) {
        $query->whereJsonContains('risk_flags', 'suspicious_docs')
              ->orWhereJsonContains('risk_flags', 'unusual_routing')
              ->orWhereJsonContains('risk_flags', 'new_declarant')
              ->orWhereJsonContains('risk_flags', 'VALUE_ANOMALY');
    })->count();
    
    $lowRisk = \App\Models\Cases::where(function($query) {
        $query->whereJsonContains('risk_flags', 'routine')
              ->orWhereJsonContains('risk_flags', 'compliance');
    })->count();
    
    // Get paginated cases with risk flags
    $cases = \App\Models\Cases::whereNotNull('risk_flags')
        ->where('risk_flags', '!=', '[]')
        ->with(['vehicle', 'declarant'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);
    
    // Prepare stats array
    $stats = [
        'high_risk' => $highRisk,
        'medium_risk' => $mediumRisk,
        'low_risk' => $lowRisk,
        'total_cases' => $totalCases,
        'with_flags' => $casesWithFlags,
        'without_flags' => $totalCases - $casesWithFlags
    ];
    
    return view('analyst.risk-matrix', [
        'stats' => $stats,
        'cases' => $cases,
        'totalCases' => $totalCases
    ]);
});

// AJAX search endpoint for Select2
Route::get('/analyst/search-cases', function (Request $request) {
    if (!session('user_email') || session('user_role') !== 'analyst') {
        return response()->json([]);
    }
    
    $search = $request->get('q', '');
    $page = $request->get('page', 1);
    $perPage = 10;
    
    $query = \App\Models\Cases::with(['vehicle', 'declarant']);
    
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
              ->orWhereHas('vehicle', function($q) use ($search) {
                  $q->where('plate_no', 'like', "%{$search}%");
              })
              ->orWhereHas('declarant', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              });
        });
    }
    
    $cases = $query->orderBy('id')
        ->skip(($page - 1) * $perPage)
        ->take($perPage + 1) // Get one extra to check if there are more
        ->get();
    
    $hasMore = $cases->count() > $perPage;
    if ($hasMore) {
        $cases = $cases->take($perPage);
    }
    
    $formatted = $cases->map(function($case) {
        return [
            'id' => $case->id,
            'text' => $case->id,
            'vehicle_plate' => $case->vehicle->plate_no ?? null,
            'declarant_name' => $case->declarant->name ?? null
        ];
    });
    
    return response()->json([
        'items' => $formatted,
        'more' => $hasMore
    ]);
});

Route::get('/analyst/performance', function () {
    if (!session('user_email') || session('user_role') !== 'analyst') {
        return redirect('/login-demo');
    }
    
    return view('analyst.performance');
});

Route::post('/analyst/update-risk-flags', function (Request $request) {
    if (!session('user_email') || session('user_role') !== 'analyst') {
        return redirect('/login-demo');
    }
    
    $case = \App\Models\Cases::find($request->case_id);
    if ($case) {
        $case->risk_flags = $request->risk_flags ?? [];
        $case->save();
        
        return back()->with('success', 'Risk flags updated for case ' . $case->id);
    }
    
    return back()->with('error', 'Case not found');
});

Route::get('/analyst/case/{id}/risk', function ($id) {
    if (!session('user_email') || session('user_role') !== 'analyst') {
        return redirect('/login-demo');
    }
    
    $case = \App\Models\Cases::with(['vehicle', 'declarant'])->findOrFail($id);
    
    return view('analyst.case-risk', ['case' => $case]);
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