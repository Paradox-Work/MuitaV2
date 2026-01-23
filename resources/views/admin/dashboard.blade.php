<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card { transition: transform 0.2s; border-left: 4px solid; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-users { border-left-color: #6610f2; }
        .stat-cases { border-left-color: #20c997; }
        .stat-inspections { border-left-color: #fd7e14; }
        .stat-risks { border-left-color: #dc3545; }
        .recent-activity { max-height: 400px; overflow-y: auto; }
        .system-health { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-white">
                👑 Admin Portal
                <a href="/dashboard-demo" class="btn btn-sm btn-outline-light ms-2">User Dashboard</a>
            </div>
        </div>
    </nav>
    
    <!-- Quick Stats Bar -->
    <div class="bg-light border-bottom py-3">
        <div class="container">
            <div class="row g-3">
                <div class="col-auto">
                    <span class="badge bg-primary">Users: {{ $stats['total_users'] }}</span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-success">Cases: {{ $stats['total_cases'] }}</span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-warning">Inspections: {{ $stats['total_inspections'] }}</span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-danger">High Risk: {{ $stats['high_risk_cases'] }}</span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-info">Active Inspectors: {{ $stats['active_inspectors'] }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mt-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Admin Dashboard</h1>
                <p class="text-muted mb-0">System administration and monitoring</p>
            </div>
            <div class="btn-group">
                <a href="/admin/users" class="btn btn-outline-dark">Manage Users</a>
                <a href="/admin/cases" class="btn btn-outline-dark">All Cases</a>
                <a href="/admin/system" class="btn btn-outline-dark">System Settings</a>
            </div>
        </div>
        
        <!-- Key Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card stat-users h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">👥 Users</h6>
                                <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                                <small class="text-muted">Total system users</small>
                            </div>
                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="/admin/users" class="btn btn-sm btn-outline-primary">Manage Users →</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card stat-card stat-cases h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">📦 Cases</h6>
                                <h3 class="mb-0">{{ $stats['total_cases'] }}</h3>
                                <small class="text-muted">{{ $stats['pending_cases'] }} pending</small>
                            </div>
                            <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                <i class="bi bi-folder"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="/admin/cases" class="btn btn-sm btn-outline-success">View All →</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card stat-card stat-inspections h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">🔍 Inspections</h6>
                                <h3 class="mb-0">{{ $stats['total_inspections'] }}</h3>
                                <small class="text-muted">{{ $stats['inspections_today'] }} today</small>
                            </div>
                            <div class="avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                <i class="bi bi-search"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="/admin/inspections" class="btn btn-sm btn-outline-warning">Monitor →</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card stat-card stat-risks h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">⚠️ High Risk</h6>
                                <h3 class="mb-0">{{ $stats['high_risk_cases'] }}</h3>
                                <small class="text-muted">Require attention</small>
                            </div>
                            <div class="avatar-sm bg-danger rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="/analyst/risk-matrix" class="btn btn-sm btn-outline-danger">Risk Matrix →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity & Quick Actions -->
        <div class="row">
            <!-- Recent Cases -->
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📋 Recent Cases</h5>
                        <a href="/admin/cases" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body recent-activity">
                        @if($recentCases->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentCases as $case)
                                <div class="list-group-item border-0 px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $case->id }}</h6>
                                            <small class="text-muted">
                                                {{ $case->vehicle->plate_no ?? 'No Vehicle' }} • 
                                                {{ $case->declarant->name ?? 'No Declarant' }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ 
                                                $case->status === 'new' ? 'primary' : 
                                                ($case->status === 'in_inspection' ? 'warning' : 'success') 
                                            }}">
                                                {{ $case->status }}
                                            </span><br>
                                            <small class="text-muted">{{ $case->created_at->format('Y-m-d H:i') }}</small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-4">No recent cases found</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions & Recent Users -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">⚡ Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/admin/users/create" class="btn btn-primary">
                                <i class="bi bi-person-plus"></i> Add New User
                            </a>
                            <a href="/admin/cases/create" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Create Test Case
                            </a>
                            <a href="/admin/system/audit" class="btn btn-outline-dark">
                                <i class="bi bi-shield-check"></i> View Audit Log
                            </a>
                            <a href="/admin/system/settings" class="btn btn-outline-secondary">
                                <i class="bi bi-gear"></i> System Settings
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">👤 Recent Users</h5>
                    </div>
                    <div class="card-body">
                        @if($recentUsers->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentUsers as $user)
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">{{ $user->created_at->format('M d') }}</small><br>
                                            <span class="badge bg-info">{{ $user->getRoleNames()->first() ?? 'User' }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-3">No users found</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Health -->
        <div class="card mt-4 system-health text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="mb-2">System Status: <span class="badge bg-success">Operational</span></h3>
                        <p class="mb-0">All systems are running normally. Last updated: {{ now()->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="display-6">🟢 99.9%</div>
                        <small>Uptime this month</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>