<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --sidebar-width: 260px; --primary: #1a3a1c; --accent: #2d6a30; }
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            width: var(--sidebar-width); min-height: 100vh;
            background: linear-gradient(180deg,#0d1f0f 0%,#1a3a1c 100%); position: fixed; top: 0; left: 0;
            z-index: 1000; transition: transform .3s; overflow-y: auto;
        }
        .sidebar-brand { padding: 1rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,.1); }
        .sidebar-brand h5 { color: #fff; font-weight: 700; margin: 0; font-size: .95rem; }
        .sidebar-brand small { color: rgba(255,255,255,.6); font-size: .75rem; }
        .sidebar .nav-link {
            color: rgba(255,255,255,.8); padding: .55rem 1.5rem;
            border-radius: 0; font-size: .875rem; display: flex; align-items: center; gap: .6rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,.12); color: #fff;
        }
        .sidebar .nav-section { color: rgba(255,255,255,.4); font-size: .7rem; text-transform: uppercase;
            letter-spacing: .08em; padding: 1rem 1.5rem .3rem; font-weight: 600; }
        .sidebar .nav-link i { width: 18px; text-align: center; font-size: 1rem; }
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .topbar {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.5rem; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 100;
        }
        .page-content { padding: 1.5rem; }
        .card { border: none; border-radius: .75rem; box-shadow: 0 1px 6px rgba(0,0,0,.07); }
        .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; font-weight: 600; }
        .stat-card { border-radius: .75rem; color: #fff; padding: 1.25rem 1.5rem; }
        .badge-pending  { background: #fff3cd; color: #856404; }
        .badge-paid     { background: #d1e7dd; color: #0a3622; }
        .badge-overdue  { background: #f8d7da; color: #58151c; }
        .badge-partial  { background: #cfe2ff; color: #084298; }
        .badge-active   { background: #d1e7dd; color: #0a3622; }
        .badge-inactive { background: #f8d7da; color: #58151c; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('images/logo.jpg') }}" alt="নবদিগন্ত"
                 style="width:42px;height:42px;object-fit:contain;filter:drop-shadow(0 1px 4px rgba(0,0,0,.3))">
            <div>
                <div style="color:#fff;font-weight:700;font-size:.92rem;line-height:1.2">নবদিগন্ত</div>
                <div style="color:rgba(255,255,255,.55);font-size:.7rem">সমবায় সমিতি</div>
            </div>
        </div>
    </div>
    <nav class="mt-2">
        <span class="nav-section">Main</span>
        <a href="{{ route('dashboard') }}" class="nav-link @active('dashboard')"><i class="bi bi-speedometer2"></i> Dashboard</a>

        @if(!auth()->user()->isMemberRole())
        @can('manage members')
        <span class="nav-section">Members</span>
        <a href="{{ route('members.index') }}" class="nav-link @active('members.*')"><i class="bi bi-people"></i> Members</a>
        @endcan
        @else
        <span class="nav-section">আমার তথ্য</span>
        @if(auth()->user()->member)
        <a href="{{ route('members.show', auth()->user()->member) }}" class="nav-link @active('members.*')">
          <i class="bi bi-person-circle"></i> আমার প্রোফাইল
        </a>
        @endif
        @endif

        @can('generate bills')
        <span class="nav-section">Finance</span>
        <a href="{{ route('bills.index') }}" class="nav-link @active('bills.*')"><i class="bi bi-receipt"></i> Bills</a>
        @endcan

        @can('collect payments')
        <a href="{{ route('payments.index') }}" class="nav-link @active('payments.*')"><i class="bi bi-cash-coin"></i> Payments</a>
        @endcan

        @can('manage committees')
        <span class="nav-section">Groups</span>
        <a href="{{ route('committees.index') }}" class="nav-link @active('committees.*')"><i class="bi bi-diagram-3"></i> Committees</a>
        @endcan

        @can('manage expenses')
        <a href="{{ route('expenses.index') }}" class="nav-link @active('expenses.*')"><i class="bi bi-wallet2"></i> Expenses</a>
        @endcan

        @can('collect payments')
        <a href="{{ route('withdrawals.index') }}" class="nav-link @active('withdrawals.*')"><i class="bi bi-cash-stack"></i> উত্তোলন</a>
        @endcan

        <span class="nav-section">নির্বাচন</span>
        <a href="{{ route('elections.index') }}" class="nav-link @active('elections.*')"><i class="bi bi-person-badge"></i> কমিটি নির্বাচন</a>

        @can('view investments')
        <span class="nav-section">বিনিয়োগ</span>
        <a href="{{ route('investments.index') }}" class="nav-link @active('investments.*')">
          <i class="bi bi-graph-up-arrow"></i> বিনিয়োগ
        </a>
        @can('manage investment agenda')
        <a href="{{ route('investments.meeting.list') }}" class="nav-link @active('investments.meetings*')">
          <i class="bi bi-calendar-event"></i> সভার এজেন্ডা
        </a>
        @endcan
        @endcan

        @can('view reports')
        <span class="nav-section">Analytics</span>
        <a href="{{ route('reports.index') }}" class="nav-link @active('reports.*')"><i class="bi bi-bar-chart-line"></i> Reports</a>
        @endcan

        @can('manage users')
        <span class="nav-section">Admin</span>
        <a href="{{ route('users.index') }}" class="nav-link @active('users.*')"><i class="bi bi-person-gear"></i> Users</a>
        @endcan

        @can('manage settings')
        <a href="{{ route('settings.index') }}" class="nav-link @active('settings.*')"><i class="bi bi-gear"></i> Settings</a>
        @endcan

        @role('Super Admin|Admin')
        <a href="{{ route('activity-logs.index') }}" class="nav-link @active('activity-logs.*')"><i class="bi bi-clock-history"></i> Activity Log</a>
        @endrole
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h6 class="mb-0 text-muted fw-normal">@yield('page-title', 'Dashboard')</h6>
        </div>
        <div class="dropdown">
            <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
