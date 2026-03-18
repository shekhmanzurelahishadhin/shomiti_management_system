@extends('layouts.app')
@section('title','নিষ্পত্তি')
@section('page-title','বিনিয়োগ নিষ্পত্তি')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">

<div class="card mb-3">
  <div class="card-body">
    <div class="row text-center g-2">
      <div class="col-4">
        <div class="text-muted small">বিনিয়োগ পরিমাণ</div>
        <div class="fw-bold fs-5 text-primary">৳{{ number_format($investment->approved_amount,2) }}</div>
      </div>
      <div class="col-4">
        <div class="text-muted small">প্রত্যাশিত লাভ</div>
        <div class="fw-bold text-success">৳{{ number_format($investment->expected_profit_amount,2) }}</div>
      </div>
      <div class="col-4">
        <div class="text-muted small">মোট প্রত্যাশিত রিটার্ন</div>
        <div class="fw-bold text-warning">৳{{ number_format($investment->expected_return_amount,2) }}</div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header py-3" style="background:linear-gradient(135deg,#1c2833,#34495e);color:#fff">
    <i class="bi bi-check2-circle me-2 fs-5"></i>বিনিয়োগ নিষ্পত্তি ফর্ম
    <div class="small opacity-75 mt-1">{{ $investment->project_name }} — {{ $investment->member->name }}</div>
  </div>
  <div class="card-body p-4">
    <form method="POST" action="{{ route('investments.settlement.process', $investment) }}">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-semibold">প্রকৃত লাভ / ক্ষতি (৳) <span class="text-danger">*</span></label>
        <input type="number" name="actual_profit_loss" step="0.01"
               class="form-control fs-5 @error('actual_profit_loss') is-invalid @enderror"
               value="{{ old('actual_profit_loss', $investment->expected_profit_amount) }}"
               oninput="calcReturn()" required>
        <div class="form-text">লাভ হলে ধনাত্মক (+), ক্ষতি হলে ঋণাত্মক (-) দিন</div>
        @error('actual_profit_loss')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      {{-- Return preview --}}
      <div class="alert border-0 mb-3" id="returnPreview" style="background:#e8f5e9">
        <div class="row text-center">
          <div class="col-4">
            <div class="text-muted small">বিনিয়োগ</div>
            <div class="fw-bold">৳{{ number_format($investment->approved_amount,2) }}</div>
          </div>
          <div class="col-4">
            <div class="text-muted small" id="plLabel">লাভ/ক্ষতি</div>
            <div class="fw-bold" id="plDisplay">৳{{ number_format($investment->expected_profit_amount,2) }}</div>
          </div>
          <div class="col-4">
            <div class="text-muted small">মোট রিটার্ন</div>
            <div class="fw-bold fs-5 text-primary" id="returnDisplay">৳{{ number_format($investment->expected_return_amount,2) }}</div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">পেমেন্ট পদ্ধতি <span class="text-danger">*</span></label>
          <select name="payment_method" class="form-select" required>
            @foreach(['cash'=>'নগদ','bank'=>'ব্যাংক ট্রান্সফার','bkash'=>'বিকাশ','nagad'=>'নগদ অ্যাপ','cheque'=>'চেক'] as $v=>$l)
              <option value="{{ $v }}">{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">নিষ্পত্তির তারিখ <span class="text-danger">*</span></label>
          <input type="date" name="settlement_date" class="form-control"
                 value="{{ old('settlement_date', date('Y-m-d')) }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">রেফারেন্স</label>
          <input type="text" name="reference" class="form-control" value="{{ old('reference') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">ব্যাংকের নাম</label>
          <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">নোট</label>
          <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
        </div>
      </div>
      <hr>
      <div class="d-flex justify-content-between">
        <a href="{{ route('investments.show', $investment) }}" class="btn btn-outline-secondary">বাতিল</a>
        <button type="submit" class="btn btn-dark px-4"
                onclick="return confirm('নিষ্পত্তি নিশ্চিত করবেন? বিনিয়োগ বন্ধ হয়ে যাবে।')">
          <i class="bi bi-check2-circle me-1"></i>নিষ্পত্তি করুন
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
const invAmt = {{ $investment->approved_amount }};
function calcReturn() {
    const pl    = parseFloat(document.querySelector('[name=actual_profit_loss]')?.value) || 0;
    const ret   = invAmt + pl;
    const isLoss = pl < 0;
    document.getElementById('plLabel').textContent    = isLoss ? 'ক্ষতি' : 'লাভ';
    document.getElementById('plDisplay').textContent  = (isLoss?'—৳':'+৳') + Math.abs(pl).toLocaleString('en-IN',{minimumFractionDigits:2});
    document.getElementById('plDisplay').className    = 'fw-bold ' + (isLoss?'text-danger':'text-success');
    document.getElementById('returnDisplay').textContent = '৳' + ret.toLocaleString('en-IN',{minimumFractionDigits:2});
    document.getElementById('returnPreview').style.background = isLoss ? '#fdf2f2' : '#e8f5e9';
}
calcReturn();
</script>
@endpush
