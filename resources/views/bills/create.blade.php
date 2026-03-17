@extends('layouts.app')
@section('title','New Bill')
@section('page-title','Create Bill')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header py-3"><i class="bi bi-receipt me-2 text-primary"></i>New Bill</div>
      <div class="card-body">
        <form method="POST" action="{{ route('bills.store') }}">
          @csrf
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Member <span class="text-danger">*</span></label>
              <select name="member_id" class="form-select @error('member_id') is-invalid @enderror" required>
                <option value="">— Select Member —</option>
                @foreach($members as $m)
                  <option value="{{ $m->id }}" {{ old('member_id')==$m->id?'selected':'' }}
                          data-deposit="{{ $m->monthly_deposit }}">
                    {{ $m->name }} ({{ $m->member_id }}) — ৳{{ number_format($m->monthly_deposit,2) }}/mo
                  </option>
                @endforeach
              </select>
              @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Month <span class="text-danger">*</span></label>
              <select name="bill_month" class="form-select" required>
                @for($m=1;$m<=12;$m++)
                  <option value="{{ $m }}" {{ old('bill_month',now()->month)==$m?'selected':'' }}>{{ date('F',mktime(0,0,0,$m,1)) }}</option>
                @endfor
              </select>
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
              <select name="bill_year" class="form-select" required>
                @for($y=now()->year;$y>=2020;$y--)
                  <option value="{{ $y }}" {{ old('bill_year',now()->year)==$y?'selected':'' }}>{{ $y }}</option>
                @endfor
              </select>
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Amount (৳) <span class="text-danger">*</span></label>
              <input type="number" name="amount" step="0.01" min="0" id="amountField"
                     class="form-control @error('amount') is-invalid @enderror"
                     value="{{ old('amount') }}" required>
              @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
              <input type="date" name="due_date" class="form-control"
                     value="{{ old('due_date', now()->format('Y-m-').(\App\Models\Setting::get('due_date_end',15))) }}" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
          </div>
          <hr class="mt-4">
          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('bills.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-receipt me-1"></i>Create Bill</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
document.querySelector('[name=member_id]').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const dep = opt.dataset.deposit;
    if (dep) document.getElementById('amountField').value = dep;
});
</script>
@endpush
