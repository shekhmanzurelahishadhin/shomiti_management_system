@extends('layouts.app')
@section('title','আমার ড্যাশবোর্ড')
@section('page-title','আমার ড্যাশবোর্ড')
@section('content')

{{-- Member profile header --}}
<div class="card mb-4" style="background:linear-gradient(135deg,#0d1f0f,#2d6a30);color:#fff;border:none">
  <div class="card-body py-4">
    <div class="row align-items-center">
      <div class="col-auto">
        @if($member->photo)
          <img src="{{ asset('storage/'.$member->photo) }}" alt="{{ $member->name }}"
               class="rounded-circle border border-3 border-white"
               style="width:72px;height:72px;object-fit:cover">
        @else
          <div class="rounded-circle border border-3 border-white bg-white text-primary
                      d-flex align-items-center justify-content-center fw-bold"
               style="width:72px;height:72px;font-size:1.8rem">
            {{ mb_strtoupper(mb_substr($member->name,0,1)) }}
          </div>
        @endif
      </div>
      <div class="col">
        <h4 class="mb-1 fw-bold">{{ $member->name }}</h4>
        <div class="opacity-75">
          <span class="badge bg-light text-dark me-2">{{ $member->member_id }}</span>
          সদস্য হওয়ার তারিখ: {{ $member->join_date->format('d M Y') }}
        </div>
      </div>
      <div class="col-auto text-end">
        <a href="{{ route('members.show', $member) }}" class="btn btn-light btn-sm">
          <i class="bi bi-person me-1"></i>আমার প্রোফাইল
        </a>
      </div>
    </div>
  </div>
</div>

{{-- Active election banner --}}
@if($activeElection && $activeElection->isVotingOpen())
<div class="alert {{ $hasVoted ? 'alert-success' : 'alert-warning' }} d-flex justify-content-between align-items-center mb-4">
  @if($hasVoted)
    <span><i class="bi bi-check-circle-fill me-2"></i>আপনি ইতিমধ্যে ভোট দিয়েছেন — <strong>{{ $activeElection->title }}</strong></span>
    <a href="{{ route('elections.show', $activeElection) }}" class="btn btn-success btn-sm">ফলাফল দেখুন</a>
  @else
    <span><i class="bi bi-bell-fill me-2"></i>ভোটগ্রহণ চলছে! — <strong>{{ $activeElection->title }}</strong></span>
    <a href="{{ route('elections.show', $activeElection) }}" class="btn btn-warning btn-sm fw-bold">
      <i class="bi bi-check-square me-1"></i>এখনই ভোট দিন
    </a>
  @endif
</div>
@endif

