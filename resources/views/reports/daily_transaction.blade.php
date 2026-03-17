@extends('layouts.app')
@section('title','Daily Transaction')
@section('page-title','Daily Transaction Report')
@section('content')
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label small">Date</label>
        <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>View</button>
      </div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <div class="card text-center p-3" style="border-left:4px solid #27ae60">
      <div class="text-muted small">Income ({{ \Carbon\Carbon::parse($date)->format('d M Y') }})</div>
      <div class="fs-3 fw-bold text-success">৳{{ number_format($totalIn,2) }}</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-3" style="border-left:4px solid #e74c3c">
      <div class="text-muted small">Expenses</div>
      <div class="fs-3 fw-bold text-danger">৳{{ number_format($totalOut,2) }}</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-3" style="border-left:4px solid #2e86c1">
      <div class="text-muted small">Net</div>
      <div class="fs-3 fw-bold text-primary">৳{{ number_format($totalIn-$totalOut,2) }}</div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header py-3"><i class="bi bi-cash-coin me-2 text-success"></i>Payments ({{ $payments->count() }})</div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr><th>Member</th><th>Amount</th><th>Method</th><th>Reference</th></tr>
          </thead>
          <tbody>
          @forelse($payments as $p)
            <tr>
              <td>
                <div class="fw-semibold">{{ $p->member->name }}</div>
                <small class="text-muted">{{ $p->member->member_id }}</small>
              </td>
              <td class="fw-bold text-success">৳{{ number_format($p->amount,2) }}</td>
              <td><span class="badge bg-secondary">{{ strtoupper($p->payment_method) }}</span></td>
              <td>{{ $p->reference ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-3">No payments.</td></tr>
          @endforelse
          </tbody>
          @if($payments->count())
          <tfoot class="table-light fw-bold">
            <tr><td>Total</td><td class="text-success">৳{{ number_format($totalIn,2) }}</td><td colspan="2"></td></tr>
          </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card">
      <div class="card-header py-3"><i class="bi bi-wallet2 me-2 text-danger"></i>Expenses ({{ $expenses->count() }})</div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr><th>Title</th><th>Category</th><th>Amount</th></tr>
          </thead>
          <tbody>
          @forelse($expenses as $e)
            <tr>
              <td>{{ $e->title }}</td>
              <td><span class="badge bg-light text-dark">{{ ucfirst($e->category) }}</span></td>
              <td class="fw-bold text-danger">৳{{ number_format($e->amount,2) }}</td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center text-muted py-3">No expenses.</td></tr>
          @endforelse
          </tbody>
          @if($expenses->count())
          <tfoot class="table-light fw-bold">
            <tr><td colspan="2">Total</td><td class="text-danger">৳{{ number_format($totalOut,2) }}</td></tr>
          </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
