@extends('layouts.app')
@section('title','লাভ/ক্ষতি রিপোর্ট')
@section('page-title','বিনিয়োগ লাভ/ক্ষতি রিপোর্ট')
@section('content')
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}"></div>
      <div class="col-md-2"><input type="date" name="date_to"   class="form-control form-control-sm" value="{{ request('date_to') }}"></div>
      <div class="col-md-2">
        <select name="outcome" class="form-select form-select-sm">
          <option value="">সব ফলাফল</option>
          <option value="profit"   {{ request('outcome')==='profit'  ?'selected':'' }}>লাভ</option>
          <option value="loss"     {{ request('outcome')==='loss'    ?'selected':'' }}>ক্ষতি</option>
          <option value="breakeven"{{ request('outcome')==='breakeven'?'selected':'' }}>সমতা</option>
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-search me-1"></i>ফিল্টার</button></div>
      <div class="col-md-2"><a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-danger btn-sm w-100"><i class="bi bi-file-pdf me-1"></i>PDF</a></div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="card text-center p-3" style="border-left:4px solid #27ae60">
    <div class="text-muted small">মোট লাভ</div>
    <div class="fs-4 fw-bold text-success">৳{{ number_format($totalProfit,2) }}</div>
  </div></div>
  <div class="col-md-4"><div class="card text-center p-3" style="border-left:4px solid #e74c3c">
    <div class="text-muted small">মোট ক্ষতি</div>
    <div class="fs-4 fw-bold text-danger">৳{{ number_format(abs($totalLoss),2) }}</div>
  </div></div>
  <div class="col-md-4"><div class="card text-center p-3" style="border-left:4px solid #2e86c1">
    <div class="text-muted small">নিট লাভ/ক্ষতি</div>
    <div class="fs-4 fw-bold {{ $netProfitLoss>=0?'text-success':'text-danger' }}">
      {{ $netProfitLoss>=0?'+':'' }}৳{{ number_format($netProfitLoss,2) }}
    </div>
  </div></div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>সদস্য</th><th>প্রকল্প</th><th>বিনিয়োগ</th><th>লাভ/ক্ষতি</th><th>রিটার্ন</th><th>ফলাফল</th><th>তারিখ</th></tr>
      </thead>
      <tbody>
      @forelse($settlements as $i=>$s)
        <tr>
          <td>{{ $i+1 }}</td>
          <td><div class="fw-semibold">{{ $s->investmentRequest->member->name }}</div><small class="text-muted">{{ $s->investmentRequest->member->member_id }}</small></td>
          <td>{{  \Illuminate\Support\Str::limit($s->investmentRequest->project_name,30) }}</td>
          <td>৳{{ number_format($s->investment_amount,2) }}</td>
          <td class="{{ $s->actual_profit_loss>=0?'text-success':'text-danger' }} fw-bold">
            {{ $s->actual_profit_loss>=0?'+':'' }}৳{{ number_format($s->actual_profit_loss,2) }}
          </td>
          <td class="fw-bold text-primary">৳{{ number_format($s->return_amount,2) }}</td>
          <td><span class="badge bg-{{ $s->outcome==='profit'?'success':($s->outcome==='loss'?'danger':'secondary') }}">{{ $s->outcome_label }}</span></td>
          <td>{{ $s->settlement_date->format('d M Y') }}</td>
        </tr>
      @empty
        <tr><td colspan="8" class="text-center text-muted py-4">কোনো নিষ্পত্তি নেই।</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
