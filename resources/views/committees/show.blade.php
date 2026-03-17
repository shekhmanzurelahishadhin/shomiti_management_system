@extends('layouts.app')
@section('title', $committee->name)
@section('page-title','Committee Detail')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-diagram-3 me-2 text-primary"></i>{{ $committee->name }}</h5>
    <div class="d-flex gap-2">
        <span class="badge badge-{{ $committee->status }} px-3 py-2">{{ ucfirst($committee->status) }}</span>
        <a href="{{ route('committees.edit',$committee) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    {{-- Members list --}}
    <div class="card mb-3">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2 text-primary"></i>Members ({{ $committee->committeeMembers->count() }})</span>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#addMemberForm">
          <i class="bi bi-person-plus me-1"></i>Add Member
        </button>
      </div>

      <div class="collapse" id="addMemberForm">
        <div class="card-body border-bottom bg-light">
          <form method="POST" action="{{ route('committees.add-member',$committee) }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-4">
              <label class="form-label small">Member</label>
              <select name="member_id" class="form-select form-select-sm" required>
                <option value="">— Select —</option>
                @foreach($availableMembers as $m)
                  <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->member_id }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small">Contribution</label>
              <select name="contribution_type" class="form-select form-select-sm">
                <option value="full">Full Month</option>
                <option value="half">Half Month</option>
                <option value="quarter">Quarter Month</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label small">Draw Order</label>
              <input type="number" name="draw_order" class="form-control form-control-sm" min="1">
            </div>
            <div class="col-md-2">
              <label class="form-label small">Joined</label>
              <input type="date" name="joined_at" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-1">
              <button class="btn btn-primary btn-sm w-100"><i class="bi bi-plus"></i></button>
            </div>
          </form>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr><th>#</th><th>Member</th><th>Contribution</th><th>Draw Order</th><th>Joined</th><th></th></tr>
          </thead>
          <tbody>
          @forelse($committee->committeeMembers as $cm)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>
                <div class="fw-semibold">{{ $cm->member->name }}</div>
                <small class="text-muted">{{ $cm->member->member_id }}</small>
              </td>
              <td><span class="badge bg-info text-dark">{{ ucfirst($cm->contribution_type) }}</span></td>
              <td>{{ $cm->draw_order ?? '—' }}</td>
              <td>{{ $cm->joined_at ? $cm->joined_at->format('d M Y') : '—' }}</td>
              <td>
                <form method="POST" action="{{ route('committees.remove-member',[$committee,$cm]) }}"
                      class="d-inline" onsubmit="return confirm('Remove member?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger"><i class="bi bi-person-x"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-3">No members added yet.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Draws --}}
    <div class="card">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-trophy me-2 text-warning"></i>Draws ({{ $committee->draws->count() }})</span>
        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#addDrawForm">
          <i class="bi bi-plus me-1"></i>Record Draw
        </button>
      </div>
      <div class="collapse" id="addDrawForm">
        <div class="card-body border-bottom bg-light">
          <form method="POST" action="{{ route('committees.record-draw',$committee) }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-3">
              <label class="form-label small">Member</label>
              <select name="member_id" class="form-select form-select-sm" required>
                <option value="">— Select —</option>
                @foreach($committee->committeeMembers as $cm)
                  <option value="{{ $cm->member_id }}">{{ $cm->member->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label small">Draw Order</label>
              <input type="number" name="draw_order" class="form-control form-control-sm" min="1" required>
            </div>
            <div class="col-md-2">
              <label class="form-label small">Date</label>
              <input type="date" name="draw_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-2">
              <label class="form-label small">Payout (৳)</label>
              <input type="number" name="payout_amount" step="0.01" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-2">
              <label class="form-label small">Notes</label>
              <input type="text" name="notes" class="form-control form-control-sm">
            </div>
            <div class="col-md-1">
              <button class="btn btn-warning btn-sm w-100"><i class="bi bi-check"></i></button>
            </div>
          </form>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr><th>Order</th><th>Member</th><th>Date</th><th>Payout</th><th>Status</th></tr>
          </thead>
          <tbody>
          @forelse($committee->draws->sortBy('draw_order') as $draw)
            <tr>
              <td>#{{ $draw->draw_order }}</td>
              <td>{{ $draw->member->name }}</td>
              <td>{{ $draw->draw_date ? $draw->draw_date->format('d M Y') : '—' }}</td>
              <td class="fw-bold text-success">৳{{ number_format($draw->payout_amount,2) }}</td>
              <td><span class="badge badge-{{ $draw->status }}">{{ ucfirst($draw->status) }}</span></td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-3">No draws recorded.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-header py-3">Committee Info</div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Total Members</span>
          <strong>{{ $committee->committeeMembers->count() }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Fund Balance</span>
          <strong class="text-success">৳{{ number_format($committee->total_fund,2) }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Completed Draws</span>
          <strong>{{ $committee->draws->where('status','completed')->count() }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Pending Draws</span>
          <strong>{{ $committee->draws->where('status','pending')->count() }}</strong>
        </li>
      </ul>
      @if($committee->description)
      <div class="card-body text-muted small">{{ $committee->description }}</div>
      @endif
    </div>
  </div>
</div>
@endsection
