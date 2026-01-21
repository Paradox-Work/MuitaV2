// routes/web.php
<?php
use Illuminate\Support\Facades\Route;

// Simple session-based login (NO database)
Route::get('/login-demo', function () {
    return view('login-demo');
});

Route::post('/login-demo', function (\Illuminate\Http\Request $request) {
    // Hardcoded users - no database
    $users = [
        'admin@demo.com' => ['name' => 'Admin User', 'role' => 'admin', 'password' => 'admin123'],
        'inspector@demo.com' => ['name' => 'Inspector', 'role' => 'inspector', 'password' => 'insp123'],
        'broker@demo.com' => ['name' => 'Broker', 'role' => 'broker', 'password' => 'broker123'],
        'analyst@demo.com' => ['name' => 'Analyst', 'role' => 'analyst', 'password' => 'analyst123'],
    ];
    
    $email = $request->input('email');
    $password = $request->input('password');
    
    if (isset($users[$email]) && $users[$email]['password'] === $password) {
        // Store in session
        session([
            'user_email' => $email,
            'user_name' => $users[$email]['name'],
            'user_role' => $users[$email]['role']
        ]);
        
        return redirect('/dashboard-demo');
    }
    
    return back()->with('error', 'Invalid credentials');
});

Route::get('/dashboard-demo', function () {
    // Check if logged in
    if (!session('user_email')) {
        return redirect('/login-demo');
    }
    
    // Get role from session
    $role = session('user_role');
    
    // Show different content based on role
    $data = [
        'user_name' => session('user_name'),
        'user_email' => session('user_email'),
        'user_role' => $role
    ];
    
    return view("dashboard-demo", $data);
});

Route::post('/logout-demo', function () {
    session()->flush();
    return redirect('/');
});