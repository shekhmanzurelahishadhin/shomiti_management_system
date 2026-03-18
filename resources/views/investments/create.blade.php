@extends('layouts.app')
@section('title','বিনিয়োগ আবেদন')
@section('page-title','নতুন বিনিয়োগ আবেদন')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
  <div class="card-header py-3" style="background:linear-gradient(135deg,#1e8449,#27ae60);color:#fff">
    <i class="bi bi-graph-up-arrow me-2 fs-5"></i>বিনিয়োগ আবেদন ফর্ম
  </div>
  <div class="card-body p-4">
  <form method="POST" action="{{ route('investments.store') }}">
  @csrf

  {{-- Member selection: Admin sees dropdown, Member sees own info --}}
  @if(auth()->user()->isMemberRole())
    @php $myMember = auth()->user()->getLinkedMember(); @endphp
    <input type="hidden" name="member_id" value="{{ $myMember->id }}">
    <div class="alert alert-light border mb-3 py-2">
      <i class="bi bi-person-check text-success me-2"></i>
      সদস্য: <strong>{{ $myMember->name }}</strong>
      <span class="text-muted ms-2">({{ $myMember->member_id }})</span>
    </div>
  @else
  <div class="mb-3">
    <label class="form-label fw-semibold">সদস্য নির্বাচন <span class="text-danger">*</span></label>
    <select name="member_id" class="form-select @error('member_id') is-invalid @enderror" required>
      <option value="">— সদস্য নির্বাচন করুন —</option>
      @foreach($members as $m)
        <option value="{{ $m->id }}" {{ old('member_id') == $m->id ? 'selected' : '' }}>
          {{ $m->name }} ({{ $m->member_id }})
        </option>
      @endforeach
    </select>
    @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  @endif

  <div class="row g-3 mb-3">
    <div class="col-md-8">
      <label class="form-label fw-semibold">আবেদনের তারিখ <span class="text-danger">*</span></label>
      <input type="date" name="submitted_date" class="form-control"
             value="{{ old('submitted_date', date('Y-m-d')) }}" required>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label fw-semibold">প্রকল্প / কাজের নাম <span class="text-danger">*</span></label>
    <input type="text" name="project_name" class="form-control @error('project_name') is-invalid @enderror"
           value="{{ old('project_name') }}" placeholder="যেমন: কাঁচামাল ব্যবসা, জমি ক্রয়..." required>
    @error('project_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="mb-3">
    <label class="form-label fw-semibold">প্রকল্পের বিবরণ</label>
    <textarea name="project_description" class="form-control" rows="4"
              placeholder="প্রকল্পের বিস্তারিত বিবরণ দিন...">{{ old('project_description') }}</textarea>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <label class="form-label fw-semibold">বিনিয়োগের পরিমাণ (৳) <span class="text-danger">*</span></label>
      <input type="number" name="requested_amount" step="0.01" min="1"
             class="form-control @error('requested_amount') is-invalid @enderror"
             value="{{ old('requested_amount') }}" oninput="calcPreview()" required>
      @error('requested_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">মেয়াদ (মাস) <span class="text-danger">*</span></label>
      <input type="number" name="duration_months" min="1" max="120"
             class="form-control @error('duration_months') is-invalid @enderror"
             value="{{ old('duration_months', 6) }}" required>
      @error('duration_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">প্রত্যাশিত লাভের হার (%) <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="number" name="expected_profit_ratio" step="0.01" min="0" max="100"
               class="form-control @error('expected_profit_ratio') is-invalid @enderror"
               value="{{ old('expected_profit_ratio', 10) }}" oninput="calcPreview()" required>
        <span class="input-group-text">%</span>
      </div>
      @error('expected_profit_ratio')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="mb-4">
    <label class="form-label fw-semibold">প্রত্যাশিত রিটার্নের তারিখ <span class="text-danger">*</span></label>
    <input type="date" name="expected_return_date" class="form-control @error('expected_return_date') is-invalid @enderror"
           value="{{ old('expected_return_date') }}" required>
    @error('expected_return_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  {{-- Preview calculation --}}
  <div class="alert border-0 mb-4" style="background:#e8f5e9">
    <div class="fw-semibold text-success small mb-2"><i class="bi bi-calculator me-1"></i>হিসাব পূর্বরূপ</div>
    <div class="row text-center g-2">
      <div class="col-4">
        <div class="text-muted small">বিনিয়োগ</div>
        <div class="fw-bold text-primary" id="prevInv">৳ 0</div>
      </div>
      <div class="col-4">
        <div class="text-muted small">প্রত্যাশিত লাভ</div>
        <div class="fw-bold text-success" id="prevProfit">৳ 0</div>
      </div>
      <div class="col-4">
        <div class="text-muted small">মোট প্রত্যাশিত রিটার্ন</div>
        <div class="fw-bold text-warning" id="prevTotal">৳ 0</div>
      </div>
    </div>
  </div>

  <div class="border-top pt-3 d-flex justify-content-between">
    <a href="{{ route('investments.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>বাতিল
    </a>
    <button type="submit" class="btn btn-success px-4">
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
function calcPreview() {
    const inv    = parseFloat(document.querySelector('[name=requested_amount]')?.value) || 0;
    const ratio  = parseFloat(document.querySelector('[name=expected_profit_ratio]')?.value) || 0;
    const profit = inv * ratio / 100;
    document.getElementById('prevInv').textContent    = '৳ ' + inv.toLocaleString('en-IN', {minimumFractionDigits:2});
    document.getElementById('prevProfit').textContent  = '৳ ' + profit.toLocaleString('en-IN', {minimumFractionDigits:2});
    document.getElementById('prevTotal').textContent   = '৳ ' + (inv+profit).toLocaleString('en-IN', {minimumFractionDigits:2});
}
calcPreview();
</script>
@endpush
