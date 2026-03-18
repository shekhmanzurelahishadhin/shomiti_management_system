@extends('layouts.app')
@section('title','নির্বাচন বিস্তারিত')
@section('page-title','কমিটি নির্বাচন')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <div>
    <h5 class="mb-0 fw-bold">{{ $election->title }}</h5>
    <small class="text-muted">{{ $election->election_year }} সালের কমিটি নির্বাচন</small>
  </div>
  <div class="d-flex gap-2 align-items-center flex-wrap">
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
            @csrf <input type="hidden" name="status" value="{{ $s }}">
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
        <small class="text-muted">{{ $position->seats }}টি আসন &nbsp;|&nbsp; মোট ভোট: <strong>{{ $position->votes->count() }}</strong></small>
      </div>

      {{-- Add candidate (admin only) --}}
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

      {{-- Candidates list with vote counts --}}
      <div class="list-group list-group-flush">
        @forelse($position->candidates->where('status','approved') as $cand)
        @php
          $voteCount   = $cand->votes->count();
          $totalVotes  = $position->votes->count();
          $pct         = $totalVotes > 0 ? round($voteCount/$totalVotes*100) : 0;
        @endphp
        <div class="list-group-item px-3 py-2">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <div>
              <span class="fw-semibold">{{ $cand->member->name }}</span>
              <small class="text-muted ms-2">{{ $cand->member->member_id }}</small>
              @if($cand->manifesto)
                <div class="text-muted" style="font-size:.75rem">{{  \Illuminate\Support\Str::limit($cand->manifesto,60) }}</div>
              @endif
            </div>
            @if(in_array($election->status,['counting','completed']))
            <div class="text-end">
              <span class="fw-bold text-primary">{{ $voteCount }} ভোট</span>
              <div class="text-muted" style="font-size:.7rem">{{ $pct }}%</div>
            </div>
            @endif
          </div>
          @if(in_array($election->status,['counting','completed']) && $totalVotes > 0)
          <div class="progress" style="height:5px">
            <div class="progress-bar bg-primary" style="width:{{ $pct }}%"></div>
          </div>
          @endif
          @can('manage committees')
          @if(in_array($election->status,['upcoming','nomination']))
          <div class="mt-1">
            <form method="POST" action="{{ route('elections.remove-candidate',[$election,$cand]) }}"
                  class="d-inline" onsubmit="return confirm('প্রার্থী বাদ দেবেন?')">
              @csrf @method('DELETE')
              <button class="btn btn-outline-danger btn-sm py-0 px-1" style="font-size:.7rem">
                <i class="bi bi-x"></i> বাদ দিন
              </button>
            </form>
          </div>
          @endif
          @endcan
        </div>
        @empty
        <div class="list-group-item text-muted fst-italic py-3 text-center">প্রার্থী নেই</div>
        @endforelse
      </div>
    </div>
    @endforeach
  </div>

  {{-- Right: Voting Booth --}}
  <div class="col-lg-5">

    {{-- ═══ VOTING OPEN ════════════════════════════════════════════════ --}}
    @if($election->isVotingOpen())

      @if(!$voterMember)
        {{-- Not linked to any member --}}
        <div class="card mb-3 border-warning">
          <div class="card-body text-center py-4">
            <i class="bi bi-person-x fs-1 text-warning d-block mb-2"></i>
            <h6 class="fw-bold">ভোট দিতে পারবেন না</h6>
            <p class="text-muted small">আপনার অ্যাকাউন্টের সাথে কোনো সদস্য প্রোফাইল লিঙ্ক নেই।<br>
              Admin এর সাথে যোগাযোগ করুন।</p>
          </div>
        </div>

      @elseif($hasVotedAll)
        {{-- Already voted on all positions --}}
        <div class="card mb-3 border-success">
          <div class="card-body text-center py-4">
            <i class="bi bi-patch-check-fill fs-1 text-success d-block mb-2"></i>
            <h6 class="fw-bold text-success">ভোট প্রদান সম্পন্ন!</h6>
            <p class="text-muted small mb-0">আপনি সফলভাবে ভোট দিয়েছেন।</p>
            <div class="mt-2 text-muted" style="font-size:.8rem">
              সদস্য: {{ $voterMember->name }} ({{ $voterMember->member_id }})
            </div>
          </div>
        </div>

      @else
        {{-- Voting form --}}
        <div class="card mb-3 border-success">
          <div class="card-header py-3 bg-success text-white fw-bold">
            <i class="bi bi-check-square me-2"></i>ভোট প্রদান করুন
          </div>
          <div class="card-body">
            <div class="alert alert-light py-2 mb-3">
              <i class="bi bi-person-check text-success me-2"></i>
              ভোটার: <strong>{{ $voterMember->name }}</strong>
              <span class="text-muted small ms-1">({{ $voterMember->member_id }})</span>
            </div>

            <form method="POST" action="{{ route('elections.vote', $election) }}" id="voteForm">
              @csrf

              @foreach($election->positions as $position)
              @php
                $approvedCands = $position->candidates->where('status','approved');
                $alreadyVotedPosition = in_array($position->id, $votedPositions);
              @endphp
              @if($approvedCands->count())
              <div class="mb-3 p-3 {{ $alreadyVotedPosition ? 'bg-success bg-opacity-10 border border-success' : 'bg-light' }} rounded">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="fw-semibold">{{ $position->position_name }}</div>
                  @if($alreadyVotedPosition)
                    <span class="badge bg-success"><i class="bi bi-check me-1"></i>ভোট দেওয়া হয়েছে</span>
                  @else
                    <span class="badge bg-warning text-dark">ভোট দিন</span>
                  @endif
                </div>

                @if($alreadyVotedPosition)
                  <p class="text-success small mb-0"><i class="bi bi-check-circle me-1"></i>এই পদে আপনি ভোট দিয়েছেন।</p>
                @else
                  @foreach($approvedCands as $cand)
                  <div class="form-check mb-2 ps-4">
                    <input class="form-check-input" type="radio"
                           name="votes[{{ $position->id }}]"
                           value="{{ $cand->id }}"
                           id="v_{{ $position->id }}_{{ $cand->id }}"
                           required>
                    <label class="form-check-label d-flex align-items-center gap-2"
                           for="v_{{ $position->id }}_{{ $cand->id }}">
                      @if($cand->member->photo)
                        <img src="{{ asset('storage/'.$cand->member->photo) }}"
                             class="rounded-circle" style="width:28px;height:28px;object-fit:cover">
                      @else
                        <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center
                                     justify-content-center fw-bold" style="width:28px;height:28px;font-size:.75rem">
                          {{ mb_strtoupper(mb_substr($cand->member->name,0,1)) }}
                        </span>
                      @endif
                      <div>
                        <div class="fw-semibold">{{ $cand->member->name }}</div>
                        @if($cand->manifesto)
                          <div class="text-muted" style="font-size:.72rem">{{  \Illuminate\Support\Str::limit($cand->manifesto,50) }}</div>
                        @endif
                      </div>
                    </label>
                  </div>
                  @endforeach
                @endif
              </div>
              @endif
              @endforeach

              {{-- Only show submit if there are unvoted positions --}}
              @php
                $unvotedPositions = $election->positions->filter(function($pos) use ($votedPositions) {
                    return $pos->candidates->where('status','approved')->count() > 0
                           && !in_array($pos->id, $votedPositions);
                });
              @endphp

              @if($unvotedPositions->count() > 0)
              <button type="submit" class="btn btn-success w-100 py-2 fw-bold"
                      onclick="return confirm('আপনার ভোট নিশ্চিত করছেন? একবার দিলে পরিবর্তন করা যাবে না।')">
                <i class="bi bi-check-circle me-2"></i>ভোট দিন
                <span class="badge bg-light text-success ms-2">{{ $unvotedPositions->count() }}টি পদে</span>
              </button>
              <p class="text-muted small text-center mt-2 mb-0">
                <i class="bi bi-shield-check me-1"></i>আপনার ভোট গোপন থাকবে
              </p>
              @else
              <div class="text-center text-success fw-semibold py-2">
                <i class="bi bi-check-circle-fill me-2"></i>সব পদে ভোট দেওয়া হয়েছে
              </div>
              @endif
            </form>
          </div>
        </div>
      @endif

    {{-- ═══ VOTING NOT OPEN ════════════════════════════════════════════ --}}
    @else
      <div class="card mb-3">
        <div class="card-body text-center py-5">
          @if($election->status === 'upcoming')
            <i class="bi bi-hourglass-split fs-1 text-muted d-block mb-2"></i>
            <h6 class="fw-bold text-muted">ভোটগ্রহণ শুরু হয়নি</h6>
            <div class="text-muted small">শুরু হবে: {{ $election->voting_start->format('d M Y') }}</div>
          @elseif($election->status === 'nomination')
            <i class="bi bi-pencil-square fs-1 text-info d-block mb-2"></i>
            <h6 class="fw-bold">মনোনয়ন পর্যায়ে আছে</h6>
            <div class="text-muted small">ভোটগ্রহণ শুরু হবে: {{ $election->voting_start->format('d M Y') }}</div>
          @elseif(in_array($election->status, ['counting','completed']))
            <i class="bi bi-bar-chart fs-1 text-success d-block mb-2"></i>
            <h6 class="fw-bold">ভোটগ্রহণ সম্পন্ন</h6>
            @if($election->status === 'completed')
              <a href="{{ route('elections.results', $election) }}" class="btn btn-success mt-2">
                <i class="bi bi-trophy me-1"></i>ফলাফল দেখুন
              </a>
            @endif
          @elseif($election->status === 'cancelled')
            <i class="bi bi-x-circle fs-1 text-danger d-block mb-2"></i>
            <h6 class="fw-bold text-danger">নির্বাচন বাতিল করা হয়েছে</h6>
          @endif
        </div>
      </div>
    @endif

    {{-- Stats card --}}
    <div class="card">
      <div class="card-header py-3 fw-semibold"><i class="bi bi-bar-chart me-2 text-primary"></i>নির্বাচনের পরিসংখ্যান</div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">মোট পদ</span>
          <strong>{{ $election->positions->count() }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">অনুমোদিত প্রার্থী</span>
          <strong>{{ $election->candidates->where('status','approved')->count() }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">অনন্য ভোটার</span>
          <strong class="text-success">{{ $election->votes->unique('voter_member_id')->count() }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">মোট ভোট প্রদান</span>
          <strong>{{ $election->votes->count() }}</strong>
        </li>
      </ul>
    </div>
  </div>

</div>
@endsection
