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
                                    @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            <form method="POST" action="/broker/document/{{ $doc->id }}" class="d-inline" 
                            onsubmit="return confirm('Delete this document?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
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
    
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="/broker/case/{{ $case->id }}/documents">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Document Name</label>
                        <input type="text" name="filename" class="form-control" 
                               placeholder="invoice.pdf" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-control">
                            <option value="invoice">Invoice</option>
                            <option value="waybill">Waybill</option>
                            <option value="certificate">Certificate</option>
                            <option value="license">License</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Document Type</label>
                        <select name="mime_type" class="form-control">
                            <option value="application/pdf">PDF</option>
                            <option value="image/jpeg">JPEG Image</option>
                            <option value="image/png">PNG Image</option>
                            <option value="application/msword">Word Document</option>
                            <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document">Word (DOCX)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pages</label>
                        <input type="number" name="pages" class="form-control" value="1" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Document</button>
                </div>
            </div>
        </form>
    </div>
</div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>