@extends('layouts.app')
@section('title','Defaulter List')
@section('page-title','Defaulter List')
@section('content')
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label small">Month</label>
        <select name="month" class="form-select form-select-sm">
          @for($m=1;$m<=12;$m++)
            <option value="{{ $m }}" {{ $month==$m?'selected':'' }}>{{ date('F',mktime(0,0,0,$m,1)) }}</option>
          @endfor
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
        <button class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Filter</button>
      </div>
      <div class="col-md-2">
        <a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-danger btn-sm w-100">
          <i class="bi bi-file-pdf me-1"></i>Export PDF
        </a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header py-3 d-flex justify-content-between">
    <span><i class="bi bi-exclamation-triangle me-2 text-danger"></i>
      Defaulters — {{ date('F',mktime(0,0,0,$month,1)) }} {{ $year }}
    </span>
    <span class="badge bg-danger px-3 py-2">{{ $defaulters->count() }} Defaulters</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>Member</th><th>Phone</th><th>Bill Amount</th><th>Fine</th><th>Paid</th><th>Due</th><th>Status</th><th></th></tr>
      </thead>
      <tbody>
      @forelse($defaulters as $i => $bill)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>
            <div class="fw-semibold">{{ $bill->member->name }}</div>
            <small class="text-muted">{{ $bill->member->member_id }}</small>
          </td>
          <td>{{ $bill->member->phone ?? '—' }}</td>
          <td>৳{{ number_format($bill->amount,2) }}</td>
          <td class="{{ $bill->fine > 0 ? 'text-danger' : '' }}">{{ $bill->fine > 0 ? '৳'.number_format($bill->fine,2) : '—' }}</td>
          <td>৳{{ number_format($bill->paid_amount,2) }}</td>
          <td class="fw-bold text-danger">৳{{ number_format($bill->total_due,2) }}</td>
          <td><span class="badge badge-{{ $bill->status }}">{{ ucfirst($bill->status) }}</span></td>
          <td>
            <a href="{{ route('bills.show',$bill) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="text-center text-muted py-4">
          <i class="bi bi-check-circle text-success fs-3 d-block mb-2"></i>
          No defaulters for this period!
        </td></tr>
      @endforelse
      </tbody>
      @if($defaulters->count())
      <tfoot class="table-light">
        <tr>
          <td colspan="6" class="text-end fw-bold">Total Outstanding:</td>
          <td class="fw-bold text-danger">৳{{ number_format($defaulters->sum(fn($b)=>$b->total_due),2) }}</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection
