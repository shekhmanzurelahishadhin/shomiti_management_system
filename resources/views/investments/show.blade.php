@extends('layouts.app')
@section('title','বিনিয়োগ বিস্তারিত')
@section('page-title','বিনিয়োগ বিস্তারিত')
@section('content')

<div class="row g-3">
{{-- ── Left: Request Info ─────────────────────────────────────── --}}
<div class="col-lg-5">

  {{-- Status header card --}}
  <div class="card mb-3">
    <div class="card-header py-3 d-flex justify-content-between align-items-center"
         style="background:linear-gradient(135deg,#1e8449,#27ae60);color:#fff">
      <div>
        <div class="fw-bold fs-6">{{ $investment->project_name }}</div>
        <div style="font-size:.8rem;opacity:.85">আবেদন #{{ $investment->id }}</div>
      </div>
      <span class="badge bg-light text-dark fs-6 px-3">{{ $investment->status_label }}</span>
    </div>
    <ul class="list-group list-group-flush small">
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">সদস্য</span>
        <a href="{{ route('members.show',$investment->member) }}" class="fw-semibold text-decoration-none">
          {{ $investment->member->name }} ({{ $investment->member->member_id }})
        </a>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">আবেদনের তারিখ</span>
        <strong>{{ $investment->submitted_date->format('d M Y') }}</strong>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">চাওয়া পরিমাণ</span>
        <strong>৳{{ number_format($investment->requested_amount,2) }}</strong>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">মেয়াদ</span>
        <strong>{{ $investment->duration_months }} মাস</strong>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">প্রত্যাশিত লাভের হার</span>
        <strong>{{ $investment->expected_profit_ratio }}%</strong>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">প্রত্যাশিত রিটার্ন তারিখ</span>
        <strong>{{ $investment->expected_return_date->format('d M Y') }}</strong>
      </li>

      @if($investment->approved_amount)
      <li class="list-group-item bg-light"><span class="fw-bold text-success">অনুমোদিত তথ্য</span></li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">অনুমোদিত পরিমাণ</span>
        <strong class="text-success fs-5">৳{{ number_format($investment->approved_amount,2) }}</strong>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">অনুমোদিত মেয়াদ</span>
        <strong>{{ $investment->approved_duration_months }} মাস</strong>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">অনুমোদিত লাভের হার</span>
        <strong>{{ $investment->approved_profit_ratio }}%</strong>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">প্রত্যাশিত লাভ</span>
        <strong class="text-success">৳{{ number_format($investment->expected_profit_amount,2) }}</strong>
      </li>
      <li class="list-group-item d-flex justify-content-between bg-light">
        <span class="fw-bold">মোট প্রত্যাশিত রিটার্ন</span>
        <strong class="text-primary fs-5">৳{{ number_format($investment->expected_return_amount,2) }}</strong>
      </li>
      @if($investment->approved_start_date)
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">শুরুর তারিখ</span>
        <strong>{{ $investment->approved_start_date->format('d M Y') }}</strong>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">রিটার্ন তারিখ</span>
        <strong class="{{ $investment->is_matured ? 'text-danger' : '' }}">
          {{ $investment->approved_return_date->format('d M Y') }}
        </strong>
      </li>
      @if($investment->status === 'active')
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">বাকি দিন</span>
        <strong class="{{ $investment->days_remaining <= 7 ? 'text-danger' : '' }}">
          {{ $investment->days_remaining }} দিন
        </strong>
      </li>
      @endif
      @endif
      @endif
    </ul>

    @if($investment->project_description)
    <div class="card-body border-top">
      <div class="text-muted small fw-semibold mb-1">প্রকল্পের বিবরণ</div>
      <p class="mb-0 small">{{ $investment->project_description }}</p>
    </div>
    @endif

    @if($investment->approval_note)
    <div class="card-body border-top bg-light">
      <div class="text-muted small fw-semibold mb-1"><i class="bi bi-check-circle text-success me-1"></i>অনুমোদনের মন্তব্য</div>
      <p class="mb-0 small">{{ $investment->approval_note }}</p>
      @if($investment->approvedBy)
        <div class="text-muted" style="font-size:.75rem">— {{ $investment->approvedBy->name }}, {{ $investment->approved_at?->format('d M Y H:i') }}</div>
      @endif
    </div>
    @endif

    @if($investment->rejection_note)
    <div class="card-body border-top" style="background:#fdf2f2">
      <div class="text-danger small fw-semibold mb-1"><i class="bi bi-x-circle me-1"></i>প্রত্যাখ্যানের কারণ</div>
      <p class="mb-0 small">{{ $investment->rejection_note }}</p>
    </div>
    @endif

    @if($investment->modification_note)
    <div class="card-body border-top" style="background:#fff8e1">
      <div class="text-warning small fw-semibold mb-1"><i class="bi bi-pencil-square me-1"></i>সংশোধনের মন্তব্য</div>
      <p class="mb-0 small">{{ $investment->modification_note }}</p>
    </div>
    @endif
  </div>

  {{-- Action panels --}}
  @can('approve investments')
  @if(in_array($investment->status, ['pending','in_agenda']))

  {{-- APPROVE --}}
  <div class="card mb-3 border-success">
    <div class="card-header bg-success text-white py-2 fw-semibold">
      <i class="bi bi-check-circle me-2"></i>অনুমোদন করুন
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('investments.approve', $investment) }}">
        @csrf
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small fw-semibold">অনুমোদিত পরিমাণ (৳) *</label>
            <input type="number" name="approved_amount" step="0.01" min="1" class="form-control form-control-sm"
                   value="{{ $investment->requested_amount }}" required>
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">মেয়াদ (মাস) *</label>
            <input type="number" name="approved_duration_months" min="1" class="form-control form-control-sm"
                   value="{{ $investment->duration_months }}" required>
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">লাভের হার (%) *</label>
            <input type="number" name="approved_profit_ratio" step="0.01" min="0" class="form-control form-control-sm"
                   value="{{ $investment->expected_profit_ratio }}" required>
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">শুরুর তারিখ *</label>
            <input type="date" name="approved_start_date" class="form-control form-control-sm"
                   value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">রিটার্ন তারিখ *</label>
            <input type="date" name="approved_return_date" class="form-control form-control-sm" required
                   id="approvedReturnDate">
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">মন্তব্য</label>
            <textarea name="approval_note" class="form-control form-control-sm" rows="2"></textarea>
          </div>
        </div>
        <button class="btn btn-success btn-sm w-100">
          <i class="bi bi-check-circle me-1"></i>অনুমোদন করুন
        </button>
      </form>
    </div>
  </div>

  {{-- REJECT --}}
  <div class="card mb-3 border-danger">
    <div class="card-header bg-danger text-white py-2 fw-semibold">
      <i class="bi bi-x-circle me-2"></i>প্রত্যাখ্যান করুন
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('investments.reject', $investment) }}">
        @csrf
        <div class="mb-2">
          <label class="form-label small fw-semibold">প্রত্যাখ্যানের কারণ *</label>
          <textarea name="rejection_note" class="form-control form-control-sm" rows="2" required></textarea>
        </div>
        <button class="btn btn-danger btn-sm w-100">
          <i class="bi bi-x-circle me-1"></i>প্রত্যাখ্যান করুন
        </button>
      </form>
    </div>
  </div>

  {{-- MODIFICATION --}}
  <div class="card mb-3 border-warning">
    <div class="card-header bg-warning py-2 fw-semibold">
      <i class="bi bi-pencil-square me-2"></i>সংশোধনের জন্য ফেরত
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('investments.modify', $investment) }}">
        @csrf
        <div class="mb-2">
          <label class="form-label small fw-semibold">সংশোধনের নির্দেশনা *</label>
          <textarea name="modification_note" class="form-control form-control-sm" rows="2" required></textarea>
        </div>
        <button class="btn btn-warning btn-sm w-100">
          <i class="bi bi-pencil-square me-1"></i>সংশোধনের জন্য পাঠান
        </button>
      </form>
    </div>
  </div>
  @endif
  @endcan
