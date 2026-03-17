@extends('layouts.app')
@section('title','নির্বাচন')
@section('page-title','কমিটি নির্বাচন ব্যবস্থাপনা')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>বার্ষিক কমিটি নির্বাচন</h5>
  @can('manage committees')
  <a href="{{ route('elections.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus me-1"></i>নতুন নির্বাচন
  </a>
  @endcan
</div>

@if($elections->isEmpty())
  <div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>এখনো কোনো নির্বাচন তৈরি হয়নি।
    @can('manage committees')
      <a href="{{ route('elections.create') }}" class="alert-link">নতুন নির্বাচন তৈরি করুন।</a>
    @endcan
  </div>
@else
<div class="row g-3">
@foreach($elections as $election)
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold">{{ $election->election_year }} সালের নির্বাচন</span>
        <span class="badge bg-{{ $election->status_color }}">{{ $election->status_label }}</span>
      </div>
      <div class="card-body">
        <h6 class="fw-bold mb-2">{{ $election->title }}</h6>
        @if($election->description)
          <p class="text-muted small mb-3">{{ Str::limit($election->description, 80) }}</p>
        @endif

        <div class="row g-2 text-center mb-3">
          <div class="col-4">
            <div class="text-muted" style="font-size:.7rem">পদ সংখ্যা</div>
            <div class="fw-bold text-primary">{{ $election->positions_count }}</div>
          </div>
          <div class="col-4">
            <div class="text-muted" style="font-size:.7rem">প্রার্থী</div>
            <div class="fw-bold text-info">{{ $election->candidates_count }}</div>
          </div>
          <div class="col-4">
            <div class="text-muted" style="font-size:.7rem">ভোট</div>
            <div class="fw-bold text-success">{{ $election->votes_count }}</div>
          </div>
        </div>

        <div class="small text-muted mb-1">
          <i class="bi bi-calendar-event me-1"></i>
          মনোনয়ন: {{ $election->nomination_start->format('d M') }} — {{ $election->nomination_end->format('d M Y') }}
        </div>
        <div class="small text-muted">
          <i class="bi bi-check-square me-1"></i>
          ভোটগ্রহণ: {{ $election->voting_start->format('d M') }} — {{ $election->voting_end->format('d M Y') }}
        </div>
      </div>
      <div class="card-footer d-flex gap-2">
        <a href="{{ route('elections.show', $election) }}" class="btn btn-sm btn-outline-primary flex-fill">
          <i class="bi bi-eye me-1"></i>বিস্তারিত
        </a>
        @if($election->status === 'completed')
        <a href="{{ route('elections.results', $election) }}" class="btn btn-sm btn-outline-success flex-fill">
          <i class="bi bi-trophy me-1"></i>ফলাফল
        </a>
        @endif
        @can('manage committees')
        <form method="POST" action="{{ route('elections.destroy', $election) }}"
              onsubmit="return confirm('নির্বাচন মুছে ফেলবেন?')">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form>
        @endcan
      </div>
    </div>
  </div>
@endforeach
</div>

@if($elections->hasPages())
  <div class="mt-3">{{ $elections->links() }}</div>
@endif
@endif
@endsection
