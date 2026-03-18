@extends('layouts.app')
@section('title','আমার বকেয়া বিল')
@section('page-title','আমার বকেয়া বিল')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>আমার বকেয়া বিল</h5>
  <a href="{{ route('members.show', $scope) }}" class="btn btn-sm btn-outline-primary">
    <i class="bi bi-person me-1"></i>আমার প্রোফাইল
  </a>
</div>

@if($myBills->isEmpty())
<div class="card">
  <div class="card-body text-center py-5">
    <i class="bi bi-check-circle-fill fs-1 text-success d-block mb-3"></i>
    <h5 class="fw-bold text-success">সব বিল পরিশোধ হয়েছে!</h5>
    <p class="text-muted">আপনার কোনো বকেয়া বিল নেই।</p>
  </div>
</div>
@else
<div class="alert alert-warning py-2 mb-3">
  <i class="bi bi-info-circle me-2"></i>
  মোট <strong>{{ $myBills->count() }}</strong>টি বকেয়া বিল —
  মোট বকেয়া: <strong class="text-danger">৳{{ number_format($myBills->sum(fn($b)=>$b->total_due),2) }}</strong>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>মাস</th><th>বিলের পরিমাণ</th><th>জরিমানা</th><th>পরিশোধ</th><th>বাকি</th><th>শেষ তারিখ</th><th>অবস্থা</th></tr>
      </thead>
      <tbody>
      @foreach($myBills as $bill)
        <tr>
          <td class="fw-semibold">{{ $bill->month_name }} {{ $bill->bill_year }}</td>
          <td>৳{{ number_format($bill->amount,2) }}</td>
          <td class="{{ $bill->fine>0?'text-danger':'' }}">
            {{ $bill->fine>0 ? '৳'.number_format($bill->fine,2) : '—' }}
          </td>
          <td class="text-success">৳{{ number_format($bill->paid_amount,2) }}</td>
          <td class="fw-bold text-danger">৳{{ number_format($bill->total_due,2) }}</td>
          <td class="{{ $bill->due_date->isPast()?'text-danger fw-semibold':'' }}">
            {{ $bill->due_date->format('d M Y') }}
          </td>
          <td><span class="badge badge-{{ $bill->status }}">{{ ucfirst($bill->status) }}</span></td>
        </tr>
      @endforeach
      </tbody>
      <tfoot class="table-light fw-bold">
        <tr>
          <td colspan="4" class="text-end">মোট বকেয়া:</td>
          <td class="text-danger">৳{{ number_format($myBills->sum(fn($b)=>$b->total_due),2) }}</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@endif
@endsection
