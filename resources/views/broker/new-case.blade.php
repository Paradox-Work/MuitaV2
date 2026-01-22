<!DOCTYPE html>
<html>
<head>
    <title>New Declaration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text">
                📄 New Declaration
                <a href="/broker/my-cases" class="btn btn-sm btn-outline-light ms-2">Back to My Cases</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h1>New Customs Declaration</h1>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <form method="POST" action="/broker/new-case">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Vehicle *</label>
                        <select name="vehicle_id" class="form-control" required>
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">
                                {{ $vehicle->plate_no }} ({{ $vehicle->make }} {{ $vehicle->model }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Replace the Declarant and Consignee selects with: -->

                    <div class="mb-3">
                        <label class="form-label">Declarant (Your Company) *</label>
                        <div class="input-group">
                            <select name="declarant_id" class="form-control" required id="declarantSelect">
                                <option value="">Select Declarant</option>
                                @foreach($parties as $party)
                                <option value="{{ $party->id }}">
                                    {{ $party->name }} - {{ $party->type }} ({{ $party->country }})
                                </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#newPartyModal">
                                +
                            </button>
                        </div>
                        <small class="text-muted">Select existing party or create new</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Consignee (Receiver)</label>
                        <select name="consignee_id" class="form-control" id="consigneeSelect">
                            <option value="">Select Consignee</option>
                            @foreach($parties as $party)
                            <option value="{{ $party->id }}">
                                {{ $party->name }} - {{ $party->type }} ({{ $party->country }})
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Optional - who receives the shipment</small>
                    </div>
                
                <div class="col-md-6">
                   <div class="mb-3">
    <label class="form-label">Origin Country *</label>
    <select name="origin_country" class="form-control" required>
        <option value="">Select Country</option>
        <option value="LV">Latvia (LV)</option>
        <option value="LT">Lithuania (LT)</option>
        <option value="EE">Estonia (EE)</option>
        <option value="PL">Poland (PL)</option>
        <option value="DE">Germany (DE)</option>
        <option value="RU">Russia (RU)</option>
        <option value="BY">Belarus (BY)</option>
        <option value="FI">Finland (FI)</option>
        <option value="SE">Sweden (SE)</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Destination Country *</label>
    <select name="destination_country" class="form-control" required>
        <option value="">Select Country</option>
        <option value="LV">Latvia (LV)</option>
        <option value="LT">Lithuania (LT)</option>
        <option value="EE">Estonia (EE)</option>
        <option value="PL">Poland (PL)</option>
        <option value="DE">Germany (DE)</option>
        <option value="RU">Russia (RU)</option>
        <option value="BY">Belarus (BY)</option>
        <option value="FI">Finland (FI)</option>
        <option value="SE">Sweden (SE)</option>
    </select>
</div>
                    
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-control">
                            <option value="normal">Normal</option>
                            <option value="high">High Priority</option>
                            <option value="low">Low Priority</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-success btn-lg">
                    📤 Submit Declaration
                </button>
                <a href="/broker/my-cases" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
        
        <div class="mt-4 alert alert-info">
            <h5>Note:</h5>
            <p>After submission, the case will be reviewed by customs inspectors. 
               You can track its status in "My Cases".</p>
        </div>
    </div>
</body>
</html>