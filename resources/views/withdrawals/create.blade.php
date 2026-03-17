@extends('layouts.app')
@section('title','উত্তোলন আবেদন')
@section('page-title','নতুন উত্তোলন আবেদন')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">

<div class="alert alert-info border-0 mb-4">
  <i class="bi bi-info-circle me-2"></i>
  <strong>নিয়মাবলী:</strong> উত্তোলন আবেদন করলে সদস্যপদ <strong>স্থগিত (অপেক্ষামান)</strong> হবে।
  সমিতি নির্ধারিত সময়ে টাকা ফেরত দেবে। সম্পূর্ণ পরিশোধের পর সদস্যপদ
  <strong>সংযোগ বিচ্ছিন্ন</strong> হবে।
</div>

<div class="card">
  <div class="card-header py-3" style="background:linear-gradient(135deg,#856404,#d4ac0d);color:#fff">
    <i class="bi bi-cash-stack me-2 fs-5"></i>অর্থ উত্তোলন আবেদন ফর্ম
  </div>
  <div class="card-body p-4">
    <form method="POST" action="{{ route('withdrawals.store') }}">
    @csrf

    <div class="mb-3">
      <label class="form-label fw-semibold">সদস্য নির্বাচন <span class="text-danger">*</span></label>
      <select name="member_id" class="form-select @error('member_id') is-invalid @enderror"
              id="memberSelect" required>
        <option value="">— সদস্য নির্বাচন করুন —</option>
        @foreach($members as $m)
          <option value="{{ $m->id }}"
                  data-share="{{ $m->share_value }}"
                  data-deposit="{{ $m->monthly_deposit }}"
                  {{ (old('member_id') ?? request('member_id')) == $m->id ? 'selected' : '' }}>
            {{ $m->name }} ({{ $m->member_id }}) — শেয়ার: ৳{{ number_format($m->share_value,0) }}
          </option>
        @endforeach
      </select>
      @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <label class="form-label fw-semibold">শেয়ার মূল্য (৳) <span class="text-danger">*</span></label>
        <input type="number" name="share_amount" id="shareAmount" step="0.01" min="0"
               class="form-control @error('share_amount') is-invalid @enderror"
               value="{{ old('share_amount') }}" oninput="calcTotal()" required>
        @error('share_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">সঞ্চয় (৳) <span class="text-danger">*</span></label>
        <input type="number" name="savings_amount" id="savingsAmount" step="0.01" min="0"
               class="form-control @error('savings_amount') is-invalid @enderror"
               value="{{ old('savings_amount', 0) }}" oninput="calcTotal()" required>
        @error('savings_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">মুনাফা (৳)</label>
        <input type="number" name="profit_amount" id="profitAmount" step="0.01" min="0"
               class="form-control" value="{{ old('profit_amount', 0) }}" oninput="calcTotal()">
      </div>
    </div>

    <div class="alert alert-success py-2 mb-3 d-flex justify-content-between align-items-center">
      <span class="fw-semibold">মোট পরিশোধযোগ্য:</span>
      <span class="fs-5 fw-bold text-success" id="totalDisplay">৳ 0.00</span>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">আবেদনের তারিখ <span class="text-danger">*</span></label>
        <input type="date" name="requested_date" class="form-control"
               value="{{ old('requested_date', date('Y-m-d')) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">প্রত্যাশিত পরিশোধের তারিখ</label>
        <input type="date" name="scheduled_repay_date" class="form-control"
               value="{{ old('scheduled_repay_date') }}">
        <div class="form-text">সমিতি কর্তৃক নির্ধারণ হবে</div>
      </div>
    </div>

    <div class="mb-4">
      <label class="form-label fw-semibold">উত্তোলনের কারণ <span class="text-danger">*</span></label>
      <textarea name="reason" class="form-control @error('reason') is-invalid @enderror"
                rows="3" required placeholder="কেন উত্তোলন করতে চান?">{{ old('reason') }}</textarea>
      @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="border-top pt-4 d-flex justify-content-between">
      <a href="{{ route('withdrawals.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>বাতিল
      </a>
      <button type="submit" class="btn btn-warning px-4">
        <i class="bi bi-send me-1"></i>আবেদন জমা করুন
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
document.getElementById('memberSelect')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt && opt.dataset.share) {
        document.getElementById('shareAmount').value = opt.dataset.share;
        calcTotal();
    }
});
function calcTotal() {
    const s = parseFloat(document.getElementById('shareAmount')?.value)   || 0;
    const a = parseFloat(document.getElementById('savingsAmount')?.value) || 0;
    const p = parseFloat(document.getElementById('profitAmount')?.value)  || 0;
    document.getElementById('totalDisplay').textContent = '৳ ' + (s+a+p).toLocaleString('en-IN',{minimumFractionDigits:2});
}
calcTotal();
</script>
@endpush
