<!DOCTYPE html>
<html>
<head>
    <title>Inspector - Completed Inspections</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .result-passed { border-left: 4px solid #28a745; }
        .result-failed { border-left: 4px solid #dc3545; }
        .result-hold { border-left: 4px solid #ffc107; }
        .inspection-row:hover { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-dark">
                📋 Completed Inspections
                <a href="/inspector/inspections" class="btn btn-sm btn-outline-dark ms-2">Pending</a>
                <a href="/dashboard-demo" class="btn btn-sm btn-outline-dark ms-2">Dashboard</a>
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
                <a href="/inspector/reports" class="btn btn-outline-secondary">Reports</a>
                <button class="btn btn-outline-secondary" onclick="window.print()">Print</button>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
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
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h5 class="card-title">📊 Total</h5>
                        <p class="display-6 text-primary">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" 
                               value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Result</label>
                        <select name="result" class="form-control">
                            <option value="">All Results</option>
                            <option value="passed" {{ request('result') == 'passed' ? 'selected' : '' }}>Passed</option>
                            <option value="failed" {{ request('result') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="hold" {{ request('result') == 'hold' ? 'selected' : '' }}>On Hold</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="/inspector/completed" class="btn btn-outline-secondary">Clear</a>
                        @if(request('date') || request('result'))
                            <span class="ms-3 text-muted">
                                Filtered results
                            </span>
                        @endif
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
                            <th>Case</th>
                            <th>Vehicle</th>
                            <th>Result</th>
                            <th>Duration</th>
                            <th>Completed</th>
                            <th>Inspector</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inspections as $inspection)
                        @php
                            $duration = $inspection->started_at && $inspection->completed_at 
                                ? $inspection->started_at->diff($inspection->completed_at)->format('%Hh %Im')
                                : 'N/A';
                        @endphp
                        <tr class="inspection-row result-{{ $inspection->result }}">
                            <td class="fw-bold">{{ $inspection->id }}</td>
                            <td>
                                <a href="/inspector/case/{{ $inspection->case_id }}" class="text-decoration-none">
                                    {{ $inspection->case_id }}
                                </a>
                            </td>
                            <td>
                                @if($inspection->case->vehicle)
                                    {{ $inspection->case->vehicle->plate_no }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($inspection->result === 'passed')
                                    <span class="badge rounded-pill bg-success">✅ Passed</span>
                                @elseif($inspection->result === 'failed')
                                    <span class="badge rounded-pill bg-danger">❌ Failed</span>
                                @else
                                    <span class="badge rounded-pill bg-warning">⚠️ On Hold</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $duration }}</small>
                            </td>
                            <td>
                                {{ $inspection->completed_at->format('Y-m-d') }}<br>
                                <small class="text-muted">{{ $inspection->completed_at->format('H:i') }}</small>
                            </td>
                            <td>{{ $inspection->assigned_to ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/inspector/inspection/{{ $inspection->id }}/view" 
                                       class="btn btn-outline-primary">Details</a>
                                    <a href="/inspector/case/{{ $inspection->case_id }}" 
                                       class="btn btn-outline-secondary">Case</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $inspections->count() }} of {{ $stats['total'] }} completed inspections
                    @if(request('date') || request('result'))
                        <span class="ms-2">(filtered)</span>
                    @endif
                </div>
                
                @if($inspections->hasPages())
                <div>
                    {{ $inspections->links() }}
                </div>
                @endif
            </div>
            
            <!-- Summary -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">📈 Summary</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Today's Completions:</strong> {{ $stats['today'] }}</p>
                            <p><strong>Pass Rate:</strong> 
                                @if($stats['total'] > 0)
                                    {{ number_format(($stats['passed'] / $stats['total']) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Average Completion Time:</strong> N/A</p>
                            <p><strong>Most Recent:</strong> {{ $inspections->first()->completed_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <h5>No completed inspections found</h5>
                @if(request('date') || request('result'))
                    <p>No inspections match your filter criteria. <a href="/inspector/completed">Clear filters</a></p>
                @else
                    <p>No inspections have been completed yet.</p>
                @endif
                <a href="/inspector/inspections" class="btn btn-outline-info">View pending inspections</a>
            </div>
        @endif
    </div>
    
    <!-- Add view inspection detail route -->
    <script>
        // Quick filters
        document.addEventListener('DOMContentLoaded', function() {
            // Set today's date as default in date filter if empty
            const dateInput = document.querySelector('input[name="date"]');
            if (dateInput && !dateInput.value && window.location.search.includes('date=')) {
                // Keep existing
            }
        });
    </script>
</body>
</html>