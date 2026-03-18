@extends('layouts.app')
@section('title','পেমেন্ট ভাউচার')
@section('page-title','বিনিয়োগ পেমেন্ট ভাউচার')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
  <div class="card-body p-4" id="voucherBody">
    <div class="text-center mb-3">
      <div class="fw-bold fs-5 text-success">নবদিগন্ত সমবায় সমিতি</div>
      <div class="text-muted small">Nabadiganta Somobai Somiti</div>
      <hr>
      <h5 class="fw-bold">বিনিয়োগ পেমেন্ট ভাউচার</h5>
      <div class="text-muted small">ভাউচার নং: <strong>{{ $payment->voucher_number }}</strong></div>
    </div>
    <table class="table table-sm table-borderless">
      <tr><td class="text-muted">সদস্য</td><td class="fw-semibold text-end">{{ $payment->member->name }}</td></tr>
      <tr><td class="text-muted">সদস্য আইডি</td><td class="text-end">{{ $payment->member->member_id }}</td></tr>
      <tr><td class="text-muted">প্রকল্প</td><td class="fw-semibold text-end">{{ $payment->investmentRequest->project_name }}</td></tr>
      <tr><td class="text-muted">মেয়াদ</td><td class="text-end">{{ $payment->investmentRequest->approved_duration_months }} মাস</td></tr>
      <tr><td class="text-muted">লাভের হার</td><td class="text-end">{{ $payment->investmentRequest->approved_profit_ratio }}%</td></tr>
      <tr><td class="text-muted">রিটার্ন তারিখ</td><td class="text-end">{{ $payment->investmentRequest->approved_return_date?->format('d M Y') ?? '—' }}</td></tr>
      <tr><td class="text-muted">পেমেন্ট পদ্ধতি</td><td class="text-end"><span class="badge bg-secondary">{{ strtoupper($payment->payment_method) }}</span></td></tr>
      @if($payment->reference)<tr><td class="text-muted">রেফারেন্স</td><td class="text-end">{{ $payment->reference }}</td></tr>@endif
      @if($payment->bank_name)<tr><td class="text-muted">ব্যাংক</td><td class="text-end">{{ $payment->bank_name }}</td></tr>@endif
      <tr><td class="text-muted">তারিখ</td><td class="text-end">{{ $payment->payment_date->format('d M Y') }}</td></tr>
      <tr><td class="text-muted">পরিশোধকারী</td><td class="text-end">{{ $payment->paidBy->name ?? 'System' }}</td></tr>
      <tr class="border-top">
        <td class="fw-bold fs-5 pt-3">বিতরণকৃত পরিমাণ</td>
        <td class="fw-bold fs-3 text-success text-end pt-3">৳{{ number_format($payment->amount,2) }}</td>
      </tr>
    </table>
    <div class="text-center mt-3">
      <span class="badge bg-success px-4 py-2 fs-6">বিনিয়োগ সক্রিয়</span>
    </div>
    <div class="text-center text-muted small mt-4 border-top pt-3">
      তৈরি: {{ now()->format('d M Y H:i') }}
    </div>
  </div>
  <div class="card-footer d-flex gap-2 justify-content-center">
    <a href="{{ route('investments.voucher.pdf', $payment) }}" class="btn btn-danger btn-sm">
      <i class="bi bi-file-pdf me-1"></i>PDF ডাউনলোড
    </a>
    <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
      <i class="bi bi-printer me-1"></i>প্রিন্ট
    </button>
    <a href="{{ route('investments.show', $payment->investmentRequest) }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>ফিরে যান
    </a>
  </div>
</div>
</div>
</div>
@endsection