{{-- Financial summary --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #27ae60">
      <div class="text-muted small">মোট পরিশোধ</div>
      <div class="fs-4 fw-bold text-success">৳{{ number_format($totalPaid,0) }}</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #e74c3c">
      <div class="text-muted small">বকেয়া</div>
      <div class="fs-4 fw-bold {{ $pendingDues > 0 ? 'text-danger' : 'text-muted' }}">
        ৳{{ number_format($pendingDues,0) }}
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #f39c12">
      <div class="text-muted small">মাসিক চাঁদা</div>
      <div class="fs-4 fw-bold text-warning">৳{{ number_format($member->monthly_deposit,0) }}</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center p-3" style="border-left:4px solid #2e86c1">
      <div class="text-muted small">শেয়ার মূল্য</div>
      <div class="fs-4 fw-bold text-primary">৳{{ number_format($member->share_value,0) }}</div>
    </div>
  </div>
</div>

@if($overdueBills > 0)
<div class="alert alert-danger py-2 mb-4 d-flex justify-content-between align-items-center">
  <span><i class="bi bi-exclamation-triangle me-2"></i><strong>{{ $overdueBills }}টি</strong> বিল অতিদেয়। জরিমানা যোগ হচ্ছে।</span>
  <a href="{{ route('members.show', $member) }}#billsTab" class="btn btn-danger btn-sm">বিল দেখুন</a>
</div>
@endif

<div class="row g-3">
  {{-- Recent Bills --}}
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-receipt me-2 text-primary"></i>সাম্প্রতিক বিল</span>
        <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-outline-primary">সব দেখুন</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
          <thead class="table-light">
            <tr><th>মাস</th><th>পরিমাণ</th><th>পরিশোধ</th><th>বাকি</th><th>অবস্থা</th></tr>
          </thead>
          <tbody>
          @forelse($bills as $bill)
            <tr>
              <td>{{ $bill->month_name }} {{ $bill->bill_year }}</td>
              <td>৳{{ number_format($bill->amount,0) }}</td>
              <td class="text-success">৳{{ number_format($bill->paid_amount,0) }}</td>
              <td class="{{ $bill->total_due > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                ৳{{ number_format($bill->total_due,0) }}
              </td>
              <td><span class="badge badge-{{ $bill->status }}" style="font-size:.7rem">{{ ucfirst($bill->status) }}</span></td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-3">কোনো বিল নেই।</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Recent Payments --}}
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-cash-coin me-2 text-success"></i>সাম্প্রতিক পেমেন্ট</span>
        <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-outline-success">সব দেখুন</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
          <thead class="table-light">
            <tr><th>তারিখ</th><th>পরিমাণ</th><th>পদ্ধতি</th><th>রসিদ</th></tr>
          </thead>
          <tbody>
          @forelse($payments as $pay)
            <tr>
              <td>{{ $pay->payment_date->format('d M Y') }}</td>
              <td class="fw-semibold text-success">৳{{ number_format($pay->amount,0) }}</td>
              <td><span class="badge bg-secondary" style="font-size:.7rem">{{ strtoupper($pay->payment_method) }}</span></td>
              <td>
                <a href="{{ route('payments.receipt', $pay) }}" class="btn btn-sm btn-outline-success py-0 px-1">
                  <i class="bi bi-receipt"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-3">কোনো পেমেন্ট নেই।</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Active Investments --}}
  @if($activeInvestments->count())
  <div class="col-12">
    <div class="card">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-graph-up-arrow me-2 text-success"></i>আমার বিনিয়োগ</span>
        <a href="{{ route('investments.index') }}" class="btn btn-sm btn-outline-success">সব দেখুন</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
          <thead class="table-light">
            <tr><th>প্রকল্প</th><th>পরিমাণ</th><th>লাভ হার</th><th>মেয়াদ শেষ</th><th>অবস্থা</th></tr>
          </thead>
          <tbody>
          @foreach($activeInvestments as $inv)
            <tr>
              <td class="fw-semibold">{{  \Illuminate\Support\Str::limit($inv->project_name,35) }}</td>
              <td>৳{{ number_format($inv->approved_amount,0) }}</td>
              <td>{{ $inv->approved_profit_ratio }}%</td>
              <td>{{ $inv->approved_return_date?->format('d M Y') ?? '—' }}</td>
              <td><span class="badge bg-{{ $inv->status_color }}">{{ $inv->status_label }}</span></td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  {{-- Quick actions --}}
  <div class="col-12">
    <div class="card">
      <div class="card-header py-3 fw-semibold">
        <i class="bi bi-lightning me-2 text-warning"></i>দ্রুত অ্যাকশন
      </div>
      <div class="card-body d-flex gap-3 flex-wrap">
        <a href="{{ route('members.show', $member) }}" class="btn btn-outline-primary">
          <i class="bi bi-person me-2"></i>আমার প্রোফাইল
        </a>
        <a href="{{ route('members.registration-pdf', $member) }}" class="btn btn-outline-danger">
          <i class="bi bi-file-pdf me-2"></i>নিবন্ধন ফর্ম
        </a>
        @can('submit investment request')
        <a href="{{ route('investments.create') }}" class="btn btn-outline-success">
          <i class="bi bi-graph-up-arrow me-2"></i>বিনিয়োগ আবেদন
        </a>
        @endcan
        @if($activeElection && $activeElection->isVotingOpen() && !$hasVoted)
        <a href="{{ route('elections.show', $activeElection) }}" class="btn btn-warning fw-bold">
          <i class="bi bi-check-square me-2"></i>ভোট দিন
        </a>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
