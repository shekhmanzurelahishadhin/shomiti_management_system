@extends('layouts.app')
@section('title','সভার এজেন্ডা')
@section('page-title','বিনিয়োগ সভার এজেন্ডা')
@section('content')

<div class="row g-3">
  {{-- Left: Create Meeting --}}
  <div class="col-lg-5">
    <div class="card mb-3">
      <div class="card-header py-3 fw-semibold" style="background:linear-gradient(135deg,#1a5276,#2e86c1);color:#fff">
        <i class="bi bi-calendar-plus me-2"></i>নতুন সভার এজেন্ডা তৈরি
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('investments.meeting.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-semibold">সভার শিরোনাম *</label>
            <input type="text" name="title" class="form-control" required
                   placeholder="যেমন: ২০২৫ সালের ৩য় বিনিয়োগ সভা">
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold">সভার তারিখ *</label>
              <input type="date" name="meeting_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">স্থান</label>
              <input type="text" name="venue" class="form-control" placeholder="অফিস কক্ষ...">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">নোট</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>

          {{-- Add pending requests to agenda --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">এজেন্ডায় যোগ করুন</label>
            @if($pendingRequests->isEmpty())
              <div class="alert alert-info py-2 small">কোনো বিবেচনাধীন আবেদন নেই।</div>
            @else
            <div class="border rounded p-2" style="max-height:200px;overflow-y:auto">
              @foreach($pendingRequests as $req)
              <div class="form-check mb-1">
                <input class="form-check-input" type="checkbox" name="request_ids[]"
                       value="{{ $req->id }}" id="req_{{ $req->id }}">
                <label class="form-check-label small" for="req_{{ $req->id }}">
                  <strong>{{ $req->member->name }}</strong> — {{ $req->project_name }}
                  <span class="text-muted">(৳{{ number_format($req->requested_amount,0) }})</span>
                </label>
              </div>
              @endforeach
            </div>
            @endif
          </div>

          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-calendar-check me-1"></i>সভা তৈরি করুন
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Right: Meeting List --}}
  <div class="col-lg-7">
    <h6 class="fw-bold mb-3">সভার তালিকা</h6>
    @forelse($meetings as $meeting)
    <div class="card mb-3">
      <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <div>
          <span class="fw-semibold">{{ $meeting->title }}</span>
          <span class="badge bg-{{ $meeting->status_color }} ms-2">{{ $meeting->status_label }}</span>
        </div>
        <small class="text-muted">{{ $meeting->meeting_date->format('d M Y') }}</small>
      </div>
      <div class="card-body py-2 d-flex justify-content-between align-items-center">
        <span class="text-muted small">{{ $meeting->items_count }} টি আবেদন &nbsp;|&nbsp; {{ $meeting->venue ?? 'স্থান উল্লেখ নেই' }}</span>
        <a href="{{ route('investments.meeting.show', $meeting) }}" class="btn btn-sm btn-outline-primary">
          <i class="bi bi-eye me-1"></i>বিস্তারিত
        </a>
      </div>
    </div>
    @empty
    <div class="alert alert-info">কোনো সভা নেই।</div>
    @endforelse
    @if($meetings->hasPages())
      {{ $meetings->links() }}
    @endif
  </div>
</div>
@endsection
