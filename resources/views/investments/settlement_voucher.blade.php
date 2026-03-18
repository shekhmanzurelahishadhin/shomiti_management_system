@extends('layouts.app')
@section('title','নিষ্পত্তি ভাউচার')
@section('page-title','বিনিয়োগ নিষ্পত্তি ভাউচার')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
  <div class="card-body p-4">
    <div class="text-center mb-3">
      <div class="fw-bold fs-5 text-dark">নবদিগন্ত সমবায় সমিতি</div>
      <div class="text-muted small">Nabadiganta Somobai Somiti</div>
      <hr>
      <h5 class="fw-bold">বিনিয়োগ নিষ্পত্তি ভাউচার</h5>
      <div class="text-muted small">ভাউচার নং: <strong>{{ $settlement->voucher_number }}</strong></div>
    </div>
    <table class="table table-sm table-borderless">
      <tr><td class="text-muted">সদস্য</td><td class="fw-semibold text-end">{{ $settlement->member->name }}</td></tr>
      <tr><td class="text-muted">সদস্য আইডি</td><td class="text-end">{{ $settlement->member->member_id }}</td></tr>
      <tr><td class="text-muted">প্রকল্প</td><td class="fw-semibold text-end">{{ $settlement->investmentRequest->project_name }}</td></tr>
      <tr><td class="text-muted">বিনিয়োগ পরিমাণ</td><td class="text-end">৳{{ number_format($settlement->investment_amount,2) }}</td></tr>
      <tr><td class="text-muted">প্রকৃত {{ $settlement->outcome_label }}</td>
          <td class="text-end fw-bold {{ $settlement->actual_profit_loss>=0?'text-success':'text-danger' }}">
            {{ $settlement->actual_profit_loss>=0?'+':'' }}৳{{ number_format($settlement->actual_profit_loss,2) }}
          </td>
      </tr>
      <tr class="border-top bg-light">
        <td class="fw-bold fs-5 pt-3">মোট রিটার্ন পরিমাণ</td>
        <td class="fw-bold fs-3 text-primary text-end pt-3">৳{{ number_format($settlement->return_amount,2) }}</td>
      </tr>
      <tr><td class="text-muted">পেমেন্ট পদ্ধতি</td><td class="text-end"><span class="badge bg-secondary">{{ strtoupper($settlement->payment_method) }}</span></td></tr>
      @if($settlement->reference)<tr><td class="text-muted">রেফারেন্স</td><td class="text-end">{{ $settlement->reference }}</td></tr>@endif
      @if($settlement->bank_name)<tr><td class="text-muted">ব্যাংক</td><td class="text-end">{{ $settlement->bank_name }}</td></tr>@endif
      <tr><td class="text-muted">নিষ্পত্তির তারিখ</td><td class="text-end">{{ $settlement->settlement_date->format('d M Y') }}</td></tr>
      <tr><td class="text-muted">সম্পন্নকারী</td><td class="text-end">{{ $settlement->settledBy->name ?? 'System' }}</td></tr>
    </table>
    <div class="text-center mt-3">
      <span class="badge bg-{{ $settlement->outcome==='profit'?'success':($settlement->outcome==='loss'?'danger':'secondary') }} px-4 py-2 fs-6">
        {{ $settlement->outcome_label }} — বিনিয়োগ নিষ্পন্ন
      </span>
    </div>
    <div class="text-center text-muted small mt-4 border-top pt-3">তৈরি: {{ now()->format('d M Y H:i') }}</div>
  </div>
  <div class="card-footer d-flex gap-2 justify-content-center">
    <a href="{{ route('investments.settlement.voucher.pdf', $settlement) }}" class="btn btn-danger btn-sm">
      <i class="bi bi-file-pdf me-1"></i>PDF ডাউনলোড
    </a>
    <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
      <i class="bi bi-printer me-1"></i>প্রিন্ট
    </button>
    <a href="{{ route('investments.show', $settlement->investmentRequest) }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>ফিরে যান
    </a>
  </div>
</div>
</div>
</div>
@endsection
