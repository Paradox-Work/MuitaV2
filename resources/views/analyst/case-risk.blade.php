<!DOCTYPE html>
<html>
<head>
    <title>Risk Management - {{ $case->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-white">
                ⚠️ Risk Assessment
                <a href="/analyst/risk-matrix" class="btn btn-sm btn-outline-light ms-2">Back to Matrix</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h1>Risk Assessment: {{ $case->id }}</h1>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Case Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Vehicle:</strong> {{ $case->vehicle->plate_no ?? 'N/A' }}</p>
                                <p><strong>Declarant:</strong> {{ $case->declarant->name ?? 'N/A' }}</p>
                                <p><strong>Origin:</strong> {{ $case->origin_country }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> {{ $case->status }}</p>
                                <p><strong>Priority:</strong> {{ $case->priority }}</p>
                                <p><strong>Arrival:</strong> {{ $case->arrival_ts->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Risk Flag Management</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/analyst/update-risk-flags">
                            @csrf
                            <input type="hidden" name="case_id" value="{{ $case->id }}">
                            
                            <div class="mb-3">
                                <label class="form-label">Current Risk Flags:</label>
                                <div>
                                    @if($case->risk_flags && count($case->risk_flags) > 0)
                                        @foreach($case->risk_flags as $flag)
                                        <span class="badge bg-danger me-1 mb-1">{{ $flag }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No risk flags set</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Add/Remove Risk Flags:</label>
                                <div class="row">
                                    @foreach([
                                        'high_financial' => 'High Financial Risk',
                                        'high_security' => 'High Security Risk',
                                        'suspicious_docs' => 'Suspicious Documents',
                                        'unusual_routing' => 'Unusual Routing',
                                        'new_declarant' => 'New Declarant',
                                        'high_value' => 'High Value Shipment'
                                    ] as $key => $label)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="risk_flags[]" value="{{ $key }}" 
                                                   id="flag_{{ $key }}"
                                                   {{ $case->risk_flags && in_array($key, $case->risk_flags) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="flag_{{ $key }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Risk Level Assessment:</label>
                                <select name="risk_level" class="form-control">
                                    <option value="">Auto-assess based on flags</option>
                                    <option value="high" {{ str_contains(implode('', $case->risk_flags ?? []), 'high') ? 'selected' : '' }}>
                                        High Risk
                                    </option>
                                    <option value="medium">Medium Risk</option>
                                    <option value="low">Low Risk</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Notes/Justification:</label>
                                <textarea name="notes" class="form-control" rows="3" 
                                          placeholder="Add justification for risk assessment..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-danger">
                                ⚠️ Update Risk Assessment
                            </button>
                            <a href="/analyst/risk-matrix" class="btn btn-outline-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Risk Assessment Guidelines</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <h6>🔴 High Risk Indicators:</h6>
                            <ul class="mb-0 small">
                                <li>New declarant with no history</li>
                                <li>High value shipments (> €10,000)</li>
                                <li>Unusual routing patterns</li>
                                <li>Suspicious document patterns</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h6>🟡 Medium Risk Indicators:</h6>
                            <ul class="mb-0 small">
                                <li>Established declarant with minor issues</li>
                                <li>Medium value shipments (€5,000-€10,000)</li>
                                <li>Slightly unusual documentation</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-success">
                            <h6>🟢 Low Risk Indicators:</h6>
                            <ul class="mb-0 small">
                                <li>Trusted, established declarant</li>
                                <li>Low value shipments</li>
                                <li>Complete, standard documentation</li>
                                <li>Normal routing patterns</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>