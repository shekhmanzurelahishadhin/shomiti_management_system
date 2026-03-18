@extends('layouts.app')
@section('title','সভার বিস্তারিত')
@section('page-title','সভার বিস্তারিত')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h5 class="mb-0 fw-bold">{{ $meeting->title }}</h5>
    <small class="text-muted">{{ $meeting->meeting_date->format('d M Y') }} {{ $meeting->venue ? '— '.$meeting->venue : '' }}</small>
  </div>
  <div class="d-flex gap-2 align-items-center">
    <span class="badge bg-{{ $meeting->status_color }} px-3 py-2">{{ $meeting->status_label }}</span>
    @can('manage investment agenda')
    <div class="dropdown">
      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">অবস্থা পরিবর্তন</button>
      <ul class="dropdown-menu">
        @foreach(['scheduled'=>'নির্ধারিত','held'=>'অনুষ্ঠিত','cancelled'=>'বাতিল'] as $s=>$l)
        <li>
          <form method="POST" action="{{ route('investments.meeting.status', $meeting) }}">
            @csrf <input type="hidden" name="status" value="{{ $s }}">
            <button class="dropdown-item {{ $meeting->status===$s?'active':'' }}">{{ $l }}</button>
          </form>
        </li>
        @endforeach
      </ul>
    </div>
    @endcan
  </div>
</div>

@if($meeting->notes)
<div class="alert alert-light mb-3"><i class="bi bi-sticky me-2"></i>{{ $meeting->notes }}</div>
@endif

<div class="card">
  <div class="card-header py-3 fw-semibold d-flex justify-content-between align-items-center">
    <span><i class="bi bi-list-ol me-2 text-primary"></i>এজেন্ডা আইটেম ({{ $meeting->items->count() }})</span>
    @can('manage investment agenda')
    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#addItemForm">
      <i class="bi bi-plus me-1"></i>আবেদন যোগ
    </button>
    @endcan
  </div>

  @can('manage investment agenda')
  <div class="collapse" id="addItemForm">
    <div class="card-body border-bottom bg-light">
      <form method="POST" action="{{ route('investments.meeting.add', $meeting) }}" class="row g-2 align-items-end">
        @csrf
        <div class="col-md-9">
          <select name="request_id" class="form-select form-select-sm" required>
            <option value="">— বিনিয়োগ আবেদন নির্বাচন করুন —</option>
            @foreach(\App\Models\InvestmentRequest::whereIn('status',['pending','in_agenda'])->with('member')->get() as $req)
              <option value="{{ $req->id }}">{{ $req->member->name }} — {{ $req->project_name }} (৳{{ number_format($req->requested_amount,0) }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <button class="btn btn-primary btn-sm w-100"><i class="bi bi-plus me-1"></i>যোগ করুন</button>
        </div>
      </form>
    </div>
  </div>
  @endcan

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>সদস্য</th><th>প্রকল্প</th><th>চাওয়া পরিমাণ</th><th>মেয়াদ</th><th>লাভের হার</th><th>সিদ্ধান্ত</th><th class="text-end">অ্যাকশন</th></tr>
      </thead>
      <tbody>
      @forelse($meeting->items->sortBy('agenda_order') as $item)
        <tr>
          <td class="fw-bold text-muted">{{ $item->agenda_order }}</td>
          <td>
            <div class="fw-semibold">{{ $item->investmentRequest->member->name }}</div>
            <small class="text-muted">{{ $item->investmentRequest->member->member_id }}</small>
          </td>
          <td>
            <div>{{  \Illuminate\Support\Str::limit($item->investmentRequest->project_name,35) }}</div>
            <small class="text-muted">{{ $item->investmentRequest->submitted_date->format('d M Y') }}</small>
          </td>
          <td class="fw-bold">৳{{ number_format($item->investmentRequest->requested_amount,0) }}</td>
          <td>{{ $item->investmentRequest->duration_months }} মাস</td>
          <td>{{ $item->investmentRequest->expected_profit_ratio }}%</td>
          <td>
            @php $dc=['pending'=>'warning','approved'=>'success','rejected'=>'danger','modification_needed'=>'secondary']; @endphp
            <span class="badge bg-{{ $dc[$item->decision]??'secondary' }}">{{ $item->decision_label }}</span>
            @if($item->decision_note)
              <div class="text-muted" style="font-size:.72rem">{{  \Illuminate\Support\Str::limit($item->decision_note,40) }}</div>
            @endif
          </td>
          <td class="text-end">
            <a href="{{ route('investments.show', $item->investmentRequest) }}" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-eye"></i>
            </a>
          </td>
        </tr>
      @empty
        <tr><td colspan="8" class="text-center text-muted py-4">এই সভায় কোনো আবেদন নেই।</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
