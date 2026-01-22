<!DOCTYPE html>
<html>
<head>
    <title>Broker - My Cases</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .case-table th {
            background-color: #f8f9fa;
        }
        .case-id {
            font-family: monospace;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-white">
                📄 Broker Portal
                <a href="/dashboard-demo" class="btn btn-sm btn-outline-light ms-2">Dashboard</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>My Cases</h1>
                <p class="text-muted mb-0">Track your customs declarations</p>
            </div>
            <a href="/broker/new-case" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> New Declaration
            </a>
        </div>
        
        @if($cases->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover case-table">
                    <thead class="table-light">
                        <tr>
                            <th>Case ID</th>
                            <th>Vehicle</th>
                            <th>Declarant</th>
                            <th>Status</th>
                            <th>Documents</th>
                            <th>Arrival</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cases as $case)
                        <tr>
                            <td class="case-id">{{ $case->id }}</td>
                            <td>
                                @if($case->vehicle)
                                    {{ $case->vehicle->plate_no }}<br>
                                    <small class="text-muted">{{ $case->vehicle->make }} {{ $case->vehicle->model }}</small>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $case->declarant->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ 
                                    $case->status === 'new' ? 'primary' : 
                                    ($case->status === 'in_inspection' ? 'warning' : 
                                    ($case->status === 'released' ? 'success' : 'secondary')) 
                                }}">
                                    {{ $case->status }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $docCount = $case->documents->count();
                                @endphp
                                @if($docCount > 0)
                                    <span class="badge bg-info">{{ $docCount }} files</span>
                                @else
                                    <span class="badge bg-secondary">0 files</span>
                                @endif
                            </td>
                            <td>
                                {{ $case->arrival_ts ? $case->arrival_ts->format('Y-m-d') : 'N/A' }}<br>
                                <small class="text-muted">{{ $case->arrival_ts ? $case->arrival_ts->format('H:i') : '' }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/broker/case/{{ $case->id }}" class="btn btn-outline-primary">View</a>
                                    <a href="/broker/case/{{ $case->id }}/documents" class="btn btn-outline-secondary">Docs</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 text-muted">
                Showing {{ $cases->count() }} case(s)
            </div>
        @else
            <div class="alert alert-info">
                <h5>No cases found</h5>
                <p>You haven't submitted any customs declarations yet.</p>
                <a href="/broker/new-case" class="btn btn-info">Submit your first declaration</a>
            </div>
        @endif
    </div>
</body>
</html>