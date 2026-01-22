<!DOCTYPE html>
<html>
<head>
    <title>Admin - All Cases</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text">
                Admin View
                <a href="/dashboard-demo" class="btn btn-sm btn-outline-light ms-2">Back to Dashboard</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h1>All Cases (Admin View)</h1>
        
        @if($cases->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vehicle</th>
                        <th>Declarant</th>
                        <th>Status</th>
                        <th>Arrival</th>
                        <th>Inspections</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cases as $case)
                    <tr>
                        <td>{{ $case->id }}</td>
                        <td>{{ $case->vehicle->plate_no ?? 'N/A' }}</td>
                        <td>{{ $case->declarant->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ 
                                $case->status === 'new' ? 'primary' : 
                                ($case->status === 'in_inspection' ? 'warning' : 'success') 
                            }}">
                                {{ $case->status }}
                            </span>
                        </td>
                        <td>{{ $case->arrival_ts ? $case->arrival_ts->format('Y-m-d H:i') : 'N/A' }}</td>
                        <td>{{ $case->inspections->count() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-info">
                No cases found in the database.
            </div>
        @endif
    </div>
</body>
</html>