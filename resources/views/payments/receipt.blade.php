@extends('layouts.app')
@section('title','Receipt')
@section('page-title','Payment Receipt')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body p-4" id="receiptContent">
        <div class="text-center mb-4">
          <h4 class="fw-bold text-primary">নবদিগন্ত সমবায় সমিতি</h4>
          <div class="text-muted">Nabadiganta Somobai Somiti</div>
          <div class="text-muted small">{{ \App\Models\Setting::get('somity_address') }}</div>
          <hr>
          <h5 class="fw-bold">PAYMENT RECEIPT</h5>
          <div class="text-muted small">Receipt #{{ str_pad($payment->id,6,'0',STR_PAD_LEFT) }}</div>
        </div>

        <table class="table table-sm table-borderless">
          <tr>
            <td class="text-muted">Member</td>
            <td class="fw-semibold text-end">{{ $payment->member->name }}</td>
          </tr>
          <tr>
            <td class="text-muted">Member ID</td>
            <td class="text-end">{{ $payment->member->member_id }}</td>
          </tr>
          <tr>
            <td class="text-muted">Bill Period</td>
            <td class="text-end">{{ $payment->bill->month_name }} {{ $payment->bill->bill_year }}</td>
          </tr>
          <tr>
            <td class="text-muted">Payment Method</td>
            <td class="text-end"><span class="badge bg-secondary">{{ strtoupper($payment->payment_method) }}</span></td>
          </tr>
          @if($payment->reference)
          <tr>
            <td class="text-muted">Reference</td>
            <td class="text-end">{{ $payment->reference }}</td>
          </tr>
          @endif
          <tr>
            <td class="text-muted">Collected By</td>
            <td class="text-end">{{ $payment->collector->name ?? 'System' }}</td>
          </tr>
          <tr>
            <td class="text-muted">Date</td>
            <td class="text-end">{{ $payment->payment_date->format('d M Y') }}</td>
          </tr>
          <tr class="border-top">
            <td class="fw-bold fs-5 pt-3">Amount Paid</td>
            <td class="fw-bold fs-4 text-success text-end pt-3">৳{{ number_format($payment->amount,2) }}</td>
          </tr>
          <tr>
            <td class="text-muted">Remaining Due</td>
            <td class="text-end {{ $payment->bill->total_due > 0 ? 'text-danger fw-semibold' : 'text-success' }}">৳{{
              number_format($payment->bill->total_due,2) }}</td>
          </tr>
        </table>

        <div class="text-center mt-3">
          <span
            class="badge {{ $payment->bill->status=='paid' ? 'bg-success' : 'bg-warning text-dark' }} px-4 py-2 fs-6">
            Bill Status: {{ ucfirst($payment->bill->status) }}
          </span>
        </div>

        @if($payment->notes)
        <div class="mt-3 p-2 bg-light rounded small text-muted">
          <strong>Note:</strong> {{ $payment->notes }}
        </div>
        @endif

        <div class="text-center text-muted small mt-4">
          <em>Thank you for your payment.</em><br>
          Printed: {{ now()->format('d M Y H:i') }}
        </div>
      </div>
      <div class="card-footer d-flex gap-2 justify-content-center">
        <a href="{{ route('payments.receipt-pdf',$payment) }}" class="btn btn-danger btn-sm">
          <i class="bi bi-file-pdf me-1"></i>Download PDF
        </a>
        <button onclick="printReceipt()" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-printer me-1"></i>Print
        </button>
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left me-1"></i>Back
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
<script>
function printReceipt() {
    window.print();
}
</script>
<style>
  @media print {
    body * {
      visibility: hidden;
      /* hide everything */
    }

    #receiptContent,
    #receiptContent * {
      visibility: visible;
      /* show receipt */
    }

    #receiptContent {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
    }
  }
</style>