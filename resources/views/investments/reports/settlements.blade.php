@extends('layouts.app')
@section('title','নিষ্পত্তি রিপোর্ট')
@section('page-title','বিনিয়োগ নিষ্পত্তি রিপোর্ট')
@section('content')
<div class="d-flex justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-check2-circle me-2 text-dark"></i>নিষ্পত্তি রিপোর্ট</h5>
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
  <div class="col-md-4"><div class="card text-center p-3"><div class="text-muted small">মোট নিষ্পন্ন</div><div class="fs-4 fw-bold text-primary">{{ $settlements->count() }}</div></div></div>
  <div class="col-md-4"><div class="card text-center p-3"><div class="text-muted small">মোট রিটার্ন</div><div class="fs-4 fw-bold text-success">৳{{ number_format($totalReturned, 0) }}</div></div></div>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>সদস্য</th><th>প্রকল্প</th><th>বিনিয়োগ</th><th>লাভ/ক্ষতি</th><th>রিটার্ন</th><th>ফলাফল</th><th>তারিখ</th><th>ভাউচার</th></tr>
      </thead>
      <tbody>
      @forelse($settlements as $i => $s)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>
            <div class="fw-semibold">{{ $s->investmentRequest->member->name }}</div>
            <small class="text-muted">{{ $s->investmentRequest->member->member_id }}</small>
          </td>
          <td>{{  \Illuminate\Support\Str::limit($s->investmentRequest->project_name, 28) }}</td>
          <td>৳{{ number_format($s->investment_amount, 2) }}</td>
          <td class="{{ $s->actual_profit_loss >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
            {{ $s->actual_profit_loss >= 0 ? '+' : '' }}৳{{ number_format($s->actual_profit_loss, 2) }}
          </td>
          <td class="text-primary fw-bold">৳{{ number_format($s->return_amount, 2) }}</td>
          <td>
            <span class="badge bg-{{ $s->outcome === 'profit' ? 'success' : ($s->outcome === 'loss' ? 'danger' : 'secondary') }}">
              {{ $s->outcome_label }}
            </span>
          </td>
          <td>{{ $s->settlement_date->format('d M Y') }}</td>
          <td>
            <a href="{{ route('investments.settlement.voucher', $s) }}" class="btn btn-sm btn-outline-dark">
              <i class="bi bi-receipt"></i>
            </a>
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="text-center text-muted py-4">কোনো নিষ্পত্তি নেই।</td></tr>
      @endforelse
      </tbody>
      @if($settlements->count())
      <tfoot class="table-light fw-bold">
        <tr>
          <td colspan="5" class="text-end">মোট রিটার্ন:</td>
          <td class="text-success">৳{{ number_format($totalReturned, 2) }}</td>
          <td colspan="3"></td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection
