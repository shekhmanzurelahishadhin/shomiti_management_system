@extends('layouts.app')
@section('title','Member Deposit Report')
@section('page-title','Member Deposit Report')
@section('content')
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label small">Member</label>
        <select name="member_id" class="form-select form-select-sm">
          <option value="">— Select Member —</option>
          @foreach($members as $m)
            <option value="{{ $m->id }}" {{ request('member_id')==$m->id?'selected':'' }}>
              {{ $m->name }} ({{ $m->member_id }})
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small">Year</label>
        <select name="year" class="form-select form-select-sm">
          @for($y=now()->year;$y>=2020;$y--)
            <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
          @endfor
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>দেখুন</button>
      </div>
      @if($member)
      <div class="col-md-2">
        <a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-danger btn-sm w-100">
          <i class="bi bi-file-pdf me-1"></i>Export PDF
        </a>
      </div>
      @endif
    </form>
  </div>
</div>

@if($member)
<div class="card mb-3">
  <div class="card-body">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h5 class="fw-bold mb-1">{{ $member->name }}</h5>
        <div class="text-muted">{{ $member->member_id }} &nbsp;|&nbsp; {{ $member->phone ?? 'No phone' }}</div>
      </div>
      <div class="col-md-4 text-end">
        <div class="text-muted small">Monthly Deposit</div>
        <div class="fs-4 fw-bold text-success">৳{{ number_format($member->monthly_deposit,2) }}</div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header py-3">
    <i class="bi bi-person-lines-fill me-2 text-success"></i>
    Deposit Statement — {{ $year }}
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Month</th><th>Bill Amount</th><th>Fine</th><th>Discount</th><th>Paid</th><th>Due</th><th>Status</th></tr>
      </thead>
      <tbody>
      @php $totalBilled=0; $totalPaid=0; $totalDue=0; @endphp
      @for($m=1;$m<=12;$m++)
        @php $bill = $bills->firstWhere('bill_month',$m); @endphp
        <tr class="{{ $bill ? '' : 'text-muted' }}">
          <td>{{ date('F',mktime(0,0,0,$m,1)) }}</td>
          @if($bill)
            @php $totalBilled+=$bill->amount; $totalPaid+=$bill->paid_amount; $totalDue+=$bill->total_due; @endphp
            <td>৳{{ number_format($bill->amount,2) }}</td>
            <td class="{{ $bill->fine>0?'text-danger':'' }}">{{ $bill->fine>0?'৳'.number_format($bill->fine,2):'—' }}</td>
            <td class="{{ $bill->discount>0?'text-success':'' }}">{{ $bill->discount>0?'৳'.number_format($bill->discount,2):'—' }}</td>
            <td class="text-success">৳{{ number_format($bill->paid_amount,2) }}</td>
            <td class="{{ $bill->total_due>0?'text-danger fw-semibold':'' }}">৳{{ number_format($bill->total_due,2) }}</td>
            <td><span class="badge badge-{{ $bill->status }}">{{ ucfirst($bill->status) }}</span></td>
          @else
            <td colspan="5" class="text-muted fst-italic">No bill generated</td>
            <td>—</td>
          @endif
        </tr>
      @endfor
      </tbody>
      <tfoot class="table-light fw-bold">
        <tr>
          <td>Total</td>
          <td>৳{{ number_format($totalBilled,2) }}</td>
          <td></td><td></td>
          <td class="text-success">৳{{ number_format($totalPaid,2) }}</td>
          <td class="text-danger">৳{{ number_format($totalDue,2) }}</td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@else
<div class="alert alert-info">Please select a member to view their deposit report.</div>
@endif
@endsection
