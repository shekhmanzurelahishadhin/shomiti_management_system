<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a5276 0%, #2e86c1 100%); min-height: 100vh; display: flex; align-items: center; }
        .auth-card { border: none; border-radius: 1rem; box-shadow: 0 20px 60px rgba(0,0,0,.25); }
        .auth-header { background: #1a5276; border-radius: 1rem 1rem 0 0; padding: 2rem; text-align: center; color: #fff; }
        .auth-header h4 { font-weight: 700; margin-bottom: .25rem; }
        .auth-header small { opacity: .7; }
        .auth-body { padding: 2rem; }
        .btn-primary { background: #1a5276; border-color: #1a5276; }
        .btn-primary:hover { background: #154360; border-color: #154360; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card auth-card">
                <div class="auth-header">
                    <i class="bi bi-bank fs-1 mb-2 d-block"></i>
                    <h4>নবদিগন্ত সমবায় সমিতি</h4>
                    <small>Nabadiganta Somobai Somiti</small>
                </div>
                <div class="auth-body">
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
