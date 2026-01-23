<!DOCTYPE html>
<html>
<head>
    <title>Completed Inspections</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-white">
                ✅ Completed Inspections
                <a href="/inspector/inspections" class="btn btn-sm btn-outline-light ms-2">Pending</a>
                <a href="/dashboard-demo" class="btn btn-sm btn-outline-light ms-2">Dashboard</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Completed Inspections</h1>
                <p class="text-muted mb-0">History of all completed inspections</p>
            </div>
            <div class="btn-group">
                <a href="/inspector/inspections" class="btn btn-warning">Pending Inspections</a>
                <a href="/inspector/reports" class="btn btn-outline-secondary">Reports</a>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Completed</h5>
                        <p class="display-6">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h5 class="card-title">✅ Passed</h5>
                        <p class="display-6 text-success">{{ $stats['passed'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <h5 class="card-title">❌ Failed</h5>
                        <p class="display-6 text-danger">{{ $stats['failed'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h5 class="card-title">⚠️ On Hold</h5>
                        <p class="display-6 text-warning">{{ $stats['hold'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="date" name="date" class="form-control" 
                               value="{{ request('date') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="result" class="form-control">
                            <option value="">All Results</option>
                            <option value="passed" {{ request('result') == 'passed' ? 'selected' : '' }}>Passed</option>
                            <option value="failed" {{ request('result') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="hold" {{ request('result') == 'hold' ? 'selected' : '' }}>On Hold</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="/inspector/completed" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>
        
        @if($inspections->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Inspection ID</th>
                            <th>Case ID</th>
                            <th>Vehicle</th>
                            <th>Result</th>
                            <th>Completed</th>
                            <th>Duration</th>
                            <th>Inspector</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inspections as $inspection)
                        <tr>
                            <td class="fw-bold">{{ $inspection->id }}</td>
                            <td>{{ $inspection->case_id }}</td>
                            <td>{{ $inspection->case->vehicle->plate_no ?? 'N/A' }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ 
                                    $inspection->result === 'passed' ? 'success' : 
                                    ($inspection->result === 'failed' ? 'danger' : 'warning') 
                                }}">
                                    {{ $inspection->result }}
                                </span>
                            </td>
                            <td>
                                {{ $inspection->completed_at->format('Y-m-d') }}<br>
                                <small class="text-muted">{{ $inspection->completed_at->format('H:i') }}</small>
                            </td>
                            <td>
                                @if($inspection->started_at && $inspection->completed_at)
                                    @php
                                        $duration = $inspection->started_at->diff($inspection->completed_at);
                                        $hours = $duration->h + ($duration->days * 24);
                                        $minutes = $duration->i;
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $inspection->assigned_to }}</td>
                            <td>
                                <a href="/inspector/inspection/{{ $inspection->id }}/view" 
                                   class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($inspections->hasPages())
            <div class="mt-4">
                {{ $inspections->links() }}
            </div>
            @endif
            
            <div class="mt-3 text-muted">
                Showing {{ $inspections->count() }} of {{ $stats['total'] }} completed inspections
            </div>
        @else
            <div class="alert alert-info">
                <h5>No completed inspections</h5>
                <p>No inspections have been completed yet.</p>
                <a href="/inspector/inspections" class="btn btn-info">View pending inspections</a>
            </div>
        @endif
    </div>
</body>
</html>