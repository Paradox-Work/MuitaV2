<!DOCTYPE html>
<html>
<head>
    <title>Demo Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h2 class="text-center mb-4">Demo Login</h2>
                
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                <form method="POST" action="/login-demo">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                
                <div class="mt-4">
                    <h6>Demo Accounts:</h6>
                    <small class="text-muted">
                        admin@demo.com / admin123 (Admin)<br>
                        inspector@demo.com / insp123 (Inspector)<br>
                        broker@demo.com / broker123 (Broker)<br>
                        analyst@demo.com / analyst123 (Analyst)
                    </small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>