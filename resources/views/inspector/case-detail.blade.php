<!DOCTYPE html>
<html>
<head>
    <title>Case {{ $case->id }} - Inspector</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-dark">
                🔍 Case Details
                <a href="/inspector/inspections" class="btn btn-sm btn-outline-dark ms-2">Back to Inspections</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1>Case: {{ $case->id }}</h1>
                <p class="text-muted">Inspection Details</p>
            </div>
            <span class="badge rounded-pill bg-{{ 
                $case->status === 'new' ? 'primary' : 
                ($case->status === 'in_inspection' ? 'warning' : 
                ($case->status === 'released' ? 'success' : 'secondary')) 
            }} fs-6 px-3 py-2">
                {{ $case->status }}
            </span>
        </div>
        
        @if(!$case->inspections->whereNull('completed_at')->isEmpty())
        <div class="alert alert-warning mb-4">
            <h5>⚠️ Inspection in Progress</h5>
            <p>This case is currently being inspected.</p>
            <a href="/inspector/inspection/{{ $case->inspections->whereNull('completed_at')->first()->id }}" 
               class="btn btn-warning">Continue Inspection</a>
        </div>
        @endif
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">📦 Shipment Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Case ID:</th>
                                <td><code>{{ $case->id }}</code></td>
                            </tr>
                            <tr>
                                <th>External Ref:</th>
                                <td>{{ $case->external_ref ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Priority:</th>
                                <td>
                                    <span class="badge bg-{{ 
                                        $case->priority === 'high' ? 'danger' : 
                                        ($case->priority === 'low' ? 'secondary' : 'primary')
                                    }}">
                                        {{ ucfirst($case->priority) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Arrival:</th>
                                <td>{{ $case->arrival_ts ? $case->arrival_ts->format('Y-m-d H:i') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Checkpoint:</th>
                                <td>{{ $case->checkpoint_id ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Origin:</th>
                                <td>{{ $case->origin_country ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Destination:</th>
                                <td>{{ $case->destination_country ?? 'N/A' }}</td>
                            </tr>
                            @if($case->risk_flags)
                            <tr>
                                <th>Risk Flags:</th>
                                <td>
                                    @foreach($case->risk_flags as $flag)
                                    <span class="badge bg-danger me-1">{{ $flag }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">🚚 Vehicle & Parties</h5>
                    </div>
                    <div class="card-body">
                        @if($case->vehicle)
                        <h6>Vehicle:</h6>
                        <p>
                            <strong>{{ $case->vehicle->plate_no }}</strong><br>
                            {{ $case->vehicle->make }} {{ $case->vehicle->model }}<br>
                            VIN: {{ $case->vehicle->vin ?? 'N/A' }}<br>
                            Country: {{ $case->vehicle->country ?? 'N/A' }}
                        </p>
                        @endif
                        
                        <h6 class="mt-3">Declarant:</h6>
                        <p>
                            {{ $case->declarant->name ?? 'N/A' }}<br>
                            <small class="text-muted">
                                {{ $case->declarant->reg_code ?? '' }} • 
                                {{ $case->declarant->country ?? '' }}
                            </small>
                        </p>
                        
                        @if($case->consignee)
                        <h6>Consignee:</h6>
                        <p>
                            {{ $case->consignee->name }}<br>
                            <small class="text-muted">
                                {{ $case->consignee->reg_code ?? '' }} • 
                                {{ $case->consignee->country ?? '' }}
                            </small>
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Documents Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📎 Documents ({{ $case->documents->count() }})</h5>
                <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewDocsModal">
                    View All
                </a>
            </div>
            <div class="card-body">
                @if($case->documents->count() > 0)
                    <div class="list-group">
                        @foreach($case->documents->take(3) as $doc)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $doc->filename }}</strong><br>
                                    <small class="text-muted">{{ $doc->category }} • {{ $doc->mime_type }}</small>
                                </div>
                                <small>{{ $doc->created_at->format('Y-m-d') }}</small>
                            </div>
                        </div>
                        @endforeach
                        @if($case->documents->count() > 3)
                        <div class="list-group-item text-center text-muted">
                            + {{ $case->documents->count() - 3 }} more documents
                        </div>
                        @endif
                    </div>
                @else
                    <p class="text-muted mb-0">No documents attached</p>
                @endif
            </div>
        </div>
        
       <!-- Inspections History -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">🔍 Inspection History ({{ $case->inspections->count() }})</h5>
    </div>
    <div class="card-body">
        @if($case->inspections->count() > 0)
            @foreach($case->inspections as $inspection)
            <div class="border rounded p-3 mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ ucfirst($inspection->type) }} Inspection</strong><br>
                        <small class="text-muted">
                            ID: {{ $inspection->id }} • 
                            Inspector: {{ $inspection->assigned_to ?? 'N/A' }}
                        </small>
                    </div>
                    <div class="text-end">
                        @if($inspection->result && $inspection->completed_at)
                            <span class="badge bg-{{ $inspection->result === 'passed' ? 'success' : 
                                                   ($inspection->result === 'failed' ? 'danger' : 'warning') }}">
                                {{ $inspection->result }}
                            </span><br>
                            <small>{{ $inspection->completed_at->format('Y-m-d H:i') }}</small>
                        @else
                            <span class="badge bg-warning">In Progress</span><br>
                            <small>Started: {{ $inspection->started_at ? $inspection->started_at->format('Y-m-d H:i') : 'N/A' }}</small>
                        @endif
                    </div>
                </div>
                @if($inspection->result && $inspection->completed_at)
                <div class="mt-2">
                    <a href="/inspector/inspection/{{ $inspection->id }}/view" class="btn btn-sm btn-outline-primary">
                        View Report
                    </a>
                </div>
                @endif
            </div>
            @endforeach
        @else
            <p class="text-muted mb-0">No inspections yet</p>
        @endif
        
        @if($case->inspections->whereNull('completed_at')->isEmpty())
        <div class="mt-3">
            <a href="/inspector/case/{{ $case->id }}/start" class="btn btn-primary">
                Start New Inspection
            </a>
        </div>
        @endif
    </div>
</div>
    
    <!-- Documents Modal -->
    <div class="modal fade" id="viewDocsModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">All Documents - Case {{ $case->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($case->documents->count() > 0)
                        <div class="list-group">
                            @foreach($case->documents as $doc)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $doc->filename }}</h6>
                                        <small class="text-muted">
                                            Category: {{ $doc->category }} • 
                                            Type: {{ $doc->mime_type }} • 
                                            Pages: {{ $doc->pages }} • 
                                            Uploaded: {{ $doc->created_at->format('Y-m-d H:i') }}
                                        </small>
                                    </div>
                        
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No documents</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>