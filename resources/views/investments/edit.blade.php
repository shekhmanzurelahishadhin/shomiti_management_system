@extends('layouts.app')
@section('title','বিনিয়োগ সম্পাদনা')
@section('page-title','বিনিয়োগ আবেদন সম্পাদনা')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
  <div class="card-header py-3" style="background:linear-gradient(135deg,#7d6608,#d4ac0d);color:#fff">
    <i class="bi bi-pencil me-2 fs-5"></i>আবেদন সম্পাদনা — {{ $investment->project_name }}
    @if($investment->modification_note)
    <div class="mt-1 p-2 rounded" style="background:rgba(0,0,0,.2);font-size:.82rem">
      <i class="bi bi-exclamation-triangle me-1"></i>সংশোধনের নির্দেশনা: {{ $investment->modification_note }}
    </div>
    @endif
  </div>
  <div class="card-body p-4">
  <form method="POST" action="{{ route('investments.update', $investment) }}">
  @csrf @method('PUT')

  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <label class="form-label fw-semibold">সদস্য</label>
      <input type="text" class="form-control" value="{{ $investment->member->name }} ({{ $investment->member->member_id }})" readonly>
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">আবেদনের তারিখ *</label>
      <input type="date" name="submitted_date" class="form-control"
             value="{{ old('submitted_date', $investment->submitted_date->format('Y-m-d')) }}" required>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label fw-semibold">প্রকল্পের নাম *</label>
    <input type="text" name="project_name" class="form-control"
           value="{{ old('project_name', $investment->project_name) }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label fw-semibold">প্রকল্পের বিবরণ</label>
    <textarea name="project_description" class="form-control" rows="4">{{ old('project_description', $investment->project_description) }}</textarea>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <label class="form-label fw-semibold">বিনিয়োগের পরিমাণ (৳) *</label>
      <input type="number" name="requested_amount" step="0.01" min="1" class="form-control"
             value="{{ old('requested_amount', $investment->requested_amount) }}" oninput="calcPreview()" required>
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">মেয়াদ (মাস) *</label>
      <input type="number" name="duration_months" min="1" max="120" class="form-control"
             value="{{ old('duration_months', $investment->duration_months) }}" required>
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">প্রত্যাশিত লাভের হার (%) *</label>
      <div class="input-group">
        <input type="number" name="expected_profit_ratio" step="0.01" min="0" max="100" class="form-control"
               value="{{ old('expected_profit_ratio', $investment->expected_profit_ratio) }}" oninput="calcPreview()" required>
        <span class="input-group-text">%</span>
      </div>
    </div>
  </div>

  <div class="mb-4">
    <label class="form-label fw-semibold">প্রত্যাশিত রিটার্ন তারিখ *</label>
    <input type="date" name="expected_return_date" class="form-control"
           value="{{ old('expected_return_date', $investment->expected_return_date->format('Y-m-d')) }}" required>
  </div>

  <div class="alert border-0 mb-4" style="background:#e8f5e9">
    <div class="row text-center g-2">
      <div class="col-4">
        <div class="text-muted small">বিনিয়োগ</div>
        <div class="fw-bold text-primary" id="prevInv">৳{{ number_format($investment->requested_amount,2) }}</div>
      </div>
      <div class="col-4">
        <div class="text-muted small">প্রত্যাশিত লাভ</div>
        <div class="fw-bold text-success" id="prevProfit">৳{{ number_format($investment->expected_profit_amount,2) }}</div>
      </div>
      <div class="col-4">
        <div class="text-muted small">মোট রিটার্ন</div>
        <div class="fw-bold text-warning" id="prevTotal">৳{{ number_format($investment->expected_return_amount,2) }}</div>
      </div>
    </div>
  </div>

  <div class="border-top pt-3 d-flex justify-content-between">
    <a href="{{ route('investments.show', $investment) }}" class="btn btn-outline-secondary">বাতিল</a>
    <button type="submit" class="btn btn-warning px-4">
      <i class="bi bi-save me-1"></i>আপডেট ও পুনরায় জমা
    </button>
  </div>
  </form>
  </div>
</div>
</div>
</div>
@endsection
@push('scripts')
<script>
function calcPreview() {
    const inv    = parseFloat(document.querySelector('[name=requested_amount]')?.value)   || 0;
    const ratio  = parseFloat(document.querySelector('[name=expected_profit_ratio]')?.value) || 0;
    const profit = inv * ratio / 100;
    document.getElementById('prevInv').textContent    = '৳ ' + inv.toLocaleString('en-IN',{minimumFractionDigits:2});
    document.getElementById('prevProfit').textContent  = '৳ ' + profit.toLocaleString('en-IN',{minimumFractionDigits:2});
    document.getElementById('prevTotal').textContent   = '৳ ' + (inv+profit).toLocaleString('en-IN',{minimumFractionDigits:2});
}
</script>
@endpush
