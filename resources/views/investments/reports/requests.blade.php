@extends('layouts.app')
@section('title','বিনিয়োগ আবেদন রিপোর্ট')
@section('page-title','বিনিয়োগ আবেদন রিপোর্ট')
@section('content')
<div class="d-flex justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>সকল বিনিয়োগ আবেদন</h5>
  <a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-danger btn-sm">
    <i class="bi bi-file-pdf me-1"></i>PDF
  </a>
</div>
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="From"></div>
      <div class="col-md-2"><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="To"></div>
      <div class="col-md-2">
        <select name="status" class="form-select form-select-sm">
          <option value="">সব অবস্থা</option>
          <option value="pending"   {{ request('status')==='pending'  ?'selected':'' }}>বিবেচনাধীন</option>
          <option value="approved"  {{ request('status')==='approved' ?'selected':'' }}>অনুমোদিত</option>
          <option value="active"    {{ request('status')==='active'   ?'selected':'' }}>সক্রিয়</option>
          <option value="matured"   {{ request('status')==='matured'  ?'selected':'' }}>মেয়াদ শেষ</option>
          <option value="closed"    {{ request('status')==='closed'   ?'selected':'' }}>নিষ্পন্ন</option>
          <option value="rejected"  {{ request('status')==='rejected' ?'selected':'' }}>প্রত্যাখ্যাত</option>
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-search me-1"></i>ফিল্টার</button></div>
      <div class="col-md-2"><a href="{{ route('investments.reports.requests') }}" class="btn btn-outline-secondary btn-sm w-100">রিসেট</a></div>
    </form>
  </div>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>সদস্য</th><th>প্রকল্পের নাম</th><th>চাওয়া পরিমাণ</th><th>অনুমোদিত</th><th>মেয়াদ</th><th>অবস্থা</th><th>তারিখ</th><th></th></tr>
      </thead>
      <tbody>
      @forelse($data as $i => $inv)
        <tr>
          <td class="text-muted">{{ $i+1 }}</td>
          <td>
            <div class="fw-semibold">{{ $inv->member->name }}</div>
            <small class="text-muted">{{ $inv->member->member_id }}</small>
          </td>
          <td>{{  \Illuminate\Support\Str::limit($inv->project_name, 35) }}</td>
          <td>৳{{ number_format($inv->requested_amount, 0) }}</td>
          <td>{{ $inv->approved_amount ? '৳'.number_format($inv->approved_amount, 0) : '—' }}</td>
          <td>{{ $inv->duration_months }} মাস</td>
          <td><span class="badge bg-{{ $inv->status_color }}">{{ $inv->status_label }}</span></td>
          <td>{{ $inv->submitted_date->format('d M Y') }}</td>
          <td>
            <a href="{{ route('investments.show', $inv) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="text-center text-muted py-4">কোনো আবেদন নেই।</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
