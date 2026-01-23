<!DOCTYPE html>
<html>
<head>
    <title>Risk Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-white">
                📊 Risk Analysis
                <a href="/analyst/reports" class="btn btn-sm btn-outline-light ms-2">Reports</a>
                <a href="/dashboard-demo" class="btn btn-sm btn-outline-light ms-2">Dashboard</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h1>Risk Analysis Dashboard</h1>
        <p class="text-muted">Analysis of risk flags across all cases</p>
        
        <!-- Risk Distribution -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <h5 class="card-title">🔥 High Risk</h5>
                        <p class="display-6 text-danger">{{ $analysis['by_level']['high'] }}</p>
                        <small>{{ $total_cases > 0 ? number_format(($analysis['by_level']['high'] / $total_cases) * 100, 1) : 0 }}%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h5 class="card-title">⚠️ Medium Risk</h5>
                        <p class="display-6 text-warning">{{ $analysis['by_level']['medium'] }}</p>
                        <small>{{ $total_cases > 0 ? number_format(($analysis['by_level']['medium'] / $total_cases) * 100, 1) : 0 }}%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <h5 class="card-title">📊 Low Risk</h5>
                        <p class="display-6 text-info">{{ $analysis['by_level']['low'] }}</p>
                        <small>{{ $total_cases > 0 ? number_format(($analysis['by_level']['low'] / $total_cases) * 100, 1) : 0 }}%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h5 class="card-title">✅ No Risk</h5>
                        <p class="display-6 text-success">{{ $analysis['by_level']['none'] }}</p>
                        <small>{{ $total_cases > 0 ? number_format(($analysis['by_level']['none'] / $total_cases) * 100, 1) : 0 }}%</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Common Flags -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">🚩 Most Common Risk Flags</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($analysis['common_flags'] as $flag => $count)
                    <div class="col-md-4 mb-2">
                        <div class="d-flex justify-content-between align-items-center border rounded p-2">
                            <span class="badge bg-danger">{{ $flag }}</span>
                            <span class="fw-bold">{{ $count }} cases</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- High Risk Cases -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🔥 High Risk Cases ({{ $analysis['high_risk_cases']->count() }})</h5>
            </div>
            <div class="card-body">
                @if($analysis['high_risk_cases']->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Case ID</th>
                                    <th>Vehicle</th>
                                    <th>Risk Flags</th>
                                    <th>Risk Score</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analysis['high_risk_cases'] as $case)
                                <tr class="table-danger">
                                    <td class="fw-bold">{{ $case->id }}</td>
                                    <td>{{ $case->vehicle->plate_no ?? 'N/A' }}</td>
                                    <td>
                                        @foreach($case->risk_flags as $flag)
                                            <span class="badge bg-danger me-1">{{ $flag }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-danger" 
                                                 style="width: {{ $case->risk_score * 10 }}%">
                                                {{ $case->risk_score }}/10
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $case->status === 'in_inspection' ? 'warning' : 
                                            ($case->status === 'new' ? 'primary' : 'secondary')
                                        }}">
                                            {{ $case->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/admin/case/{{ $case->id }}" class="btn btn-sm btn-outline-danger">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No high risk cases found</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>