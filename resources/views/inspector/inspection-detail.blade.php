<!DOCTYPE html>
<html>
<head>
    <title>Inspection {{ $inspection->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-dark">
                🔍 Inspection in Progress
                <a href="/inspector/inspections" class="btn btn-sm btn-outline-dark ms-2">Back to List</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1>Inspection: {{ $inspection->id }}</h1>
                <p class="text-muted">Case: {{ $case->id }} • Started: {{ $inspection->started_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="text-end">
                <div class="badge bg-warning fs-6 px-3 py-2">In Progress</div>
                <p class="text-muted mb-0 mt-1">Inspector: {{ $inspection->assigned_to }}</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <!-- Inspection Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Inspection Checklist</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/inspector/inspection/{{ $inspection->id }}/complete">
                            @csrf
                            
                            <div class="mb-4">
                                <h6>Required Checks:</h6>
                               @if(is_array($inspection->checks) && count($inspection->checks) > 0)
                                @foreach($inspection->checks as $check)
                                    @if(!in_array($check, ['notes']))
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" 
                                            id="check_{{ $loop->index }}" name="checks[]" value="{{ $check }}">
                                        <label class="form-check-label" for="check_{{ $loop->index }}">
                                            {{ ucfirst(str_replace('_', ' ', $check)) }}
                                        </label>
                                    </div>
                                    @endif
                                @endforeach
                            @else
                                <!-- Default checks if none exist -->
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check_1" name="checks[]" value="document_verification" checked>
                                    <label class="form-check-label" for="check_1">Document verification</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check_2" name="checks[]" value="physical_check" checked>
                                    <label class="form-check-label" for="check_2">Physical check</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check_3" name="checks[]" value="risk_assessment">
                                    <label class="form-check-label" for="check_3">Risk assessment</label>
                                </div>
                            @endif
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Inspection Result *</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="result" value="passed" id="result_passed" required>
                                    <label class="form-check-label text-success" for="result_passed">
                                        ✅ Passed - Release shipment
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="result" value="hold" id="result_hold">
                                    <label class="form-check-label text-warning" for="result_hold">
                                        ⚠️ Hold - Requires further review
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="result" value="failed" id="result_failed">
                                    <label class="form-check-label text-danger" for="result_failed">
                                        ❌ Failed - Reject shipment
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Inspection Notes</label>
                                <textarea name="notes" class="form-control" rows="3" 
                                          placeholder="Additional observations, findings, or comments..."></textarea>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="/inspector/inspections" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    📋 Complete Inspection
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Case Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Case Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Case ID:</strong> {{ $case->id }}</p>
                        <p><strong>Vehicle:</strong> {{ $case->vehicle->plate_no ?? 'N/A' }}</p>
                        <p><strong>Declarant:</strong> {{ $case->declarant->name ?? 'N/A' }}</p>
                        <p><strong>Route:</strong> {{ $case->origin_country }} → {{ $case->destination_country }}</p>
                        <p><strong>Arrival:</strong> {{ $case->arrival_ts->format('Y-m-d H:i') }}</p>
                        <p><strong>Documents:</strong> {{ $case->documents->count() }} files</p>
                        
                        <div class="mt-3">
                            <a href="/inspector/case/{{ $case->id }}" class="btn btn-sm btn-outline-primary w-100">
                                View Full Case Details
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Risk Indicators -->
                @if($case->risk_flags)
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">⚠️ Risk Flags</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($case->risk_flags as $flag)
                            <li>{{ ucfirst($flag) }} risk indicator</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>