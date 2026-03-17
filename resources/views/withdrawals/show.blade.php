@extends('layouts.app')
@section('title','উত্তোলন আবেদন বিস্তারিত')
@section('page-title','উত্তোলন আবেদন বিস্তারিত')
@section('content')
<div class="row g-3">

  {{-- Left: Request Info --}}
  <div class="col-lg-5">
    <div class="card mb-3">
      <div class="card-header py-3 d-flex justify-content-between align-items-center"
           style="background:linear-gradient(135deg,#856404,#d4ac0d);color:#fff">
        <span><i class="bi bi-cash-stack me-2"></i>আবেদন #{{ $withdrawal->id }}</span>
        <span class="badge bg-light text-dark">{{ $withdrawal->status_label }}</span>
      </div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">সদস্য</span>
          <a href="{{ route('members.show', $withdrawal->member) }}" class="fw-semibold text-decoration-none">
            {{ $withdrawal->member->name }} ({{ $withdrawal->member->member_id }})
          </a>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">সদস্যের অবস্থা</span>
          <span class="badge bg-{{ $withdrawal->member->status_badge_color }}">
            {{ $withdrawal->member->status_label }}
          </span>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">আবেদনের তারিখ</span>
          <strong>{{ $withdrawal->requested_date->format('d M Y') }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">নির্ধারিত পরিশোধ</span>
          <strong>{{ $withdrawal->scheduled_repay_date?->format('d M Y') ?? '—' }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">শেয়ার মূল্য</span>
          <strong>৳{{ number_format($withdrawal->share_amount,2) }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">সঞ্চয়</span>
          <strong>৳{{ number_format($withdrawal->savings_amount,2) }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">মুনাফা</span>
          <strong>৳{{ number_format($withdrawal->profit_amount,2) }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between bg-light">
          <span class="fw-bold">মোট পরিশোধযোগ্য</span>
          <strong class="text-primary fs-5">৳{{ number_format($withdrawal->total_amount,2) }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">পরিশোধ হয়েছে</span>
          <strong class="text-success">৳{{ number_format($withdrawal->repaid_amount,2) }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="fw-bold text-danger">বকেয়া</span>
          <strong class="text-danger fs-5">৳{{ number_format($withdrawal->remaining_amount,2) }}</strong>
        </li>
      </ul>

      {{-- Progress bar --}}
      @if($withdrawal->total_amount > 0)
      <div class="card-body">
        <div class="d-flex justify-content-between small text-muted mb-1">
          <span>পরিশোধের অগ্রগতি</span>
          <span>{{ $withdrawal->repayment_percent }}%</span>
        </div>
        <div class="progress" style="height:12px">
          <div class="progress-bar bg-success" style="width:{{ $withdrawal->repayment_percent }}%"></div>
        </div>
      </div>
      @endif

      @if($withdrawal->reason)
      <div class="card-body border-top">
        <div class="text-muted small fw-semibold mb-1">উত্তোলনের কারণ</div>
        <p class="mb-0 small">{{ $withdrawal->reason }}</p>
      </div>
      @endif

      @if($withdrawal->admin_note)
      <div class="card-body border-top">
        <div class="text-muted small fw-semibold mb-1">প্রশাসনিক মন্তব্য</div>
        <p class="mb-0 small">{{ $withdrawal->admin_note }}</p>
        @if($withdrawal->approvedBy)
          <div class="text-muted" style="font-size:.75rem">{{ $withdrawal->approvedBy->name }}, {{ $withdrawal->approved_at?->format('d M Y H:i') }}</div>
        @endif
      </div>
      @endif
    </div>

    {{-- Action Panels --}}
    @can('manage members')

    {{-- APPROVE (pending only) --}}
    @if($withdrawal->status === 'pending')
    <div class="card mb-3 border-success">
      <div class="card-header bg-success text-white py-2 fw-semibold">
        <i class="bi bi-check-circle me-2"></i>আবেদন অনুমোদন করুন
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('withdrawals.approve', $withdrawal) }}">
          @csrf
          <div class="mb-2">
            <label class="form-label small fw-semibold">নির্ধারিত পরিশোধের তারিখ <span class="text-danger">*</span></label>
            <input type="date" name="scheduled_repay_date" class="form-control form-control-sm" required
                   min="{{ date('Y-m-d') }}">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">মন্তব্য</label>
            <textarea name="admin_note" class="form-control form-control-sm" rows="2"></textarea>
          </div>
          <button class="btn btn-success btn-sm w-100">
            <i class="bi bi-check-circle me-1"></i>অনুমোদন করুন (সদস্যপদ স্থগিত হবে)
          </button>
        </form>
      </div>
    </div>
    <div class="card mb-3 border-danger">
      <div class="card-header bg-danger text-white py-2 fw-semibold">
        <i class="bi bi-x-circle me-2"></i>আবেদন প্রত্যাখ্যান করুন
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('withdrawals.reject', $withdrawal) }}">
          @csrf
          <div class="mb-3">
            <label class="form-label small fw-semibold">প্রত্যাখ্যানের কারণ <span class="text-danger">*</span></label>
            <textarea name="admin_note" class="form-control form-control-sm" rows="2" required></textarea>
          </div>
          <button class="btn btn-danger btn-sm w-100">
            <i class="bi bi-x-circle me-1"></i>প্রত্যাখ্যান করুন
          </button>
        </form>
      </div>
    </div>
    @endif

    {{-- MARK FULLY REPAID (on_hold / partially_repaid) --}}
    @if(in_array($withdrawal->status, ['on_hold','partially_repaid']))
    <div class="card mb-3 border-primary">
      <div class="card-body text-center">
        <form method="POST" action="{{ route('withdrawals.mark-repaid', $withdrawal) }}"
              onsubmit="return confirm('সম্পূর্ণ পরিশোধ চিহ্নিত করবেন? সদস্যপদ সংযোগ বিচ্ছিন্ন হবে।')">
          @csrf
          <button class="btn btn-primary btn-sm">
            <i class="bi bi-check2-all me-1"></i>সম্পূর্ণ পরিশোধ চিহ্নিত করুন
          </button>
        </form>
      </div>
    </div>
    @endif

    @endcan
  </div>

  {{-- Right: Repayment record + history --}}
  <div class="col-lg-7">

    {{-- Add repayment --}}
    @can('collect payments')
    @if(in_array($withdrawal->status, ['on_hold','partially_repaid']))
    <div class="card mb-3">
      <div class="card-header py-3 fw-semibold">
        <i class="bi bi-cash-coin me-2 text-success"></i>পরিশোধ রেকর্ড করুন
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('withdrawals.repayment', $withdrawal) }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">পরিমাণ (৳) <span class="text-danger">*</span></label>
              <input type="number" name="amount" step="0.01" min="0.01"
                     class="form-control @error('amount') is-invalid @enderror"
                     value="{{ old('amount', $withdrawal->remaining_amount) }}" required>
              @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <div class="form-text text-danger">বকেয়া: ৳{{ number_format($withdrawal->remaining_amount,2) }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">তারিখ <span class="text-danger">*</span></label>
              <input type="date" name="repay_date" class="form-control"
                     value="{{ old('repay_date', date('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">পদ্ধতি <span class="text-danger">*</span></label>
              <select name="method" class="form-select">
                @foreach(['cash'=>'নগদ','bank'=>'ব্যাংক','bkash'=>'বিকাশ','nagad'=>'নগদ অ্যাপ','other'=>'অন্যান্য'] as $v=>$l)
                  <option value="{{ $v }}">{{ $l }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">রেফারেন্স</label>
              <input type="text" name="reference" class="form-control" value="{{ old('reference') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">মন্তব্য</label>
              <input type="text" name="note" class="form-control" value="{{ old('note') }}">
            </div>
          </div>
          <div class="mt-3 text-end">
            <button class="btn btn-success">
              <i class="bi bi-cash-coin me-1"></i>পরিশোধ রেকর্ড করুন
            </button>
          </div>
        </form>
      </div>
    </div>
    @endif
    @endcan

    {{-- Repayment history --}}
    <div class="card">
      <div class="card-header py-3 fw-semibold">
        <i class="bi bi-clock-history me-2 text-primary"></i>পরিশোধের ইতিহাস ({{ $withdrawal->repayments->count() }})
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr><th>তারিখ</th><th>পরিমাণ</th><th>পদ্ধতি</th><th>রেফারেন্স</th><th>পরিশোধকারী</th></tr>
          </thead>
          <tbody>
          @forelse($withdrawal->repayments as $r)
            <tr>
              <td>{{ $r->repay_date->format('d M Y') }}</td>
              <td class="fw-bold text-success">৳{{ number_format($r->amount,2) }}</td>
              <td><span class="badge bg-secondary">{{ strtoupper($r->method) }}</span></td>
              <td>{{ $r->reference ?? '—' }}</td>
              <td>{{ $r->paidBy->name ?? 'System' }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">কোনো পরিশোধ রেকর্ড নেই।</td></tr>
          @endforelse
          </tbody>
          @if($withdrawal->repayments->count())
          <tfoot class="table-light fw-bold">
            <tr>
              <td>মোট</td>
              <td class="text-success">৳{{ number_format($withdrawal->repaid_amount,2) }}</td>
              <td colspan="3"></td>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>
    </div>

  </div>
</div>
@endsection