</div>

{{-- ── Right: Timeline & Vouchers ────────────────────────────── --}}
<div class="col-lg-7">

  {{-- Progress Timeline --}}
  <div class="card mb-3">
    <div class="card-header py-3 fw-semibold"><i class="bi bi-diagram-2 me-2 text-primary"></i>প্রক্রিয়ার অবস্থা</div>
    <div class="card-body">
      @php
        $steps = [
            ['key'=>'pending',   'label'=>'আবেদন জমা',     'icon'=>'bi-send',         'color'=>'primary'],
            ['key'=>'approved',  'label'=>'অনুমোদিত',       'icon'=>'bi-check-circle', 'color'=>'success'],
            ['key'=>'active',    'label'=>'পেমেন্ট সম্পন্ন','icon'=>'bi-cash-coin',   'color'=>'info'],
            ['key'=>'matured',   'label'=>'মেয়াদ শেষ',      'icon'=>'bi-alarm',        'color'=>'warning'],
            ['key'=>'closed',    'label'=>'নিষ্পন্ন',        'icon'=>'bi-flag-fill',   'color'=>'dark'],
        ];
        $statusOrder = ['pending'=>0,'in_agenda'=>0,'approved'=>1,'modification_needed'=>1,'rejected'=>-1,'active'=>2,'matured'=>3,'closed'=>4];
        $currentIdx  = $statusOrder[$investment->status] ?? 0;
      @endphp
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        @foreach($steps as $i => $step)
        <div class="text-center flex-fill">
          <div class="rounded-circle mx-auto mb-1 d-flex align-items-center justify-content-center
               {{ $i <= $currentIdx ? 'bg-'.$step['color'].' text-white' : 'bg-light text-muted' }}"
               style="width:42px;height:42px;font-size:1.1rem">
            <i class="bi {{ $step['icon'] }}"></i>
          </div>
          <div style="font-size:.7rem" class="{{ $i <= $currentIdx ? 'fw-semibold' : 'text-muted' }}">{{ $step['label'] }}</div>
        </div>
        @if(!$loop->last)
        <div class="flex-fill" style="height:2px;background:{{ $i < $currentIdx ? '#27ae60' : '#dee2e6' }}"></div>
        @endif
        @endforeach
      </div>
    </div>
  </div>

  {{-- Payment Voucher --}}
  @if($investment->payment)
  <div class="card mb-3 border-success">
    <div class="card-header py-2 bg-success text-white fw-semibold d-flex justify-content-between">
      <span><i class="bi bi-receipt me-2"></i>পেমেন্ট ভাউচার</span>
      <span>{{ $investment->payment->voucher_number }}</span>
    </div>
    <ul class="list-group list-group-flush small">
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">তারিখ</span>
        <strong>{{ $investment->payment->payment_date->format('d M Y') }}</strong>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">পরিমাণ</span>
        <strong class="text-success fs-5">৳{{ number_format($investment->payment->amount,2) }}</strong>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">পদ্ধতি</span>
        <span class="badge bg-secondary">{{ strtoupper($investment->payment->payment_method) }}</span>
      </li>
      @if($investment->payment->reference)
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">রেফারেন্স</span><strong>{{ $investment->payment->reference }}</strong>
      </li>
      @endif
    </ul>
    <div class="card-footer d-flex gap-2">
      <a href="{{ route('investments.voucher.view', $investment->payment) }}" class="btn btn-success btn-sm flex-fill">
        <i class="bi bi-eye me-1"></i>ভাউচার দেখুন
      </a>
      <a href="{{ route('investments.voucher.pdf', $investment->payment) }}" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-file-pdf me-1"></i>PDF
      </a>
    </div>
  </div>
  @elseif($investment->status === 'approved')
  @can('process investment payment')
  <div class="card mb-3 border-primary">
    <div class="card-body text-center py-4">
      <i class="bi bi-cash-coin fs-1 text-primary d-block mb-2"></i>
      <p class="text-muted mb-3">অনুমোদিত হয়েছে। পেমেন্ট করতে প্রস্তুত।</p>
      <a href="{{ route('investments.payment', $investment) }}" class="btn btn-primary">
        <i class="bi bi-cash-coin me-1"></i>পেমেন্ট করুন
      </a>
    </div>
  </div>
  @endcan
  @endif

  {{-- Settlement Voucher --}}
  @if($investment->settlement)
  <div class="card mb-3 border-dark">
    <div class="card-header py-2 bg-dark text-white fw-semibold d-flex justify-content-between">
      <span><i class="bi bi-check2-circle me-2"></i>নিষ্পত্তি ভাউচার</span>
      <span>{{ $investment->settlement->voucher_number }}</span>
    </div>
    <ul class="list-group list-group-flush small">
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">নিষ্পত্তির তারিখ</span>
        <strong>{{ $investment->settlement->settlement_date->format('d M Y') }}</strong>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">বিনিয়োগ</span>
        <strong>৳{{ number_format($investment->settlement->investment_amount,2) }}</strong>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span class="text-muted">লাভ/ক্ষতি</span>
        <strong class="{{ $investment->settlement->actual_profit_loss >= 0 ? 'text-success' : 'text-danger' }}">
          {{ $investment->settlement->actual_profit_loss >= 0 ? '+' : '' }}৳{{ number_format($investment->settlement->actual_profit_loss,2) }}
          ({{ $investment->settlement->outcome_label }})
        </strong>
      </li>
      <li class="list-group-item d-flex justify-content-between bg-light">
        <span class="fw-bold">রিটার্ন পরিমাণ</span>
        <strong class="text-primary fs-5">৳{{ number_format($investment->settlement->return_amount,2) }}</strong>
      </li>
    </ul>
    <div class="card-footer d-flex gap-2">
      <a href="{{ route('investments.settlement.voucher', $investment->settlement) }}" class="btn btn-dark btn-sm flex-fill">
        <i class="bi bi-eye me-1"></i>ভাউচার দেখুন
      </a>
      <a href="{{ route('investments.settlement.voucher.pdf', $investment->settlement) }}" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-file-pdf me-1"></i>PDF
      </a>
    </div>
  </div>
  @elseif(in_array($investment->status, ['active','matured']))
  @can('settle investments')
  <div class="card mb-3 border-dark">
    <div class="card-body text-center py-4">
      <i class="bi bi-check2-circle fs-1 text-dark d-block mb-2"></i>
      <p class="text-muted mb-3">
        {{ $investment->is_matured ? 'মেয়াদ শেষ হয়েছে।' : 'বিনিয়োগ সক্রিয়।' }} নিষ্পত্তি করুন।
      </p>
      <a href="{{ route('investments.settlement', $investment) }}" class="btn btn-dark">
        <i class="bi bi-check2-circle me-1"></i>নিষ্পত্তি করুন
      </a>
    </div>
  </div>
  @endcan
  @endif

  {{-- Meeting history --}}
  @if($investment->meetingItems->count())
  <div class="card">
    <div class="card-header py-3 fw-semibold"><i class="bi bi-calendar-event me-2 text-info"></i>সভার ইতিহাস</div>
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light">
          <tr><th>সভার শিরোনাম</th><th>তারিখ</th><th>সিদ্ধান্ত</th></tr>
        </thead>
        <tbody>
        @foreach($investment->meetingItems as $item)
          <tr>
            <td><a href="{{ route('investments.meeting.show', $item->meeting) }}" class="text-decoration-none">{{ $item->meeting->title }}</a></td>
            <td>{{ $item->meeting->meeting_date->format('d M Y') }}</td>
            <td>
              @php $dc=['pending'=>'warning','approved'=>'success','rejected'=>'danger','modification_needed'=>'secondary']; @endphp
              <span class="badge bg-{{ $dc[$item->decision] ?? 'secondary' }}">{{ $item->decision_label }}</span>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

</div>
</div>
@endsection
@push('scripts')
<script>
// Auto-calculate approved return date from start date + duration
document.querySelectorAll('[name=approved_start_date],[name=approved_duration_months]').forEach(el => {
    el.addEventListener('change', function() {
        const startEl    = document.querySelector('[name=approved_start_date]');
        const durationEl = document.querySelector('[name=approved_duration_months]');
        const returnEl   = document.getElementById('approvedReturnDate');
        if (startEl?.value && durationEl?.value && returnEl) {
            const d = new Date(startEl.value);
            d.setMonth(d.getMonth() + parseInt(durationEl.value));
            returnEl.value = d.toISOString().split('T')[0];
        }
    });
});
</script>
@endpush
