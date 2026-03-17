@extends('layouts.app')
@section('title','সদস্য তালিকা')
@section('page-title','সদস্য ব্যবস্থাপনা')
@section('content')
<div class="row g-2 mb-3">
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #2e86c1">
      <div class="text-muted small">মোট সদস্য</div>
      <div class="fs-4 fw-bold text-primary">{{ $totalMembers }}</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #27ae60">
      <div class="text-muted small">সক্রিয় সদস্য</div>
      <div class="fs-4 fw-bold text-success">{{ $activeMembers }}</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #e74c3c">
      <div class="text-muted small">স্থগিত সদস্য</div>
      <div class="fs-4 fw-bold text-danger">{{ $suspendedMembers }}</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #d4ac0d">
      <div class="text-muted small">সর্বোচ্চ সদস্য</div>
      <div class="fs-4 fw-bold text-warning">{{ \App\Models\Setting::get('max_members', 30) }}</div>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0 fw-bold"><i class="bi bi-people me-2 text-primary"></i>সদস্য তালিকা</h5>
  <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-person-plus me-1"></i>নতুন সদস্য যোগ
  </a>
</div>

<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="নাম, সদস্য আইডি, ফোন অথবা পিতার নাম..." value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <select name="status" class="form-select form-select-sm">
          <option value="">সব অবস্থা</option>
          <option value="active"    {{ request('status')=='active'   ?'selected':'' }}>সক্রিয়</option>
          <option value="inactive"    {{ request('status')=='inactive'   ?'selected':'' }}>নিষ্ক্রিয়</option>
          <option value="suspended"  {{ request('status')=='suspended'  ?'selected':'' }}>স্থগিত</option>
          <option value="on_hold"    {{ request('status')=='on_hold'    ?'selected':'' }}>অপেক্ষামান</option>
          <option value="disconnected" {{ request('status')=='disconnected'?'selected':'' }}>সংযোগ বিচ্ছিন্ন</option>
        </select>
      </div>
      <div class="col-md-2">
        <select name="gender" class="form-select form-select-sm">
          <option value="">সব লিঙ্গ</option>
          <option value="male"   {{ request('gender')=='male'  ?'selected':'' }}>পুরুষ</option>
          <option value="female" {{ request('gender')=='female'?'selected':'' }}>মহিলা</option>
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-search me-1"></i>খুঁজুন</button>
      </div>
      <div class="col-md-2">
        <a href="{{ route('members.index') }}" class="btn btn-outline-secondary btn-sm w-100">রিসেট</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>সদস্য আইডি</th>
          <th>নাম / পিতার নাম</th>
          <th>মোবাইল</th>
          <th>শেয়ার</th>
          <th>মাসিক চাঁদা</th>
          <th>ভর্তির তারিখ</th>
          <th>অবস্থা</th>
          <th class="text-end">অ্যাকশন</th>
        </tr>
      </thead>
      <tbody>
      @forelse($members as $member)
        <tr>
          <td>
            <span class="badge bg-light text-dark fw-semibold border">{{ $member->member_id }}</span>
          </td>
          <td>
            <a href="{{ route('members.show', $member) }}" class="text-decoration-none fw-semibold">
              {{ $member->name }}
            </a>
            @if($member->father_name)
              <div class="small text-muted">পিতা: {{ $member->father_name }}</div>
            @endif
          </td>
          <td>{{ $member->phone ?? '—' }}</td>
          <td>
            <span class="badge bg-info text-dark">{{ $member->share_count }}টি</span>
            <small class="text-muted">৳{{ number_format($member->share_value,0) }}</small>
          </td>
          <td class="fw-semibold text-success">৳{{ number_format($member->monthly_deposit,0) }}</td>
          <td>{{ $member->join_date->format('d M Y') }}</td>
          <td>
              <span class="badge bg-{{ $member->status_badge_color }}">
              {{ $member->status_label }}
            </span>
          </td>
          <td class="text-end">
            <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-outline-info" title="বিস্তারিত">
              <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-outline-warning" title="সম্পাদনা">
              <i class="bi bi-pencil"></i>
            </a>
            <form method="POST" action="{{ route('members.destroy', $member) }}" class="d-inline"
                  onsubmit="return confirm('সদস্য মুছে ফেলবেন?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" title="মুছুন">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="8" class="text-center text-muted py-4">কোনো সদস্য পাওয়া যায়নি।</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($members->hasPages())
    <div class="card-footer">{{ $members->links() }}</div>
  @endif
</div>
@endsection
