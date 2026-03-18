@extends('layouts.app')
@section('title','পেমেন্ট বিস্তারিত')
@section('page-title','পেমেন্ট বিস্তারিত')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <span class="fw-bold"><i class="bi bi-cash-coin me-2 text-success"></i>পেমেন্ট #{{ $payment->id }}</span>
    <span class="badge bg-success">পরিশোধ</span>
  </div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item d-flex justify-content-between">
      <span class="text-muted">সদস্য</span>
      <a href="{{ route('members.show', $payment->member) }}" class="fw-semibold text-decoration-none">
        {{ $payment->member->name }} ({{ $payment->member->member_id }})
      </a>
    </li>
    <li class="list-group-item d-flex justify-content-between">
      <span class="text-muted">বিল মাস</span>
      <strong>{{ $payment->bill ? $payment->bill->month_name.' '.$payment->bill->bill_year : '—' }}</strong>
    </li>
    <li class="list-group-item d-flex justify-content-between">
      <span class="text-muted">পরিমাণ</span>
      <strong class="text-success fs-5">৳{{ number_format($payment->amount, 2) }}</strong>
    </li>
    <li class="list-group-item d-flex justify-content-between">
      <span class="text-muted">পেমেন্ট পদ্ধতি</span>
      <span class="badge bg-secondary">{{ strtoupper($payment->payment_method) }}</span>
    </li>
    @if($payment->reference)
    <li class="list-group-item d-flex justify-content-between">
      <span class="text-muted">রেফারেন্স</span>
      <strong>{{ $payment->reference }}</strong>
    </li>
    @endif
    <li class="list-group-item d-flex justify-content-between">
      <span class="text-muted">তারিখ</span>
      <strong>{{ $payment->payment_date->format('d M Y') }}</strong>
    </li>
    <li class="list-group-item d-flex justify-content-between">
      <span class="text-muted">সংগ্রহকারী</span>
      <strong>{{ $payment->collector->name ?? 'System' }}</strong>
    </li>
    @if($payment->notes)
    <li class="list-group-item">
      <span class="text-muted small">নোট:</span> {{ $payment->notes }}
    </li>
    @endif
  </ul>
  <div class="card-footer d-flex gap-2">
    <a href="{{ route('payments.receipt', $payment) }}" class="btn btn-success btn-sm flex-fill">
      <i class="bi bi-receipt me-1"></i>রসিদ দেখুন
    </a>
    <a href="{{ route('payments.receipt-pdf', $payment) }}" class="btn btn-outline-danger btn-sm">
      <i class="bi bi-file-pdf me-1"></i>PDF
    </a>
    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>ফিরে যান
    </a>
  </div>
</div>
</div>
</div>
@endsection
