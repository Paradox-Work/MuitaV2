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
    </div>
</body>
</html>