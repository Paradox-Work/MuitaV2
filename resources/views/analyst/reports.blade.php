<!DOCTYPE html>
<html>
<head>
    <title>Analyst - Reports & Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card { 
            transition: transform 0.2s; 
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .chart-container { 
            position: relative; 
            height: 300px; 
        }
        .nav-pills-custom .nav-link {
            border-radius: 20px;
            padding: 8px 20px;
            margin-right: 5px;
            border: 1px solid #dee2e6;
        }
        .nav-pills-custom .nav-link.active {
            background-color: #6c757d;
            color: white;
            border-color: #6c757d;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-white">
                📊 Analytics Portal
                <a href="/dashboard-demo" class="btn btn-sm btn-outline-light ms-2">Dashboard</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Risk Analysis & Reports</h1>
                <p class="text-muted mb-0">Customs declaration analytics and risk assessment</p>
            </div>
            <div class="btn-group">
                <a href="/analyst/risk-matrix" class="btn btn-outline-dark">Risk Matrix</a>
            </div>
        </div>
        
        <!-- Navigation Tabs -->
        <ul class="nav nav-pills nav-pills-custom mb-4">
            <li class="nav-item">
                <a class="nav-link active" href="/analyst/reports">📈 Overview</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/analyst/risk-matrix">⚠️ Risk Matrix</a>
            </li>
        </ul>
        
        <!-- Period Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="form-label">Report Period:</label>
                    </div>
                    <div class="col-auto">
                        <select name="period" class="form-select" onchange="this.form.submit()">
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Cases</h5>
                        <p class="display-6">{{ number_format($stats['total_cases']) }}</p>
                        <small>+{{ $stats['cases_today'] }} today</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Pending Inspections</h5>
                        <p class="display-6">{{ $stats['pending_inspections'] }}</p>
                        <small>{{ $stats['inspections_today'] }} started today</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Released Today</h5>
                        <p class="display-6">{{ $stats['released_today'] }}</p>
                        <small>{{ number_format($stats['pass_rate'], 1) }}% pass rate</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Avg. Inspection Time</h5>
                        <p class="display-6">{{ number_format($stats['avg_inspection_time']) }}m</p>
                        <small>Efficiency metric</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Risk Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="riskChart"></canvas>
                        </div>
                        <div class="row text-center mt-3">
                            <div class="col-4">
                                <span class="badge bg-danger fs-6 p-2">{{ $stats['high_risk_cases'] }} High</span>
                            </div>
                            <div class="col-4">
                                <span class="badge bg-warning fs-6 p-2">{{ $stats['medium_risk_cases'] }} Medium</span>
                            </div>
                            <div class="col-4">
                                <span class="badge bg-success fs-6 p-2">{{ $stats['low_risk_cases'] }} Low</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Top Origin Countries</h5>
                    </div>
                    <div class="card-body">
                        @if(count($stats['top_origin_countries']) > 0)
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Country</th>
                                        <th>Cases</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['top_origin_countries'] as $country)
                                    <tr>
                                        <td>{{ $country->origin_country ?? 'Unknown' }}</td>
                                        <td>{{ $country->total ?? 0 }}</td>
                                        <td>
                                            @php
                                                $percentage = $stats['total_cases'] > 0 ? 
                                                    (($country->total ?? 0) / $stats['total_cases'] * 100) : 0;
                                            @endphp
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <small>{{ number_format($percentage, 1) }}%</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">No country data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Risk Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">📊 Risk Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Risk Level Distribution:</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>High Risk</span>
                                <span class="badge bg-danger">{{ $stats['high_risk_cases'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Medium Risk</span>
                                <span class="badge bg-warning">{{ $stats['medium_risk_cases'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Low Risk</span>
                                <span class="badge bg-success">{{ $stats['low_risk_cases'] }}</span>
                            </div>
                        </div>
                        <a href="/analyst/risk-matrix" class="btn btn-outline-primary w-100 mt-3">
                            Go to Risk Matrix
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">⚡ Performance</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Efficiency Metrics:</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Avg. Inspection Time</span>
                                <span>{{ number_format($stats['avg_inspection_time']) }}m</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Pass Rate</span>
                                <span>{{ number_format($stats['pass_rate'], 1) }}%</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Pending Inspections</span>
                                <span>{{ $stats['pending_inspections'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">📈 Quick Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Daily Summary
                                <span class="badge bg-primary rounded-pill">New</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                Weekly Analytics
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                Monthly Report
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                Risk Assessment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pagination (if needed for tables) -->
        @if(isset($cases) && $cases->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    @if($cases->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $cases->previousPageUrl() }}">Previous</a></li>
                    @endif
                    
                    @for($i = 1; $i <= $cases->lastPage(); $i++)
                        <li class="page-item {{ $cases->currentPage() == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ $cases->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    
                    @if($cases->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $cases->nextPageUrl() }}">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>
        @endif
    </div>
    
    <script>
        // Risk Distribution Chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('riskChart').getContext('2d');
            const riskChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['High Risk', 'Medium Risk', 'Low Risk'],
                    datasets: [{
                        data: [
                            {{ $stats['high_risk_cases'] ?? 0 }},
                            {{ $stats['medium_risk_cases'] ?? 0 }},
                            {{ $stats['low_risk_cases'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#dc3545',
                            '#ffc107', 
                            '#28a745'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const value = context.raw;
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>