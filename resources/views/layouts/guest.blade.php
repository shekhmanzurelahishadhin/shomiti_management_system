<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') — নবদিগন্ত সমবায় সমিতি</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d1f0f 0%, #1a3a1c 50%, #0d1f0f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .auth-card { border: none; border-radius: 1rem; box-shadow: 0 20px 60px rgba(0,0,0,.5); overflow: hidden; }
        .auth-header {
            background: #0d1f0f;
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            color: #fff;
            border-bottom: 2px solid #2d6a30;
        }
        .auth-header .logo-img {
            width: 90px;
            height: 90px;
            object-fit: contain;
            margin-bottom: .75rem;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,.4));
        }
        .auth-header h4 { font-weight: 700; margin-bottom: .2rem; font-size: 1.15rem; color: #fff; }
        .auth-header .tagline { color: #6abf6e; font-size: .78rem; letter-spacing: .05em; }
        .auth-header .sub { color: rgba(255,255,255,.55); font-size: .78rem; }
        .auth-body { padding: 1.8rem 2rem; background: #fff; }
        .btn-primary { background: #2d6a30; border-color: #2d6a30; }
        .btn-primary:hover { background: #1e4d21; border-color: #1e4d21; }
        .form-control:focus { border-color: #2d6a30; box-shadow: 0 0 0 .2rem rgba(45,106,48,.2); }
        a { color: #2d6a30; }
        a:hover { color: #1e4d21; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card auth-card">
                <div class="auth-header">
                    <img src="{{ asset('images/logo.jpg') }}" alt="নবদিগন্ত" class="logo-img">
                    <h4>নবদিগন্ত সমবায় সমিতি</h4>
                    <div class="tagline">একসাথে দিগন্তে</div>
                    <div class="sub">Nabadiganta Somobai Somiti</div>
                </div>
                <div class="auth-body">
                    @if(session('status'))
                        <div class="alert alert-success py-2">{{ session('status') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
            <div class="text-center mt-3" style="color:rgba(255,255,255,.35);font-size:.75rem">
                &copy; {{ date('Y') }} নবদিগন্ত সমবায় সমিতি
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
