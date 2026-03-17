@extends('layouts.app')
@section('title','Edit Bill')
@section('page-title','Edit Bill')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header py-3 d-flex justify-content-between">
        <span><i class="bi bi-pencil me-2 text-warning"></i>Edit Bill #{{ $bill->id }}</span>
        <span>{{ $bill->member->name }} — {{ $bill->month_name }} {{ $bill->bill_year }}</span>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('bills.update', $bill) }}">
          @csrf @method('PUT')
          <div class="row g-3">
            <div class="col-6">
              <label class="form-label fw-semibold">Amount (৳)</label>
              <input type="number" name="amount" step="0.01" min="0" class="form-control"
                     value="{{ old('amount',$bill->amount) }}" required>
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Fine (৳)</label>
              <input type="number" name="fine" step="0.01" min="0" class="form-control"
                     value="{{ old('fine',$bill->fine) }}" id="fineField">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Discount (৳)</label>
              <input type="number" name="discount" step="0.01" min="0" class="form-control"
                     value="{{ old('discount',$bill->discount) }}">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Due Date</label>
              <input type="date" name="due_date" class="form-control"
                     value="{{ old('due_date',$bill->due_date->format('Y-m-d')) }}" required>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="fine_waived" id="fineWaived"
                       value="1" {{ old('fine_waived',$bill->fine_waived) ? 'checked' : '' }}>
                <label class="form-check-label" for="fineWaived">Waive Fine</label>
              </div>
            </div>
            <div class="col-12" id="waiveReasonDiv" style="{{ old('fine_waived',$bill->fine_waived) ? '' : 'display:none' }}">
              <label class="form-label">Waive Reason</label>
              <input type="text" name="fine_waive_reason" class="form-control"
                     value="{{ old('fine_waive_reason',$bill->fine_waive_reason) }}"
                     placeholder="Reason for waiving fine...">
            </div>
            <div class="col-12">
              <label class="form-label">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes',$bill->notes) }}</textarea>
            </div>
          </div>
          <hr class="mt-4">
          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('bills.show',$bill) }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Update Bill</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('fineWaived').addEventListener('change', function() {
    const div = document.getElementById('waiveReasonDiv');
    div.style.display = this.checked ? '' : 'none';
    if (this.checked) document.getElementById('fineField').value = 0;
});
</script>
@endpush
