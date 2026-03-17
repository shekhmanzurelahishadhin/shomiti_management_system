@extends('layouts.app')
@section('title','Collect Payment')
@section('page-title','Collect Payment')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header py-3"><i class="bi bi-cash-coin me-2 text-success"></i>Record Payment</div>
      <div class="card-body">

        {{-- Step 1: Select member --}}
        <form method="GET" action="{{ route('payments.create') }}" class="mb-4">
          <div class="row g-2 align-items-end">
            <div class="col-md-8">
              <label class="form-label fw-semibold">Select Member</label>
              <select name="member_id" class="form-select" onchange="this.form.submit()">
                <option value="">— Choose Member —</option>
                @foreach($members as $m)
                  <option value="{{ $m->id }}" {{ $selectedMember && $selectedMember->id==$m->id ? 'selected' : '' }}>
                    {{ $m->name }} ({{ $m->member_id }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <button class="btn btn-outline-primary w-100">Load Bills</button>
            </div>
          </div>
        </form>

        @if($selectedMember)
        <div class="alert alert-info py-2 mb-3">
          <strong>{{ $selectedMember->name }}</strong> &mdash; {{ $selectedMember->member_id }}
          &nbsp;|&nbsp; Monthly: ৳{{ number_format($selectedMember->monthly_deposit,2) }}
        </div>

        <form method="POST" action="{{ route('payments.store') }}">
          @csrf
          <input type="hidden" name="member_id" value="{{ $selectedMember->id }}">

          <div class="mb-3">
            <label class="form-label fw-semibold">Select Bill <span class="text-danger">*</span></label>
            @if($pendingBills->isEmpty())
              <div class="alert alert-success py-2">No pending bills for this member.</div>
            @else
              <select name="bill_id" class="form-select @error('bill_id') is-invalid @enderror"
                      id="billSelect" required>
                <option value="">— Choose Bill —</option>
                @foreach($pendingBills as $bill)
                  <option value="{{ $bill->id }}"
                          data-due="{{ $bill->total_due }}"
                          {{ (old('bill_id', request('bill_id'))==$bill->id) ? 'selected' : '' }}>
                    {{ $bill->month_name }} {{ $bill->bill_year }}
                    — Due: ৳{{ number_format($bill->total_due,2) }}
                    [{{ ucfirst($bill->status) }}]
                  </option>
                @endforeach
              </select>
              @error('bill_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @endif
          </div>

          @if($pendingBills->isNotEmpty())
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Amount (৳) <span class="text-danger">*</span></label>
              <input type="number" name="amount" step="0.01" min="0.01" id="amountInput"
                     class="form-control @error('amount') is-invalid @enderror"
                     value="{{ old('amount') }}" required>
              @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
              <select name="payment_method" class="form-select" required>
                @foreach(['cash'=>'Cash','bank'=>'Bank Transfer','bkash'=>'bKash','nagad'=>'Nagad','other'=>'Other'] as $v=>$l)
                  <option value="{{ $v }}" {{ old('payment_method','cash')==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
              <input type="date" name="payment_date" class="form-control"
                     value="{{ old('payment_date', date('Y-m-d')) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Reference / Transaction ID</label>
              <input type="text" name="reference" class="form-control" value="{{ old('reference') }}"
                     placeholder="Optional reference number">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
          </div>
          <hr class="mt-4">
          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check-circle me-1"></i>Record Payment
            </button>
          </div>
          @endif
        </form>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
const billSel = document.getElementById('billSelect');
const amtInput = document.getElementById('amountInput');
if (billSel && amtInput) {
    billSel.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt && opt.dataset.due) amtInput.value = opt.dataset.due;
    });
    // auto-fill on load
    if (billSel.value) {
        const opt = billSel.options[billSel.selectedIndex];
        if (opt && opt.dataset.due && !amtInput.value) amtInput.value = opt.dataset.due;
    }
}
</script>
@endpush
