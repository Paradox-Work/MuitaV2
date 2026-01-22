Route::post('/login-demo', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    // Try database authentication first
    if (\Auth::attempt($request->only('email', 'password'))) {
        $user = \Auth::user();
        
        // Store in session
        session([
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'user_role' => $user->getRoleNames()->first() ?? 'user'
        ]);
        
        return redirect('/dashboard-demo');
    }
    
    // Fallback to hardcoded demo accounts
    $demoUsers = [
        'admin@demo.com' => ['name' => 'Admin User', 'role' => 'admin', 'password' => 'admin123'],
        'inspector@demo.com' => ['name' => 'Inspector', 'role' => 'inspector', 'password' => 'insp123'],
        'broker@demo.com' => ['name' => 'Broker', 'role' => 'broker', 'password' => 'broker123'],
        'analyst@demo.com' => ['name' => 'Analyst', 'role' => 'analyst', 'password' => 'analyst123'],
    ];
    
    $email = $request->input('email');
    $password = $request->input('password');
    
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