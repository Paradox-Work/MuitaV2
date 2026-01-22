<!DOCTYPE html>
<html>
<head>
    <title>Admin - System Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text">
                👑 System Users
                <a href="/dashboard-demo" class="btn btn-sm btn-outline-light ms-2">Back to Dashboard</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h1>System Users (API Users)</h1>
        <p class="text-muted">Users imported from API - separate from auth users</p>
        
        @php
            $users = \App\Models\SystemUser::all();
        @endphp
        
        @if($users->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Active</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                            @if($user->active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-warning">
                No system users found in database.
            </div>
        @endif
    </div>
</body>
</html>