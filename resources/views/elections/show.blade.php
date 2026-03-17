@extends('layouts.app')
@section('title','নির্বাচন বিস্তারিত')
@section('page-title','নির্বাচন বিস্তারিত')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h5 class="mb-0 fw-bold">{{ $election->title }}</h5>
    <small class="text-muted">{{ $election->election_year }} সালের কমিটি নির্বাচন</small>
  </div>
  <div class="d-flex gap-2 align-items-center">
    <span class="badge bg-{{ $election->status_color }} px-3 py-2 fs-6">{{ $election->status_label }}</span>
    @can('manage committees')
    <div class="dropdown">
      <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
        <i class="bi bi-gear me-1"></i>অবস্থা পরিবর্তন
      </button>
      <ul class="dropdown-menu">
        @foreach(['upcoming'=>'আসন্ন','nomination'=>'মনোনয়ন শুরু','voting'=>'ভোটগ্রহণ শুরু','counting'=>'গণনা','completed'=>'সম্পন্ন','cancelled'=>'বাতিল'] as $s=>$l)
        <li>
          <form method="POST" action="{{ route('elections.status', $election) }}">
            @csrf
            <input type="hidden" name="status" value="{{ $s }}">
            <button class="dropdown-item {{ $election->status===$s?'active':'' }}">{{ $l }}</button>
          </form>
        </li>
        @endforeach
      </ul>
    </div>
    @endcan
    @if($election->status === 'counting')
    @can('manage committees')
    <form method="POST" action="{{ route('elections.count', $election) }}"
          onsubmit="return confirm('ভোট গণনা করে ফলাফল প্রকাশ করবেন?')">
      @csrf
      <button class="btn btn-success btn-sm">
        <i class="bi bi-calculator me-1"></i>ফলাফল প্রকাশ
      </button>
    </form>
    @endcan
    @endif
    @if($election->status === 'completed')
    <a href="{{ route('elections.results', $election) }}" class="btn btn-success btn-sm">
      <i class="bi bi-trophy me-1"></i>ফলাফল দেখুন
    </a>
    @endif
  </div>
</div>

