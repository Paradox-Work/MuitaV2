<!DOCTYPE html>
<html>
<head>
    <title>Inspection {{ $inspection->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-white">
                📄 Inspection Report
                <a href="/inspector/completed" class="btn btn-sm btn-outline-light ms-2">Back to Completed</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <!-- Report Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h1 class="mb-2">Inspection Report: {{ $inspection->id }}</h1>
                        <p class="text-muted mb-0">
                            Case: {{ $case->id }} • 
                            Completed: {{ $inspection->completed_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="display-6">
                            @if($inspection->result === 'passed')
                                <span class="badge bg-success fs-4 px-4 py-2">✅ PASSED</span>
                            @elseif($inspection->result === 'failed')
                                <span class="badge bg-danger fs-4 px-4 py-2">❌ FAILED</span>
                            @else
                                <span class="badge bg-warning fs-4 px-4 py-2">⚠️ ON HOLD</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <!-- Inspection Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Inspection Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th width="30%">Inspection ID:</th>
                                <td>{{ $inspection->id }}</td>
                            </tr>
                            <tr>
                                <th>Type:</th>
                                <td>{{ ucfirst($inspection->type) }} Inspection</td>
                            </tr>
                            <tr>
                                <th>Started:</th>
                                <td>{{ $inspection->started_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Completed:</th>
                                <td>{{ $inspection->completed_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Duration:</th>
                                <td>{{ $inspection->started_at->diff($inspection->completed_at)->format('%H hours %I minutes') }}</td>
                            </tr>
                            <tr>
                                <th>Location:</th>
                                <td>{{ $inspection->location ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Inspector:</th>
                                <td>{{ $inspection->assigned_to ?? 'N/A' }}</td>
                            </tr>
                        </table>
                        
                        @if(isset($inspection->checks['notes']))
                        <div class="mt-3">
                            <h6>Inspector Notes:</h6>
                            <div class="border rounded p-3 bg-light">
                                {{ $inspection->checks['notes'] }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Case Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Case Summary</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Case ID:</strong> {{ $case->id }}</p>
                        <p><strong>Vehicle:</strong> {{ $case->vehicle->plate_no ?? 'N/A' }}</p>
                        <p><strong>Declarant:</strong> {{ $case->declarant->name ?? 'N/A' }}</p>
                        <p><strong>Route:</strong> {{ $case->origin_country }} → {{ $case->destination_country }}</p>
                        <p><strong>Documents:</strong> {{ $case->documents->count() }} files</p>
                        
                        <div class="mt-3">
                            <a href="/inspector/case/{{ $case->id }}" class="btn btn-sm btn-outline-primary w-100">
                                View Full Case
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <h6>Report Actions:</h6>
                        <div class="d-grid gap-2">
                            <button onclick="window.print()" class="btn btn-outline-primary">
                                🖨️ Print Report
                            </button>
                            <a href="/inspector/completed" class="btn btn-outline-secondary">
                                ← Back to Completed
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Checklist (if any) -->
        @if(is_array($inspection->checks) && count($inspection->checks) > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Checklist Items</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($inspection->checks as $key => $check)
                        @if(!in_array($key, ['notes']))
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                <label class="form-check-label">
                                    {{ ucfirst(str_replace('_', ' ', $key)) }}
                                </label>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Print Styles -->
    <style media="print">
        nav, .btn, .no-print { display: none !important; }
        .card { border: 1px solid #000 !important; }
        .badge { border: 1px solid #000 !important; }
    </style>
</body>
</html>