@extends('layouts.app')
@section('title','Bill Detail')
@section('page-title','Bill Detail')
@section('content')
<div class="row g-3">
  <div class="col-lg-5">
    <div class="card">
      <div class="card-header py-3 d-flex justify-content-between">
        <span><i class="bi bi-receipt me-2 text-primary"></i>Bill #{{ $bill->id }}</span>
        <span class="badge badge-{{ $bill->status }} px-3">{{ ucfirst($bill->status) }}</span>
      </div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Member</span>
          <a href="{{ route('members.show',$bill->member) }}" class="fw-semibold text-decoration-none">
            {{ $bill->member->name }} <small class="text-muted">({{ $bill->member->member_id }})</small>
          </a>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Period</span>
          <strong>{{ $bill->month_name }} {{ $bill->bill_year }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Amount</span>
          <strong>৳{{ number_format($bill->amount,2) }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Fine</span>
          <strong class="{{ $bill->fine > 0 ? 'text-danger' : '' }}">
            {{ $bill->fine > 0 ? '৳'.number_format($bill->fine,2) : '—' }}
            @if($bill->fine_waived)<span class="badge bg-success ms-1">Waived</span>@endif
          </strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Discount</span>
          <strong class="text-success">{{ $bill->discount > 0 ? '৳'.number_format($bill->discount,2) : '—' }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Paid</span>
          <strong class="text-success">৳{{ number_format($bill->paid_amount,2) }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between bg-light">
          <span class="fw-bold">Total Due</span>
          <strong class="text-danger fs-5">৳{{ number_format($bill->total_due,2) }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Due Date</span>
          <span class="{{ $bill->due_date->isPast() && $bill->status!='paid' ? 'text-danger fw-semibold' : '' }}">
            {{ $bill->due_date->format('d M Y') }}
          </span>
        </li>
        @if($bill->notes)
        <li class="list-group-item"><span class="text-muted small">Notes:</span> {{ $bill->notes }}</li>
        @endif
      </ul>
      <div class="card-body d-flex gap-2">
        <a href="{{ route('bills.edit',$bill) }}" class="btn btn-warning btn-sm flex-fill">
          <i class="bi bi-pencil me-1"></i>Edit
        </a>
        @can('collect payments')
        @if($bill->status !== 'paid')
        <a href="{{ route('payments.create',['member_id'=>$bill->member_id,'bill_id'=>$bill->id]) }}"
           class="btn btn-success btn-sm flex-fill">
          <i class="bi bi-cash me-1"></i>Collect
        </a>
        @endif
        @endcan
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card">
      <div class="card-header py-3"><i class="bi bi-clock-history me-2 text-success"></i>Payment History</div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr><th>Date</th><th>Amount</th><th>Method</th><th>Ref</th><th>By</th><th></th></tr>
          </thead>
          <tbody>
          @forelse($bill->payments as $p)
            <tr>
              <td>{{ $p->payment_date->format('d M Y') }}</td>
              <td class="fw-bold text-success">৳{{ number_format($p->amount,2) }}</td>
              <td><span class="badge bg-secondary">{{ strtoupper($p->payment_method) }}</span></td>
              <td>{{ $p->reference ?? '—' }}</td>
              <td>{{ $p->collector->name ?? 'System' }}</td>
              <td><a href="{{ route('payments.receipt',$p) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-receipt"></i></a></td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No payments recorded.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