{{-- Timeline --}}
<div class="card mb-3">
  <div class="card-body py-3">
    <div class="row text-center g-2">
      <div class="col-6 col-md-3">
        <div class="text-muted small">মনোনয়ন শুরু</div>
        <div class="fw-semibold">{{ $election->nomination_start->format('d M Y') }}</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="text-muted small">মনোনয়ন শেষ</div>
        <div class="fw-semibold">{{ $election->nomination_end->format('d M Y') }}</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="text-muted small">ভোটগ্রহণ শুরু</div>
        <div class="fw-semibold text-success">{{ $election->voting_start->format('d M Y') }}</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="text-muted small">ভোটগ্রহণ শেষ</div>
        <div class="fw-semibold text-success">{{ $election->voting_end->format('d M Y') }}</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  {{-- Left: Positions & Candidates --}}
  <div class="col-lg-7">
    @foreach($election->positions as $position)
    <div class="card mb-3">
      <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-person-circle me-2 text-primary"></i>{{ $position->position_name }}</span>
        <small class="text-muted">{{ $position->seats }}টি আসন</small>
      </div>

      {{-- Add candidate form --}}
      @can('manage committees')
      @if(in_array($election->status,['upcoming','nomination']))
      <div class="p-3 bg-light border-bottom">
        <form method="POST" action="{{ route('elections.add-candidate', $election) }}" class="row g-2 align-items-end">
          @csrf
          <input type="hidden" name="election_position_id" value="{{ $position->id }}">
          <div class="col-md-6">
            <select name="member_id" class="form-select form-select-sm" required>
              <option value="">— প্রার্থী নির্বাচন করুন —</option>
              @foreach($activeMembers as $m)
                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->member_id }})</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <input type="text" name="manifesto" class="form-control form-control-sm" placeholder="ইশতেহার (ঐচ্ছিক)">
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary btn-sm w-100"><i class="bi bi-plus"></i></button>
          </div>
        </form>
      </div>
      @endif
      @endcan

      {{-- Candidates list --}}
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead class="table-light">
            <tr><th>প্রার্থী</th><th>ভোট</th><th>অবস্থা</th><th></th></tr>
          </thead>
          <tbody>
          @forelse($position->candidates->where('status','approved') as $cand)
            <tr>
              <td>
                <div class="fw-semibold">{{ $cand->member->name }}</div>
                <small class="text-muted">{{ $cand->member->member_id }}</small>
                @if($cand->manifesto)
                  <div class="text-muted" style="font-size:.75rem">{{ Str::limit($cand->manifesto,60) }}</div>
                @endif
              </td>
              <td>
                <span class="badge bg-{{ $election->status==='completed'?'primary':'light text-dark' }} px-2">
                  {{ $cand->votes()->count() }}
                </span>
              </td>
              <td><span class="badge bg-success">অনুমোদিত</span></td>
              <td>
                @can('manage committees')
                <form method="POST" action="{{ route('elections.remove-candidate',[$election,$cand]) }}"
                      onsubmit="return confirm('প্রার্থী বাদ দেবেন?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button>
                </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-2 fst-italic">প্রার্থী নেই</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Right: Voting panel --}}
  <div class="col-lg-5">
    {{-- VOTING BOOTH --}}
    @if($election->isVotingOpen())
    <div class="card mb-3 border-success">
      <div class="card-header py-3 bg-success text-white fw-bold">
        <i class="bi bi-check-square me-2"></i>ভোট প্রদান করুন
      </div>
      <div class="card-body">
        @php
          $voterMemberField = null;
        @endphp

        <div class="mb-3">
          <label class="form-label fw-semibold">আপনার সদস্য আইডি</label>
          <select id="voterSelect" class="form-select" onchange="checkVoterEligibility()">
            <option value="">— আপনার নাম নির্বাচন করুন —</option>
            @foreach($activeMembers as $m)
              <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->member_id }})</option>
            @endforeach
          </select>
        </div>

        <form method="POST" action="{{ route('elections.vote', $election) }}" id="voteForm">
          @csrf
          <input type="hidden" name="voter_member_id" id="voterMemberInput">

          @foreach($election->positions as $position)
          @php $approvedCands = $position->candidates->where('status','approved'); @endphp
          @if($approvedCands->count())
          <div class="mb-3 p-3 bg-light rounded">
            <div class="fw-semibold mb-2">{{ $position->position_name }}</div>
            @foreach($approvedCands as $cand)
            <div class="form-check mb-1">
              <input class="form-check-input" type="radio"
                     name="votes[{{ $position->id }}]"
                     value="{{ $cand->id }}"
                     id="v_{{ $position->id }}_{{ $cand->id }}">
              <label class="form-check-label" for="v_{{ $position->id }}_{{ $cand->id }}">
                {{ $cand->member->name }}
                @if($cand->manifesto)<small class="text-muted ms-2">— {{ Str::limit($cand->manifesto,40) }}</small>@endif
              </label>
            </div>
            @endforeach
          </div>
          @endif
          @endforeach

          <button type="submit" class="btn btn-success w-100" id="voteBtn" disabled
                  onclick="return confirm('আপনার ভোট প্রদান নিশ্চিত করছেন?')">
            <i class="bi bi-check-circle me-1"></i>ভোট দিন
          </button>
        </form>
      </div>
    </div>
    @else
    <div class="card mb-3">
      <div class="card-body text-center py-4">
        @if($election->status === 'upcoming')
          <i class="bi bi-hourglass-split fs-1 text-muted d-block mb-2"></i>
          <p class="text-muted">ভোটগ্রহণ শুরু হয়নি</p>
          <div class="text-muted small">শুরু হবে: {{ $election->voting_start->format('d M Y') }}</div>
        @elseif($election->status === 'nomination')
          <i class="bi bi-pencil-square fs-1 text-info d-block mb-2"></i>
          <p class="text-muted">মনোনয়ন পর্যায়ে আছে</p>
        @elseif(in_array($election->status, ['counting','completed']))
          <i class="bi bi-bar-chart fs-1 text-success d-block mb-2"></i>
          <p class="text-muted">ভোটগ্রহণ সম্পন্ন</p>
          @if($election->status === 'completed')
          <a href="{{ route('elections.results', $election) }}" class="btn btn-success btn-sm">
            <i class="bi bi-trophy me-1"></i>ফলাফল দেখুন
          </a>
          @endif
        @else
          <i class="bi bi-x-circle fs-1 text-danger d-block mb-2"></i>
          <p class="text-muted">নির্বাচন {{ $election->status_label }}</p>
        @endif
      </div>
    </div>
    @endif

    {{-- Stats --}}
    <div class="card">
      <div class="card-header py-3 fw-semibold"><i class="bi bi-bar-chart me-2 text-primary"></i>নির্বাচনের পরিসংখ্যান</div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">মোট পদ</span>
          <strong>{{ $election->positions->count() }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">মোট প্রার্থী</span>
          <strong>{{ $election->candidates->where('status','approved')->count() }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">মোট ভোট প্রদান</span>
          <strong class="text-success">{{ $election->votes->unique('voter_member_id')->count() }}</strong>
        </li>
      </ul>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
function checkVoterEligibility() {
    const sel = document.getElementById('voterSelect');
    const btn = document.getElementById('voteBtn');
    const inp = document.getElementById('voterMemberInput');
    if (sel.value) {
        inp.value = sel.value;
        btn.disabled = false;
    } else {
        inp.value = '';
        btn.disabled = true;
    }
}
</script>
@endpush
