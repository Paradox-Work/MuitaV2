<!DOCTYPE html>
<html>
<head>
    <title>Add New User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-white">
                👑 Add New User
                <a href="/admin/users" class="btn btn-sm btn-outline-light ms-2">Back to Users</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Create New System User</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/admin/users">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control" required 
                                       placeholder="John Doe">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" required 
                                       placeholder="john@example.com">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" class="form-control" required 
                                       placeholder="Minimum 8 characters">
                                <small class="text-muted">User will use this to login</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Role *</label>
                                <select name="role" class="form-control" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->name }}">
                                        {{ ucfirst($role->name) }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    Admin: Full access<br>
                                    Inspector: Case inspections<br>
                                    Broker: Submit declarations<br>
                                    Analyst: Reports & analytics
                                </small>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-person-plus"></i> Create User
                                </button>
                                <a href="/admin/users" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="alert alert-info mt-4">
                    <h6>📋 Role Permissions:</h6>
                    <ul class="mb-0 small">
                        <li><strong>Admin:</strong> Full system access, user management, all cases</li>
                        <li><strong>Inspector:</strong> View and inspect cases, update inspections</li>
                        <li><strong>Broker:</strong> Submit new declarations, view own cases</li>
                        <li><strong>Analyst:</strong> View reports, risk analysis, statistics</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>