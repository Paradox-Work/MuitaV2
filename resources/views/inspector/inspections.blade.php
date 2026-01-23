<!DOCTYPE html>
<html>
<head>
    <title>Inspector - Pending Inspections</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .risk-high { border-left: 4px solid #dc3545; }
        .risk-medium { border-left: 4px solid #ffc107; }
        .risk-low { border-left: 4px solid #28a745; }
        .case-card:hover { 
            transform: translateY(-2px); 
            transition: transform 0.2s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .case-id {
            font-family: monospace;
            font-weight: bold;
        }
        .pagination {
            justify-content: center;
            margin-top: 2rem;
        }
        .pagination .page-item.active .page-link {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }
        .pagination .page-item .page-link {
            color: #6c757d;
        }
        .pagination .page-item .page-link:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-dark">
                🔍 Inspector Portal
                <a href="/dashboard-demo" class="btn btn-sm btn-outline-dark ms-2">Dashboard</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Pending Inspections</h1>
                <p class="text-muted mb-0">Review and inspect assigned cases</p>
            </div>
            <div class="btn-group">
                <a href="/inspector/completed" class="btn btn-outline-secondary">Completed</a>
                <a href="/inspector/reports" class="btn btn-outline-secondary">Reports</a>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h5 class="card-title text-primary">Pending Inspections</h5>
                        <p class="display-6">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h5 class="card-title text-success">Completed Today</h5>
                        <p class="display-6">{{ $stats['completed_today'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <h5 class="card-title text-danger">Risk Cases</h5>
                        <p class="display-6">{{ $stats['high_risk'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by Case ID or Vehicle Plate..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="/inspector/inspections" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>
        
        @if($cases->count() > 0)
            <div class="row">
                @foreach($cases as $case)
                @php
                    $riskClass = 'risk-low';
                    $riskLabel = '';
                    if ($case->risk_flags) {
                        if (in_array('high', $case->risk_flags)) {
                            $riskClass = 'risk-high';
                            $riskLabel = 'High Risk';
                        } elseif (in_array('medium', $case->risk_flags)) {
                            $riskClass = 'risk-medium';
                            $riskLabel = 'Medium Risk';
                        } else {
                            $riskLabel = 'Low Risk';
                        }
                    }
                    
                    $docCount = $case->documents->count();
                    $activeInspection = $case->inspections->whereNull('completed_at')->first();
                @endphp
                
                <div class="col-md-6 mb-4">
                    <div class="card case-card h-100 {{ $riskClass }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <span class="case-id">{{ $case->id }}</span>
                                        @if($activeInspection)
                                            <span class="badge bg-warning">In Progress</span>
                                        @endif
                                    </h5>
                                    <p class="text-muted mb-0">
                                        {{ $case->vehicle->plate_no ?? 'No Vehicle' }}
                                        • {{ $case->arrival_ts ? $case->arrival_ts->format('Y-m-d H:i') : 'N/A' }}
                                    </p>
                                </div>
                                <!-- In the case card section, replace the risk badge: -->
                                @if($case->risk_flags)
                                    @php
                                        $riskLevel = $case->risk_level; // Uses the accessor
                                        $riskScore = $case->risk_score;
                                        $riskColors = [
                                            'high' => 'danger',
                                            'medium' => 'warning', 
                                            'low' => 'info',
                                            'none' => 'secondary'
                                        ];
                                        $riskIcons = [
                                            'high' => '⚠️🔥',
                                            'medium' => '⚠️',
                                            'low' => '📊',
                                            'none' => '✅'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $riskColors[$riskLevel] }}" 
                                        data-bs-toggle="tooltip" 
                                        title="Risk Score: {{ $riskScore }}/10 - Flags: {{ implode(', ', $case->risk_flags) }}">
                                        {{ $riskIcons[$riskLevel] }} {{ strtoupper($riskLevel) }} RISK
                                    </span>
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                <p class="mb-1"><strong>Declarant:</strong> {{ $case->declarant->name ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Route:</strong> {{ $case->origin_country }} → {{ $case->destination_country }}</p>
                                <p class="mb-0"><strong>Documents:</strong> 
                                    @if($docCount > 0)
                                        <span class="badge bg-info">{{ $docCount }} files</span>
                                    @else
                                        <span class="badge bg-secondary">No docs</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                @if($activeInspection)
                                    <a href="/inspector/inspection/{{ $activeInspection->id }}" 
                                       class="btn btn-warning">Continue Inspection</a>
                                @else
                                    <a href="/inspector/case/{{ $case->id }}/start" 
                                       class="btn btn-primary">Start Inspection</a>
                                @endif
                                <a href="/inspector/case/{{ $case->id }}" 
                                   class="btn btn-outline-secondary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($cases->hasPages())
            <nav class="mt-4">
                {{ $cases->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
            </nav>
            @endif
            
            <div class="text-center text-muted mt-3">
                Showing {{ $cases->count() }} of {{ $cases->total() }} pending inspections
            </div>
        @else
            <div class="alert alert-success">
                <h5>✅ All clear!</h5>
                <p>No pending inspections at the moment.</p>
                <a href="/inspector/completed" class="btn btn-outline-success">View completed inspections</a>
            </div>
        @endif
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>