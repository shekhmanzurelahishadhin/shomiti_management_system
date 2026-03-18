@extends('layouts.app')
@section('title','সক্রিয় বিনিয়োগ')
@section('page-title','সক্রিয় বিনিয়োগ রিপোর্ট')
@section('content')
<div class="d-flex justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-graph-up-arrow me-2 text-success"></i>সক্রিয় বিনিয়োগ</h5>
  <a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-danger btn-sm">
    <i class="bi bi-file-pdf me-1"></i>PDF
  </a>
</div>
<div class="row g-2 mb-3">
  <div class="col-md-4">
    <div class="card text-center p-3"><div class="text-muted small">সক্রিয় বিনিয়োগ</div>
      <div class="fs-4 fw-bold text-success">{{ $investments->count() }}</div></div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-3"><div class="text-muted small">মোট বিনিয়োগ</div>
      <div class="fs-4 fw-bold text-primary">৳{{ number_format($totalActive,0) }}</div></div>
  </div>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>সদস্য</th><th>প্রকল্প</th><th>পরিমাণ</th><th>লাভের হার</th><th>শুরু</th><th>মেয়াদ শেষ</th><th>বাকি</th><th>প্রত্যাশিত রিটার্ন</th></tr>
      </thead>
      <tbody>
      @forelse($investments as $i=>$inv)
        <tr class="{{ $inv->is_matured ? 'table-warning' : '' }}">
          <td>{{ $i+1 }}</td>
          <td><div class="fw-semibold">{{ $inv->member->name }}</div><small class="text-muted">{{ $inv->member->member_id }}</small></td>
          <td>{{  \Illuminate\Support\Str::limit($inv->project_name,30) }}</td>
          <td class="fw-bold">৳{{ number_format($inv->approved_amount,2) }}</td>
          <td>{{ $inv->approved_profit_ratio }}%</td>
          <td>{{ $inv->approved_start_date?->format('d M Y') ?? '—' }}</td>
          <td class="{{ $inv->is_matured?'text-danger fw-bold':'' }}">{{ $inv->approved_return_date?->format('d M Y') ?? '—' }}</td>
          <td class="{{ $inv->days_remaining<=7?'text-danger fw-semibold':'' }}">{{ $inv->days_remaining }} দিন</td>
          <td class="text-success fw-bold">৳{{ number_format($inv->expected_return_amount,2) }}</td>
        </tr>
      @empty
        <tr><td colspan="9" class="text-center text-muted py-4">কোনো সক্রিয় বিনিয়োগ নেই।</td></tr>
      @endforelse
      </tbody>
      @if($investments->count())
      <tfoot class="table-light fw-bold">
        <tr><td colspan="3" class="text-end">মোট:</td><td>৳{{ number_format($totalActive,2) }}</td><td colspan="5"></td></tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection
