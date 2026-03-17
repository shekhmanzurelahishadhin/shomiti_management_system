@extends('layouts.app')
@section('title','Activity Log')
@section('page-title','Activity Log')
@section('content')
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <select name="action" class="form-select form-select-sm">
          <option value="">All Actions</option>
          @foreach($actions as $action)
            <option value="{{ $action }}" {{ request('action')==$action?'selected':'' }}>{{ ucfirst($action) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
      </div>
      <div class="col-md-2">
        <input type="date" name="date_to"   class="form-control form-control-sm" value="{{ request('date_to') }}">
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Filter</button>
      </div>
      <div class="col-md-2">
        <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header py-3 d-flex justify-content-between">
    <span><i class="bi bi-clock-history me-2 text-primary"></i>Activity Log</span>
    <span class="badge bg-secondary">{{ $logs->total() }} records</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Time</th><th>User</th><th>Action</th><th>Description</th><th>Model</th><th>IP</th></tr>
      </thead>
      <tbody>
      @forelse($logs as $log)
        <tr>
          <td class="text-muted small text-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
          <td>{{ $log->user->name ?? '<em class="text-muted">System</em>' }}</td>
          <td>
            @php
              $colors = ['create'=>'success','update'=>'warning','delete'=>'danger','generate'=>'info','fine'=>'dark','login'=>'primary'];
              $color  = $colors[$log->action] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $color }}">{{ ucfirst($log->action) }}</span>
          </td>
          <td>{{ $log->description }}</td>
          <td class="text-muted small">
            {{ $log->model_type ? class_basename($log->model_type).' #'.$log->model_id : '—' }}
          </td>
          <td class="text-muted small">{{ $log->ip_address }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-4">No activity logs found.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($logs->hasPages())
    <div class="card-footer">{{ $logs->links() }}</div>
  @endif
</div>
@endsection
