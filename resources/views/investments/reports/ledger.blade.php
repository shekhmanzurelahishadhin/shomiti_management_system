@extends('layouts.app')
@section('title','সদস্যভিত্তিক লেজার')
@section('page-title','সদস্যভিত্তিক বিনিয়োগ লেজার')
@section('content')
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-5">
        <select name="member_id" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">— সদস্য নির্বাচন করুন —</option>
          @foreach($members as $m)
            <option value="{{ $m->id }}" {{ request('member_id')==$m->id?'selected':'' }}>{{ $m->name }} ({{ $m->member_id }})</option>
          @endforeach
        </select>
      </div>
      @if($member)
      <div class="col-md-2">
        <a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-danger btn-sm w-100"><i class="bi bi-file-pdf me-1"></i>PDF</a>
      </div>
      @endif
    </form>
  </div>
</div>

@if($member)
<div class="card mb-3">
  <div class="card-body py-3">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h5 class="fw-bold mb-1">{{ $member->name }}</h5>
        <div class="text-muted">{{ $member->member_id }} | {{ $member->phone ?? 'ফোন নেই' }}</div>
      </div>
      <div class="col-md-4 text-end">
        <div class="text-muted small">মোট বিনিয়োগ</div>
        <div class="fs-4 fw-bold text-primary">{{ $ledger->count() }}টি</div>
      </div>
    </div>
  </div>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>প্রকল্প</th><th>পরিমাণ</th><th>লাভ হার</th><th>অবস্থা</th><th>পেমেন্ট</th><th>নিষ্পত্তি</th><th>নিট লাভ/ক্ষতি</th></tr>
      </thead>
      <tbody>
      @forelse($ledger as $i=>$inv)
        <tr>
          <td>{{ $i+1 }}</td>
          <td><div class="fw-semibold">{{ $inv->project_name }}</div><small class="text-muted">{{ $inv->submitted_date->format('d M Y') }}</small></td>
          <td>৳{{ number_format($inv->approved_amount ?? $inv->requested_amount,2) }}</td>
          <td>{{ $inv->approved_profit_ratio ?? $inv->expected_profit_ratio }}%</td>
          <td><span class="badge bg-{{ $inv->status_color }}">{{ $inv->status_label }}</span></td>
          <td>{{ $inv->payment ? '৳'.number_format($inv->payment->amount,2) : '—' }}</td>
          <td>{{ $inv->settlement ? '৳'.number_format($inv->settlement->return_amount,2) : '—' }}</td>
          <td class="{{ ($inv->settlement?->actual_profit_loss ?? 0)>=0?'text-success':'text-danger' }} fw-bold">
            @if($inv->settlement)
              {{ $inv->settlement->actual_profit_loss>=0?'+':'' }}৳{{ number_format($inv->settlement->actual_profit_loss,2) }}
            @else —
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="8" class="text-center text-muted py-4">এই সদস্যের কোনো বিনিয়োগ নেই।</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@else
<div class="alert alert-info">একটি সদস্য নির্বাচন করুন।</div>
@endif
@endsection
