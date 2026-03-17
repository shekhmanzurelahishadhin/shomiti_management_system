@extends('layouts.app')
@section('title','Payments')
@section('page-title','Payment Ledger')
@section('content')
<div class="row g-3 mb-3">
  <div class="col-6 col-md-3">
    <div class="card text-center p-3">
      <div class="text-muted small">Today's Collection</div>
      <div class="fs-4 fw-bold text-success">৳{{ number_format($todayTotal,2) }}</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center p-3">
      <div class="text-muted small">This Month</div>
      <div class="fs-4 fw-bold text-primary">৳{{ number_format($monthTotal,2) }}</div>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-cash-coin me-2 text-success"></i>Payments</h5>
    <a href="{{ route('payments.create') }}" class="btn btn-success btn-sm">
        <i class="bi bi-plus me-1"></i>Collect Payment
    </a>
</div>

<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="Member name or ID..." value="{{ request('search') }}"></div>
      <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="From"></div>
      <div class="col-md-2"><input type="date" name="date_to"   class="form-control form-control-sm" value="{{ request('date_to') }}"   placeholder="To"></div>
      <div class="col-md-2">
        <select name="method" class="form-select form-select-sm">
          <option value="">All Methods</option>
          @foreach(['cash','bank','bkash','nagad','other'] as $m)
            <option value="{{ $m }}" {{ request('method')==$m?'selected':'' }}>{{ strtoupper($m) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-1"><button class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-search"></i></button></div>
      <div class="col-md-2"><a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm w-100">Clear</a></div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>Member</th><th>Bill Period</th><th>Amount</th><th>Method</th><th>Reference</th><th>Date</th><th>Collected By</th><th class="text-end">Actions</th></tr>
      </thead>
      <tbody>
      @forelse($payments as $p)
        <tr>
          <td class="text-muted small">{{ $p->id }}</td>
          <td>
            <div class="fw-semibold">{{ $p->member->name ?? '—' }}</div>
            <small class="text-muted">{{ $p->member->member_id ?? '' }}</small>
          </td>
          <td>{{ $p->bill ? $p->bill->month_name.' '.$p->bill->bill_year : '—' }}</td>
          <td class="fw-bold text-success">৳{{ number_format($p->amount,2) }}</td>
          <td><span class="badge bg-secondary">{{ strtoupper($p->payment_method) }}</span></td>
          <td>{{ $p->reference ?? '—' }}</td>
          <td>{{ $p->payment_date->format('d M Y') }}</td>
          <td>{{ $p->collector->name ?? 'System' }}</td>
          <td class="text-end">
            <a href="{{ route('payments.receipt',$p) }}" class="btn btn-sm btn-outline-success" title="Receipt"><i class="bi bi-receipt"></i></a>
            <a href="{{ route('payments.receipt-pdf',$p) }}" class="btn btn-sm btn-outline-danger" title="PDF"><i class="bi bi-file-pdf"></i></a>
            <form method="POST" action="{{ route('payments.destroy',$p) }}" class="d-inline"
                  onsubmit="return confirm('Delete this payment?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="text-center text-muted py-4">No payments found.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($payments->hasPages())
    <div class="card-footer">{{ $payments->links() }}</div>
  @endif
</div>
@endsection
