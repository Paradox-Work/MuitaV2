<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text">
                {{ $user_name }} ({{ $user_role }})
                <form method="POST" action="/logout-demo" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light ms-2">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h1>{{ ucfirst($user_role) }} Dashboard</h1>
        
        @if($user_role === 'admin')
            <div class="alert alert-success">
                <h4>👑 Admin Access</h4>
                <p>You have full system privileges.</p>
            </div>
        @elseif($user_role === 'inspector')
            <div class="alert alert-warning">
                <h4>🔍 Inspector Access</h4>
                <p>You can review and inspect cases.</p>
            </div>
        @elseif($user_role === 'broker')
            <div class="alert alert-info">
                <h4>📄 Broker Access</h4>
                <p>You can submit customs declarations.</p>
            </div>
        @elseif($user_role === 'analyst')
            <div class="alert alert-secondary">
                <h4>📊 Analyst Access</h4>
                <p>You can view reports and analytics.</p>
            </div>
        @endif
        
        <!-- ADD THIS SECTION FOR QUICK ACTIONS -->
        <div class="mt-4">
            <h3>Quick Actions:</h3>
            
            @if($user_role === 'admin')
                <a href="/admin/cases" class="btn btn-primary">View All Cases</a>
                <a href="/admin/users" class="btn btn-secondary">Manage Users</a>
            @elseif($user_role === 'inspector')
                <a href="/inspector/inspections" class="btn btn-primary">My Inspections</a>
            @elseif($user_role === 'broker')
                <a href="/broker/my-cases" class="btn btn-primary">My Cases</a>
                <a href="/broker/new-case" class="btn btn-success">New Declaration</a>
            @elseif($user_role === 'analyst')
                <a href="/analyst/reports" class="btn btn-primary">View Reports</a>
            @endif
        </div>
        
        <!-- Keep your existing role features list -->
        <div class="mt-4">
            <h3>Role-based Features:</h3>
            <ul>
                @if($user_role === 'admin')
                    <li>Manage all users</li>
                    <li>System configuration</li>
                    <li>View all cases</li>
                @elseif($user_role === 'inspector')
                    <li>Review assigned cases</li>
                    <li>Submit inspection reports</li>
                    <li>Flag suspicious items</li>
                @elseif($user_role === 'broker')
                    <li>Submit new declarations</li>
                    <li>Track case status</li>
                    <li>Upload documents</li>
                @elseif($user_role === 'analyst')
                    <li>View risk reports</li>
                    <li>Generate statistics</li>
                    <li>Monitor trends</li>
                @endif
            </ul>
        </div>
        
        <!-- Keep Spatie info section -->
        @if($db_user)
            <div class="alert alert-info mt-3">
                <h5>✅ Database User with Spatie Roles</h5>
                <p>User ID: {{ $db_user->id }}</p>
                <p>Roles: {{ $db_user->getRoleNames()->implode(', ') }}</p>
                <p>Permissions: {{ $permissions->implode(', ') }}</p>
            </div>
        @else
            <div class="alert alert-warning mt-3">
                <h5>⚠️ Session-based Demo User</h5>
                <p>Using temporary session data. <a href="/login-db">Switch to database login</a></p>
            </div>
        @endif
    </div>
</body>
</html>