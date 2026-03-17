@extends('layouts.app')
@section('title','নির্বাচনের ফলাফল')
@section('page-title','নির্বাচনের ফলাফল')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h5 class="mb-0 fw-bold"><i class="bi bi-trophy me-2 text-warning"></i>{{ $election->title }}</h5>
    <small class="text-muted">{{ $election->election_year }} সালের কমিটি নির্বাচন — চূড়ান্ত ফলাফল</small>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('elections.show', $election) }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>ফিরে যান
    </a>
    <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
      <i class="bi bi-printer me-1"></i>প্রিন্ট
    </button>
  </div>
</div>

{{-- Summary --}}
<div class="alert alert-success border-0 mb-4" style="background:#d1e7dd">
  <div class="row text-center g-2">
    <div class="col-4">
      <div class="text-muted small">মোট ভোটার</div>
      <div class="fw-bold fs-5">{{ $election->votes->unique('voter_member_id')->count() }}</div>
    </div>
    <div class="col-4">
      <div class="text-muted small">মোট ভোট</div>
      <div class="fw-bold fs-5">{{ $election->votes->count() }}</div>
    </div>
    <div class="col-4">
      <div class="text-muted small">নির্বাচিত সদস্য</div>
      <div class="fw-bold fs-5">{{ $election->results->where('is_elected',true)->count() }}</div>
    </div>
  </div>
</div>

{{-- Results by Position --}}
@foreach($election->positions as $position)
@php
  $posResults = $positionResults->get($position->id, collect())->sortByDesc('vote_count');
  $totalVotes = $posResults->sum('vote_count');
@endphp
<div class="card mb-4">
  <div class="card-header py-3 d-flex justify-content-between align-items-center"
       style="background:linear-gradient(135deg,#1a5276,#2e86c1);color:#fff">
    <span class="fw-bold fs-6"><i class="bi bi-person-badge me-2"></i>{{ $position->position_name }}</span>
    <span class="badge bg-light text-dark">{{ $position->seats }}টি আসন</span>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>ক্রম</th><th>প্রার্থী</th><th>ভোট সংখ্যা</th><th>শতাংশ</th><th>ফলাফল</th></tr>
      </thead>
      <tbody>
      @forelse($posResults as $idx => $result)
        <tr class="{{ $result->is_elected ? 'table-success' : '' }}">
          <td>{{ $idx + 1 }}</td>
          <td>
            <div class="d-flex align-items-center gap-2">
              @if($result->is_elected)
                <i class="bi bi-trophy-fill text-warning fs-5"></i>
              @endif
              <div>
                <div class="fw-bold {{ $result->is_elected ? 'text-success' : '' }}">
                  {{ $result->member->name }}
                </div>
                <small class="text-muted">{{ $result->member->member_id }}</small>
              </div>
            </div>
          </td>
          <td>
            <span class="fw-bold fs-5">{{ $result->vote_count }}</span>
            @if($totalVotes > 0)
            <div class="progress mt-1" style="height:6px;max-width:150px">
              <div class="progress-bar bg-primary"
                   style="width:{{ $totalVotes > 0 ? round($result->vote_count/$totalVotes*100) : 0 }}%"></div>
            </div>
            @endif
          </td>
          <td>
            {{ $totalVotes > 0 ? round($result->vote_count/$totalVotes*100,1) : 0 }}%
          </td>
          <td>
            @if($result->is_elected)
              <span class="badge bg-success px-3 py-2">
                <i class="bi bi-check-circle me-1"></i>নির্বাচিত
              </span>
            @else
              <span class="badge bg-secondary">বিজয়ী নন</span>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center text-muted py-3">কোনো ফলাফল নেই।</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endforeach

{{-- Elected Committee Summary --}}
<div class="card border-success">
  <div class="card-header py-3 bg-success text-white fw-bold">
    <i class="bi bi-people-fill me-2"></i>নির্বাচিত কমিটি {{ $election->election_year }}
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr><th>পদ</th><th>নির্বাচিত সদস্য</th><th>সদস্য আইডি</th><th>ফোন</th><th>প্রাপ্ত ভোট</th></tr>
      </thead>
      <tbody>
      @foreach($election->positions as $position)
        @foreach($positionResults->get($position->id, collect())->where('is_elected',true) as $result)
        <tr>
          <td class="fw-semibold text-primary">{{ $position->position_name }}</td>
          <td class="fw-bold">{{ $result->member->name }}</td>
          <td><span class="badge bg-light text-dark">{{ $result->member->member_id }}</span></td>
          <td>{{ $result->member->phone ?? '—' }}</td>
          <td><span class="badge bg-success">{{ $result->vote_count }}</span></td>
        </tr>
        @endforeach
      @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer text-muted small text-center">
    নবদিগন্ত সমবায় সমিতি — {{ $election->election_year }} সালের বার্ষিক নির্বাচন
    — ভোটগ্রহণ: {{ $election->voting_start->format('d M') }} থেকে {{ $election->voting_end->format('d M Y') }}
  </div>
</div>
@endsection
