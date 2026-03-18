@extends('layouts.app')
@section('title','পেমেন্ট রিপোর্ট')
@section('page-title','বিনিয়োগ পেমেন্ট রিপোর্ট')
@section('content')
<div class="d-flex justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-cash-coin me-2 text-success"></i>বিনিয়োগ পেমেন্ট রিপোর্ট</h5>
  <a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-danger btn-sm">
    <i class="bi bi-file-pdf me-1"></i>PDF
  </a>
</div>
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="From"></div>
      <div class="col-md-3"><input type="date" name="date_to"   class="form-control form-control-sm" value="{{ request('date_to') }}"   placeholder="To"></div>
      <div class="col-md-2"><button class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-search me-1"></i>ফিল্টার</button></div>
    </form>
  </div>
</div>
<div class="row g-2 mb-3">
  <div class="col-md-4"><div class="card text-center p-3"><div class="text-muted small">মোট পেমেন্ট</div><div class="fs-4 fw-bold">{{ $payments->count() }}</div></div></div>
  <div class="col-md-4"><div class="card text-center p-3"><div class="text-muted small">মোট বিতরণ</div><div class="fs-4 fw-bold text-success">৳{{ number_format($totalPaid, 0) }}</div></div></div>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>ভাউচার</th><th>সদস্য</th><th>প্রকল্প</th><th>পরিমাণ</th><th>পদ্ধতি</th><th>তারিখ</th><th>পরিশোধকারী</th><th></th></tr>
      </thead>
      <tbody>
      @forelse($payments as $i => $p)
        <tr>
          <td>{{ $i+1 }}</td>
          <td><span class="badge bg-success">{{ $p->voucher_number }}</span></td>
          <td>
            <div class="fw-semibold">{{ $p->member->name }}</div>
            <small class="text-muted">{{ $p->member->member_id }}</small>
          </td>
          <td>{{  \Illuminate\Support\Str::limit($p->investmentRequest->project_name, 28) }}</td>
          <td class="fw-bold text-success">৳{{ number_format($p->amount, 2) }}</td>
          <td><span class="badge bg-secondary">{{ strtoupper($p->payment_method) }}</span></td>
          <td>{{ $p->payment_date->format('d M Y') }}</td>
          <td>{{ $p->paidBy->name ?? 'System' }}</td>
          <td>
            <a href="{{ route('investments.voucher.view', $p) }}" class="btn btn-sm btn-outline-success">
              <i class="bi bi-receipt"></i>
            </a>
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="text-center text-muted py-4">কোনো পেমেন্ট নেই।</td></tr>
      @endforelse
      </tbody>
      @if($payments->count())
      <tfoot class="table-light fw-bold">
        <tr>
          <td colspan="4" class="text-end">মোট বিতরণ:</td>
          <td class="text-success">৳{{ number_format($totalPaid, 2) }}</td>
          <td colspan="4"></td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection
