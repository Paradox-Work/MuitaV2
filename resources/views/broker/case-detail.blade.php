<!DOCTYPE html>
<html>
<head>
    <title>Case {{ $case->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-white">
                📄 Case Details
                <a href="/broker/my-cases" class="btn btn-sm btn-outline-light ms-2">Back to Cases</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1>Case: {{ $case->id }}</h1>
                <p class="text-muted">Declaration Details</p>
            </div>
            <span class="badge rounded-pill bg-{{ 
                $case->status === 'new' ? 'primary' : 
                ($case->status === 'in_inspection' ? 'warning' : 'success') 
            }} fs-6 px-3 py-2">
                {{ $case->status }}
            </span>
        </div>
        
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
                                <td>{{ ucfirst($case->priority) }}</td>
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
                        <p>{{ $case->declarant->name ?? 'N/A' }}</p>
                        
                        @if($case->consignee)
                        <h6>Consignee:</h6>
                        <p>{{ $case->consignee->name }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Documents Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📎 Documents ({{ $case->documents->count() }})</h5>
                <a href="/broker/case/{{ $case->id }}/documents" class="btn btn-sm btn-outline-primary">Manage Documents</a>
            </div>
            <div class="card-body">
                @if($case->documents->count() > 0)
                    <div class="list-group">
                        @foreach($case->documents as $doc)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $doc->filename }}</strong><br>
                                    <small class="text-muted">{{ $doc->category }} • {{ $doc->mime_type }} • {{ $doc->pages }} pages</small>
                                </div>
                                <small>{{ $doc->created_at->format('Y-m-d') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No documents attached</p>
                @endif
            </div>
        </div>
        
        <!-- Inspections Section -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🔍 Inspections ({{ $case->inspections->count() }})</h5>
            </div>
            <div class="card-body">
                @if($case->inspections->count() > 0)
                    @foreach($case->inspections as $inspection)
                    <div class="border rounded p-3 mb-2">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ ucfirst($inspection->type) }} Inspection</strong><br>
                                <small class="text-muted">
                                    Result: <span class="badge bg-{{ $inspection->result === 'passed' ? 'success' : 'danger' }}">
                                        {{ $inspection->result ?? 'pending' }}
                                    </span>
                                </small>
                            </div>
                            <div class="text-end">
                                <small>Started: {{ $inspection->started_at ? $inspection->started_at->format('Y-m-d') : 'N/A' }}</small><br>
                                <small>Completed: {{ $inspection->completed_at ? $inspection->completed_at->format('Y-m-d') : 'Pending' }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted mb-0">No inspections yet</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>