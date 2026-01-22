<!DOCTYPE html>
<html>
<head>
    <title>Analyst Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container">
            <a class="navbar-brand" href="/">Customs System</a>
            <div class="navbar-text">
                📊 Analyst Reports
                <a href="/dashboard-demo" class="btn btn-sm btn-outline-light ms-2">Back to Dashboard</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h1>Analytics & Reports</h1>
        
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Cases</h5>
                        <p class="display-6">{{ $stats['total_cases'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pending Inspections</h5>
                        <p class="display-6">{{ $stats['pending_inspections'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">High Risk Cases</h5>
                        <p class="display-6 text-danger">{{ $stats['high_risk_cases'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Completed Today</h5>
                        <p class="display-6">{{ $stats['completed_today'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h3>Report Types</h3>
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action">
                    📅 Daily Inspection Report
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    📈 Monthly Performance Analytics
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    ⚠️ Risk Assessment Summary
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    🚨 Compliance Audit Report
                </a>
            </div>
        </div>
    </div>
</body>
</html>