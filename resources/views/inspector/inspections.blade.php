<div class="container mt-4">
    <h1>🔍 Assigned Inspections</h1>
    
    @if($cases->count() > 0)
        @foreach($cases as $case)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Case: {{ $case->id }}</h5>
                <p><strong>Vehicle:</strong> {{ $case->vehicle->plate_no ?? 'N/A' }}</p>
                <p><strong>Documents:</strong> {{ $case->documents->count() }} files</p>
                <p><strong>Status:</strong> <span class="badge bg-warning">{{ $case->status }}</span></p>
                
                <a href="#" class="btn btn-primary btn-sm">Start Inspection</a>
                <a href="#" class="btn btn-outline-secondary btn-sm">View Details</a>
            </div>
        </div>
        @endforeach
    @else
        <div class="alert alert-info">
            No inspections assigned at the moment.
        </div>
    @endif
</div>