@extends('layouts.app')
@section('title','বিনিয়োগ')
@section('page-title','বিনিয়োগ ব্যবস্থাপনা')
@section('content')

{{-- Stats --}}
<div class="row g-3 mb-3">
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #f39c12">
      <div class="text-muted small">বিবেচনাধীন</div>
      <div class="fs-4 fw-bold text-warning">{{ $stats['pending'] }}</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #007bff">
      <div class="text-muted small">অনুমোদিত (অপেক্ষায়)</div>
      <div class="fs-4 fw-bold text-primary">{{ $stats['approved'] }}</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #27ae60">
      <div class="text-muted small">সক্রিয় বিনিয়োগ</div>
      <div class="fs-4 fw-bold text-success">{{ $stats['active'] }}</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #1a5276">
      <div class="text-muted small">মোট সক্রিয় অর্থ</div>
      <div class="fs-4 fw-bold text-primary">৳{{ number_format($stats['total_active_amount'],0) }}</div>
    </div>
  </div>
</div>

{{-- Matured alert --}}
@if($stats['matured'] > 0)
<div class="alert alert-dark py-2 d-flex justify-content-between align-items-center mb-3">
  <span><i class="bi bi-alarm me-2"></i><strong>{{ $stats['matured'] }}</strong>টি বিনিয়োগের মেয়াদ শেষ — নিষ্পত্তির অপেক্ষায়</span>
  <a href="{{ route('investments.index', ['status'=>'matured']) }}" class="btn btn-dark btn-sm">দেখুন</a>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2 text-success"></i>বিনিয়োগ আবেদন তালিকা</h5>
  <div class="d-flex gap-2">
    @can('manage investment agenda')
    <a href="{{ route('investments.meeting.list') }}" class="btn btn-info btn-sm text-white">
      <i class="bi bi-calendar-event me-1"></i>সভার এজেন্ডা
    </a>
    @endcan
    @can('process investment payment')
    <form method="POST" action="{{ route('investments.check-maturities') }}" class="d-inline">
      @csrf
      <button class="btn btn-dark btn-sm" title="মেয়াদ পরীক্ষা করুন">
        <i class="bi bi-alarm me-1"></i>মেয়াদ চেক
      </button>
    </form>
    @endcan
    <a href="{{ route('investments.reports.index') }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-bar-chart me-1"></i>রিপোর্ট
    </a>
    @can('submit investment request')
    <a href="{{ route('investments.create') }}" class="btn btn-success btn-sm">
      <i class="bi bi-plus me-1"></i>নতুন আবেদন
    </a>
    @endcan
  </div>
</div>

{{-- Filter --}}
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="প্রকল্পের নাম বা সদস্য..." value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <select name="status" class="form-select form-select-sm">
          <option value="">সব অবস্থা</option>
          @foreach(['pending'=>'বিবেচনাধীন','in_agenda'=>'এজেন্ডায়','approved'=>'অনুমোদিত','rejected'=>'প্রত্যাখ্যাত','modification_needed'=>'সংশোধন','active'=>'সক্রিয়','matured'=>'মেয়াদ শেষ','closed'=>'নিষ্পন্ন'] as $v=>$l)
            <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
      </div>
      <div class="col-md-2">
        <input type="date" name="date_to"   class="form-control form-control-sm" value="{{ request('date_to') }}">
      </div>
      <div class="col-md-1">
        <button class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-search"></i></button>
      </div>
      <div class="col-md-2">
        <a href="{{ route('investments.index') }}" class="btn btn-outline-secondary btn-sm w-100">রিসেট</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th><th>সদস্য</th><th>প্রকল্প</th><th>চাওয়া</th><th>অনুমোদিত</th>
          <th>মেয়াদ</th><th>রিটার্ন তারিখ</th><th>অবস্থা</th><th class="text-end">অ্যাকশন</th>
        </tr>
      </thead>
      <tbody>
      @forelse($requests as $inv)
        <tr class="{{ $inv->is_matured && $inv->status==='active' ? 'table-warning' : '' }}">
          <td class="text-muted small">{{ $inv->id }}</td>
          <td>
            <div class="fw-semibold">{{ $inv->member->name }}</div>
            <small class="text-muted">{{ $inv->member->member_id }}</small>
          </td>
          <td>
            <div class="fw-semibold">{{  \Illuminate\Support\Str::limit($inv->project_name,35) }}</div>
            <small class="text-muted">{{ $inv->submitted_date->format('d M Y') }}</small>
          </td>
          <td>৳{{ number_format($inv->requested_amount,0) }}</td>
          <td>{{ $inv->approved_amount ? '৳'.number_format($inv->approved_amount,0) : '—' }}</td>
          <td>{{ $inv->approved_duration_months ?? $inv->duration_months }} মাস</td>
          <td>
            @if($inv->approved_return_date)
              <span class="{{ $inv->is_matured ? 'text-danger fw-semibold' : '' }}">
                {{ $inv->approved_return_date->format('d M Y') }}
              </span>
              @if($inv->status === 'active' && !$inv->is_matured)
                <div class="text-muted" style="font-size:.72rem">{{ $inv->days_remaining }} দিন বাকি</div>
              @endif
            @else
              {{ $inv->expected_return_date->format('d M Y') }}
            @endif
          </td>
          <td>
            <span class="badge bg-{{ $inv->status_color }}">{{ $inv->status_label }}</span>
            @if($inv->is_matured && $inv->status==='active')
              <div><span class="badge bg-warning text-dark" style="font-size:.68rem">মেয়াদ শেষ!</span></div>
            @endif
          </td>
          <td class="text-end">
            <a href="{{ route('investments.show', $inv) }}" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-eye"></i>
            </a>
            @if($inv->status === 'approved' && !$inv->payment)
              @can('process investment payment')
              <a href="{{ route('investments.payment', $inv) }}" class="btn btn-sm btn-outline-success" title="পেমেন্ট">
                <i class="bi bi-cash-coin"></i>
              </a>
              @endcan
            @endif
            @if(in_array($inv->status, ['active','matured']) && !$inv->settlement)
              @can('settle investments')
              <a href="{{ route('investments.settlement', $inv) }}" class="btn btn-sm btn-outline-dark" title="নিষ্পত্তি">
                <i class="bi bi-check2-circle"></i>
              </a>
              @endcan
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="text-center text-muted py-4">কোনো বিনিয়োগ আবেদন নেই।</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($requests->hasPages())
    <div class="card-footer">{{ $requests->links() }}</div>
  @endif
</div>
@endsection
