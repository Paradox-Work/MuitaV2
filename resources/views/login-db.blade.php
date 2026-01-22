<!DOCTYPE html>
<html>
<head>
    <title>Database Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h2 class="text-center mb-4">Database Login</h2>
                <p class="text-center text-muted">Using Spatie roles from database</p>
                
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                <form method="POST" action="/login-db">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login with Database</button>
                </form>
                
                <div class="mt-4">
                    <h6>Database Users (from seeder):</h6>
                    <small class="text-muted">
                        admin@system.com / admin123<br>
                        inspector@system.com / insp123<br>
                        broker@system.com / broker123<br>
                        analyst@system.com / analyst123
                    </small>
                </div>
                
                <div class="mt-3 text-center">
                    <a href="/login-demo" class="btn btn-outline-secondary btn-sm">Use Session Demo Instead</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>