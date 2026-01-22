<!DOCTYPE html>
<html>
<head>
    <title>Broker - My Cases</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .case-table th {
            background-color: #f8f9fa;
        }
        .case-id {
            font-family: monospace;
            font-weight: bold;
        }
        .search-highlight {
            background-color: #fff3cd;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text text-white">
                📄 Broker Portal
                <a href="/dashboard-demo" class="btn btn-sm btn-outline-light ms-2">Dashboard</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>My Cases</h1>
                <p class="text-muted mb-0">Track your customs declarations</p>
            </div>
            <a href="/broker/new-case" class="btn btn-success">
                ➕ New Declaration
            </a>
        </div>
        
        <!-- Search Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="/broker/my-cases" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by Case ID, Vehicle, Declarant..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="in_inspection" {{ request('status') == 'in_inspection' ? 'selected' : '' }}>In Inspection</option>
                            <option value="released" {{ request('status') == 'released' ? 'selected' : '' }}>Released</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="sort" class="form-control">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="id" {{ request('sort') == 'id' ? 'selected' : '' }}>By Case ID</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="/broker/my-cases" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
                
                <!-- Quick Stats -->
                <div class="row mt-3 g-2">
                    <div class="col-auto">
                        <span class="badge bg-primary">New: {{ $stats['new'] ?? 0 }}</span>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-warning">Inspecting: {{ $stats['in_inspection'] ?? 0 }}</span>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-success">Released: {{ $stats['released'] ?? 0 }}</span>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-info">Total: {{ $stats['total'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        @if($cases->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover case-table">
                    <thead class="table-light">
                        <tr>
                            <th>Case ID</th>
                            <th>Vehicle</th>
                            <th>Declarant</th>
                            <th>Status</th>
                            <th>Documents</th>
                            <th>Arrival</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cases as $case)
                        <tr>
                            <td class="case-id">
                                @if(request('search') && str_contains(strtolower($case->id), strtolower(request('search'))))
                                    {!! str_replace(request('search'), '<span class="search-highlight">'.request('search').'</span>', $case->id) !!}
                                @else
                                    {{ $case->id }}
                                @endif
                            </td>
                            <td>
                                @if($case->vehicle)
                                    @if(request('search') && str_contains(strtolower($case->vehicle->plate_no), strtolower(request('search'))))
                                        <span class="search-highlight">{{ $case->vehicle->plate_no }}</span>
                                    @else
                                        {{ $case->vehicle->plate_no }}
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $case->vehicle->make }} {{ $case->vehicle->model }}</small>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($case->declarant)
                                    @if(request('search') && str_contains(strtolower($case->declarant->name), strtolower(request('search'))))
                                        <span class="search-highlight">{{ $case->declarant->name }}</span>
                                    @else
                                        {{ $case->declarant->name }}
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-{{ 
                                    $case->status === 'new' ? 'primary' : 
                                    ($case->status === 'in_inspection' ? 'warning' : 
                                    ($case->status === 'released' ? 'success' : 'secondary')) 
                                }}">
                                    {{ $case->status }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $docCount = $case->documents->count();
                                @endphp
                                @if($docCount > 0)
                                    <span class="badge bg-info">{{ $docCount }} files</span>
                                @else
                                    <span class="badge bg-secondary">0 files</span>
                                @endif
                            </td>
                            <td>
                                {{ $case->arrival_ts ? $case->arrival_ts->format('Y-m-d') : 'N/A' }}<br>
                                <small class="text-muted">{{ $case->arrival_ts ? $case->arrival_ts->format('H:i') : '' }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/broker/case/{{ $case->id }}" class="btn btn-outline-primary">View</a>
                                    <a href="/broker/case/{{ $case->id }}/documents" class="btn btn-outline-secondary">Docs</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $cases->count() }} of {{ $totalCases ?? $cases->count() }} case(s)
                    @if(request('search') || request('status'))
                        <span class="ms-2">(filtered)</span>
                    @endif
                </div>
                
                @if($cases->hasPages())
                <div>
                    {{ $cases->links() }}
                </div>
                @endif
            </div>
        @else
            <div class="alert alert-info">
                <h5>No cases found</h5>
                @if(request('search') || request('status'))
                    <p>No cases match your search criteria. <a href="/broker/my-cases">Clear filters</a></p>
                @else
                    <p>You haven't submitted any customs declarations yet.</p>
                    <a href="/broker/new-case" class="btn btn-info">Submit your first declaration</a>
                @endif
            </div>
        @endif
    </div>
    
    <script>
        // Quick search highlight on page load
        document.addEventListener('DOMContentLoaded', function() {
            const searchParam = new URLSearchParams(window.location.search).get('search');
            if (searchParam) {
                // Highlight in page title
                document.title = document.title.replace(
                    searchParam, 
                    '<span class="search-highlight">' + searchParam + '</span>'
                );
            }
        });
    </script>
</body>
</html>