<!DOCTYPE html>
<html>
<head>
    <title>Documents - {{ $case->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-white">
                📎 Case Documents
                <a href="/broker/case/{{ $case->id }}" class="btn btn-sm btn-outline-light ms-2">Back to Case</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Documents for Case: {{ $case->id }}</h1>
                <p class="text-muted">Upload and manage declaration documents</p>
            </div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
                📤 Upload Document
            </button>
        </div>
        
        @if($case->documents->count() > 0)
            <div class="list-group">
                @foreach($case->documents as $doc)
                <div class="list-group-item list-group-item-action">
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
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary">View</button>
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-3">
                <small class="text-muted">Total: {{ $case->documents->count() }} documents</small>
            </div>
        @else
            <div class="alert alert-info">
                <h5>No documents uploaded</h5>
                <p>Upload supporting documents for this declaration.</p>
            </div>
        @endif
    </div>
    
    <!-- Upload Modal (static for now) -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Document upload functionality would be implemented here.</p>
                    <p>For demo purposes, this shows where file uploads would go.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>