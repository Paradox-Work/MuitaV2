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
                    
                    <div class="mb-3">
                        <label class="form-label">Declarant (Your Company) *</label>
                        <select name="declarant_id" class="form-control" required>
                            <option value="">Select Declarant</option>
                            @foreach($parties as $party)
                            <option value="{{ $party->id }}">{{ $party->name }} ({{ $party->reg_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Consignee (Receiver)</label>
                        <select name="consignee_id" class="form-control">
                            <option value="">Select Consignee</option>
                            @foreach($parties as $party)
                            <option value="{{ $party->id }}">{{ $party->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Origin Country *</label>
                        <input type="text" name="origin_country" class="form-control" 
                               placeholder="LV" maxlength="2" required>
                        <small class="text-muted">2-letter country code</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Destination Country *</label>
                        <input type="text" name="destination_country" class="form-control" 
                               placeholder="LT" maxlength="2" required>
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