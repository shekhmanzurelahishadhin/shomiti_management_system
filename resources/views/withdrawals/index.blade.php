@extends('layouts.app')
@section('title','উত্তোলন আবেদন')
@section('page-title','অর্থ উত্তোলন ব্যবস্থাপনা')
@section('content')

<div class="row g-3 mb-3">
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #f39c12">
      <div class="text-muted small">বিবেচনাধীন</div>
      <div class="fs-4 fw-bold text-warning">{{ $stats['pending'] }}</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #17a2b8">
      <div class="text-muted small">স্থগিত (অপেক্ষায়)</div>
      <div class="fs-4 fw-bold text-info">{{ $stats['on_hold'] }}</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #007bff">
      <div class="text-muted small">আংশিক পরিশোধ</div>
      <div class="fs-4 fw-bold text-primary">{{ $stats['partially_repaid'] }}</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #e74c3c">
      <div class="text-muted small">মোট বকেয়া পরিশোধ</div>
      <div class="fs-4 fw-bold text-danger">৳{{ number_format($stats['total_pending_amount'],0) }}</div>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-2 text-warning"></i>উত্তোলন আবেদন তালিকা</h5>
  <a href="{{ route('withdrawals.create') }}" class="btn btn-warning btn-sm">
    <i class="bi bi-plus me-1"></i>নতুন আবেদন
  </a>
</div>

<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="সদস্যের নাম বা আইডি..." value="{{ request('search') }}">
      </div>
      <div class="col-md-3">
        <select name="status" class="form-select form-select-sm">
          <option value="">সব অবস্থা</option>
          @foreach(['pending'=>'বিবেচনাধীন','on_hold'=>'স্থগিত','partially_repaid'=>'আংশিক পরিশোধ','repaid'=>'সম্পূর্ণ পরিশোধ','rejected'=>'প্রত্যাখ্যাত'] as $v=>$l)
            <option value="{{ $v }}" {{ request('status')==$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-search me-1"></i>খুঁজুন</button>
      </div>
      <div class="col-md-2">
        <a href="{{ route('withdrawals.index') }}" class="btn btn-outline-secondary btn-sm w-100">রিসেট</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>সদস্য</th><th>আবেদনের তারিখ</th><th>মোট টাকা</th><th>পরিশোধ হয়েছে</th><th>বাকি</th><th>নির্ধারিত তারিখ</th><th>অবস্থা</th><th class="text-end">অ্যাকশন</th></tr>
      </thead>
      <tbody>
      @forelse($requests as $req)
        <tr>
          <td class="text-muted small">{{ $req->id }}</td>
          <td>
            <div class="fw-semibold">{{ $req->member->name }}</div>
            <small class="text-muted">{{ $req->member->member_id }}</small>
          </td>
          <td>{{ $req->requested_date->format('d M Y') }}</td>
          <td class="fw-bold">৳{{ number_format($req->total_amount,2) }}</td>
          <td class="text-success">৳{{ number_format($req->repaid_amount,2) }}</td>
          <td class="{{ $req->remaining_amount > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
            ৳{{ number_format($req->remaining_amount,2) }}
          </td>
          <td>{{ $req->scheduled_repay_date ? $req->scheduled_repay_date->format('d M Y') : '—' }}</td>
          <td>
            <span class="badge bg-{{ $req->status_color }}">{{ $req->status_label }}</span>
          </td>
          <td class="text-end">
            <a href="{{ route('withdrawals.show', $req) }}" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-eye"></i>
            </a>
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="text-center text-muted py-4">কোনো আবেদন নেই।</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($requests->hasPages())
    <div class="card-footer">{{ $requests->links() }}</div>
  @endif
</div>
@endsection
