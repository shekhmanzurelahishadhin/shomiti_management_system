@extends('layouts.app')
@section('title','Monthly Collection Report')
@section('page-title','Monthly Collection Report')
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
  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <span><i class="bi bi-calendar-check me-2 text-primary"></i>
      Collection Report — {{ date('F',mktime(0,0,0,$month,1)) }} {{ $year }}
    </span>
    <span class="badge bg-success px-3 py-2 fs-6">Total: ৳{{ number_format($total,2) }}</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>Member</th><th>Bill Period</th><th>Amount</th><th>Method</th><th>Reference</th><th>Date</th><th>Collected By</th></tr>
      </thead>
      <tbody>
      @forelse($payments as $i => $p)
        <tr>
          <td class="text-muted">{{ $i+1 }}</td>
          <td>
            <div class="fw-semibold">{{ $p->member->name }}</div>
            <small class="text-muted">{{ $p->member->member_id }}</small>
          </td>
          <td>{{ $p->bill ? $p->bill->month_name.' '.$p->bill->bill_year : '—' }}</td>
          <td class="fw-bold text-success">৳{{ number_format($p->amount,2) }}</td>
          <td><span class="badge bg-secondary">{{ strtoupper($p->payment_method) }}</span></td>
          <td>{{ $p->reference ?? '—' }}</td>
          <td>{{ $p->payment_date->format('d M Y') }}</td>
          <td>{{ $p->collector->name ?? 'System' }}</td>
        </tr>
      @empty
        <tr><td colspan="8" class="text-center text-muted py-4">No payments found for this period.</td></tr>
      @endforelse
      </tbody>
      @if($payments->count())
      <tfoot class="table-light">
        <tr>
          <td colspan="3" class="text-end fw-bold">Total Collection:</td>
          <td colspan="5" class="fw-bold text-success fs-5">৳{{ number_format($total,2) }}</td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection
