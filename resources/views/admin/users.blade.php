<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .role-badge {
            font-size: 0.75em;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-white">
                👑 User Management
                <a href="/admin/dashboard" class="btn btn-sm btn-outline-light ms-2">Admin Dashboard</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>User Management</h1>
                <p class="text-muted mb-0">Manage system users and permissions</p>
            </div>
            <a href="/admin/users/create" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Add New User
            </a>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="system-users-tab" data-bs-toggle="tab" data-bs-target="#system-users" type="button">
                    System Users ({{ $users->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="api-users-tab" data-bs-toggle="tab" data-bs-target="#api-users" type="button">
                    API Users ({{ $systemUsers->count() }})
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="userTabsContent">
            <!-- System Users Tab -->
            <div class="tab-pane fade show active" id="system-users" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        @if($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Created</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-3">
                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $user->name }}</strong>
                                                        <div class="text-muted small">ID: {{ $user->id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @foreach($user->roles as $role)
                                                <span class="badge role-badge bg-{{ 
                                                    $role->name === 'admin' ? 'danger' : 
                                                    ($role->name === 'inspector' ? 'warning' : 
                                                    ($role->name === 'analyst' ? 'info' : 'primary')) 
                                                }}">
                                                    {{ $role->name }}
                                                </span>
                                                @endforeach
                                            </td>
                                            <td>
                                                {{ $user->created_at->format('Y-m-d') }}<br>
                                                <small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Active
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="#" class="btn btn-outline-primary">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <form method="POST" action="/admin/users/{{ $user->id }}" 
                                                          class="d-inline" 
                                                          onsubmit="return confirm('Delete user {{ $user->name }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-people display-1 text-muted"></i>
                                <h5 class="mt-3">No system users found</h5>
                                <p class="text-muted">Add your first user to get started</p>
                                <a href="/admin/users/create" class="btn btn-primary">Add User</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- API Users Tab -->
            <div class="tab-pane fade" id="api-users" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        @if($systemUsers->count() > 0)
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Last Active</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($systemUsers as $user)
                                    <tr>
                                        <td>
                                            <strong>{{ $user->username }}</strong><br>
                                            <small class="text-muted">ID: {{ $user->id }}</small>
                                        </td>
                                        <td>{{ $user->full_name }}</td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $user->role === 'inspector' ? 'warning' : 
                                                ($user->role === 'admin' ? 'danger' : 'info') 
                                            }}">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($user->active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $user->updated_at->format('Y-m-d') }}<br>
                                            <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-database display-1 text-muted"></i>
                                <h5 class="mt-3">No API users found</h5>
                                <p class="text-muted">API users are imported from external systems</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>