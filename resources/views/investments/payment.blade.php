@extends('layouts.app')
@section('title','বিনিয়োগ পেমেন্ট')
@section('page-title','বিনিয়োগ পেমেন্ট')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">

<div class="alert alert-success border-0 mb-3">
  <div class="row text-center g-2">
    <div class="col-4">
      <div class="text-muted small">অনুমোদিত পরিমাণ</div>
      <div class="fw-bold fs-5 text-success">৳{{ number_format($investment->approved_amount,2) }}</div>
    </div>
    <div class="col-4">
      <div class="text-muted small">প্রত্যাশিত লাভ ({{ $investment->approved_profit_ratio }}%)</div>
      <div class="fw-bold text-success">৳{{ number_format($investment->expected_profit_amount,2) }}</div>
    </div>
    <div class="col-4">
      <div class="text-muted small">মোট প্রত্যাশিত রিটার্ন</div>
      <div class="fw-bold text-primary">৳{{ number_format($investment->expected_return_amount,2) }}</div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header py-3" style="background:linear-gradient(135deg,#1e8449,#27ae60);color:#fff">
    <i class="bi bi-cash-coin me-2 fs-5"></i>পেমেন্ট ভাউচার তৈরি
    <div class="small opacity-75 mt-1">{{ $investment->project_name }} — {{ $investment->member->name }}</div>
  </div>
  <div class="card-body p-4">
    <form method="POST" action="{{ route('investments.payment.process', $investment) }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">পরিমাণ (৳) <span class="text-danger">*</span></label>
          <input type="number" name="amount" step="0.01" min="1"
                 class="form-control @error('amount') is-invalid @enderror"
                 value="{{ old('amount', $investment->approved_amount) }}" required>
          @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">পেমেন্টের তারিখ <span class="text-danger">*</span></label>
          <input type="date" name="payment_date" class="form-control"
                 value="{{ old('payment_date', date('Y-m-d')) }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">পেমেন্ট পদ্ধতি <span class="text-danger">*</span></label>
          <select name="payment_method" class="form-select" id="payMethod" onchange="toggleBankFields()" required>
            @foreach(['cash'=>'নগদ','bank'=>'ব্যাংক ট্রান্সফার','bkash'=>'বিকাশ','nagad'=>'নগদ অ্যাপ','cheque'=>'চেক'] as $v=>$l)
              <option value="{{ $v }}" {{ old('payment_method','cash')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">রেফারেন্স / চেক নং</label>
          <input type="text" name="reference" class="form-control" value="{{ old('reference') }}" placeholder="ঐচ্ছিক">
        </div>
        <div id="bankFields" style="display:none" class="col-12">
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label fw-semibold">ব্যাংকের নাম</label>
              <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">অ্যাকাউন্ট নম্বর</label>
              <input type="text" name="account_number" class="form-control" value="{{ old('account_number') }}">
            </div>
          </div>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">নোট</label>
          <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
        </div>
      </div>
      <hr class="mt-4">
      <div class="d-flex justify-content-between">
        <a href="{{ route('investments.show', $investment) }}" class="btn btn-outline-secondary">বাতিল</a>
        <button type="submit" class="btn btn-success px-4"
                onclick="return confirm('পেমেন্ট নিশ্চিত করবেন? এটি বিনিয়োগ সক্রিয় করবে।')">
          <i class="bi bi-cash-coin me-1"></i>পেমেন্ট করুন ও ভাউচার তৈরি করুন
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
function toggleBankFields() {
    const m = document.getElementById('payMethod').value;
    document.getElementById('bankFields').style.display = (m === 'bank' || m === 'cheque') ? '' : 'none';
}
toggleBankFields();
</script>
@endpush
