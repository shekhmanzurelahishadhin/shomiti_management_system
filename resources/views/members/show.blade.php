@extends('layouts.app')
@section('title', $member->name)
@section('page-title','সদস্য বিস্তারিত')
@section('content')
<div class="row g-3">

  {{-- Left: Profile Card --}}
  <div class="col-lg-4">
    <div class="card mb-3">
      <div class="card-body text-center py-4">
        @if($member->photo)
          <img src="{{ asset('storage/'.$member->photo) }}" alt="{{ $member->name }}"
               class="rounded-circle mb-3 border border-3 border-primary"
               style="width:90px;height:90px;object-fit:cover">
        @else
          <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 border border-3 border-primary"
               style="width:90px;height:90px;font-size:2rem;font-weight:700">
            {{ strtoupper(mb_substr($member->name,0,1)) }}
          </div>
        @endif
        <h5 class="fw-bold mb-1">{{ $member->name }}</h5>
        <div class="text-muted small mb-1">
          {{ $member->father_name ? 'পিতা: '.$member->father_name : '' }}
        </div>
        <div class="mb-2">
          <span class="badge bg-secondary me-1">{{ $member->member_id }}</span>
          <span class="badge badge-{{ $member->status }}">
            {{ ['active'=>'সক্রিয়','inactive'=>'নিষ্ক্রিয়','suspended'=>'স্থগিত'][$member->status] ?? $member->status }}
          </span>
        </div>
      </div>

      <ul class="list-group list-group-flush small">
        @if($member->phone)
        <li class="list-group-item d-flex gap-2 align-items-start">
          <i class="bi bi-telephone text-primary mt-1"></i>
          <div><div class="text-muted" style="font-size:.72rem">মোবাইল</div>{{ $member->phone }}</div>
        </li>
        @endif
        @if($member->date_of_birth)
        <li class="list-group-item d-flex gap-2 align-items-start">
          <i class="bi bi-calendar text-primary mt-1"></i>
          <div><div class="text-muted" style="font-size:.72rem">জন্ম তারিখ</div>{{ $member->date_of_birth->format('d M Y') }}</div>
        </li>
        @endif
        <li class="list-group-item d-flex gap-2 align-items-start">
          <i class="bi bi-gender-ambiguous text-primary mt-1"></i>
          <div><div class="text-muted" style="font-size:.72rem">লিঙ্গ / বৈবাহিক অবস্থা</div>
            {{ ['male'=>'পুরুষ','female'=>'মহিলা','other'=>'অন্যান্য'][$member->gender] ?? $member->gender }}
            /
            {{ ['married'=>'বিবাহিত','unmarried'=>'অবিবাহিত','divorced'=>'তালাক','widowed'=>'বিধবা'][$member->marital_status] ?? $member->marital_status }}
          </div>
        </li>
        @if($member->nid_or_birth_cert)
        <li class="list-group-item d-flex gap-2 align-items-start">
          <i class="bi bi-credit-card text-primary mt-1"></i>
          <div><div class="text-muted" style="font-size:.72rem">NID / জন্ম সনদ</div>{{ $member->nid_or_birth_cert }}</div>
        </li>
        @endif
        @if($member->full_present_address)
        <li class="list-group-item d-flex gap-2 align-items-start">
          <i class="bi bi-geo-alt text-primary mt-1"></i>
          <div><div class="text-muted" style="font-size:.72rem">বর্তমান ঠিকানা</div>{{ $member->full_present_address }}</div>
        </li>
        @endif
        <li class="list-group-item d-flex gap-2 align-items-start">
          <i class="bi bi-calendar-check text-success mt-1"></i>
          <div><div class="text-muted" style="font-size:.72rem">ভর্তি তারিখ</div>{{ $member->join_date->format('d M Y') }}</div>
        </li>
        <li class="list-group-item d-flex gap-2 align-items-start">
          <i class="bi bi-bar-chart-steps text-primary mt-1"></i>
          <div><div class="text-muted" style="font-size:.72rem">শেয়ার সংখ্যা / মূল্য</div>
            {{ $member->share_count }}টি শেয়ার = <strong class="text-success">৳{{ number_format($member->share_value,0) }}</strong>
          </div>
        </li>
        <li class="list-group-item d-flex gap-2 align-items-start">
          <i class="bi bi-cash text-success mt-1"></i>
          <div><div class="text-muted" style="font-size:.72rem">মাসিক চাঁদা</div>
            <strong class="text-success">৳{{ number_format($member->monthly_deposit,2) }}</strong>
          </div>
        </li>
        @if($member->referredBy)
        <li class="list-group-item d-flex gap-2 align-items-start">
          <i class="bi bi-person-check text-primary mt-1"></i>
          <div><div class="text-muted" style="font-size:.72rem">রেফার্ড বাই</div>{{ $member->referredBy->name }} ({{ $member->referredBy->member_id }})</div>
        </li>
        @endif
      </ul>

      {{-- Nominee --}}
      @if($member->nominee_name)
      <div class="card-body border-top">
        <div class="text-muted small fw-semibold mb-2"><i class="bi bi-person-heart me-1 text-danger"></i>নমিনি তথ্য</div>
        <div class="small"><strong>{{ $member->nominee_name }}</strong></div>
        <div class="text-muted small">সম্পর্ক: {{ $member->nominee_relation }}</div>
        @if($member->nominee_phone)<div class="text-muted small">মোবাইল: {{ $member->nominee_phone }}</div>@endif
      </div>
      @endif

      <div class="card-body d-flex gap-2">
        <a href="{{ route('members.edit', $member) }}" class="btn btn-warning btn-sm flex-fill">
          <i class="bi bi-pencil me-1"></i>সম্পাদনা
        </a>
        <a href="{{ route('members.registration-pdf', $member) }}" class="btn btn-danger btn-sm flex-fill" title="নিবন্ধন ফর্ম PDF">
          <i class="bi bi-file-pdf me-1"></i>ফর্ম PDF
        </a>
        @can('collect payments')
        <a href="{{ route('payments.create', ['member_id' => $member->id]) }}" class="btn btn-success btn-sm flex-fill">
          <i class="bi bi-cash me-1"></i>চাঁদা দিন
        </a>
        @endcan
      </div>
    </div>
  </div>

  {{-- Right: Tabs --}}
  <div class="col-lg-8">
    {{-- Summary row --}}
    <div class="row g-2 mb-3">
      <div class="col-4">
        <div class="card text-center p-3">
          <div class="text-muted small">মোট পরিশোধ</div>
          <div class="fw-bold text-success">৳{{ number_format($payments->sum('amount'),0) }}</div>
        </div>
      </div>
      <div class="col-4">
        <div class="card text-center p-3">
          <div class="text-muted small">বকেয়া বিল</div>
          <div class="fw-bold text-danger">৳{{ number_format($member->pending_dues,0) }}</div>
        </div>
      </div>
      <div class="col-4">
        <div class="card text-center p-3">
          <div class="text-muted small">মোট বিল</div>
          <div class="fw-bold text-primary">{{ $bills->total() }}টি</div>
        </div>
      </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="memberTabs">
      <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#billsTab">বিল</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#paymentsTab">পেমেন্ট</button></li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane fade show active" id="billsTab">
        <div class="card">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr><th>মাস</th><th>পরিমাণ</th><th>জরিমানা</th><th>পরিশোধ</th><th>বাকি</th><th>অবস্থা</th><th></th></tr>
              </thead>
              <tbody>
              @forelse($bills as $bill)
                <tr>
                  <td>{{ $bill->month_name }} {{ $bill->bill_year }}</td>
                  <td>৳{{ number_format($bill->amount,2) }}</td>
                  <td class="{{ $bill->fine>0?'text-danger':'' }}">{{ $bill->fine>0?'৳'.number_format($bill->fine,2):'—' }}</td>
                  <td class="text-success">৳{{ number_format($bill->paid_amount,2) }}</td>
                  <td class="{{ $bill->total_due>0?'text-danger fw-semibold':'' }}">৳{{ number_format($bill->total_due,2) }}</td>
                  <td><span class="badge badge-{{ $bill->status }}">{{ ucfirst($bill->status) }}</span></td>
                  <td><a href="{{ route('bills.show', $bill) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a></td>
                </tr>
              @empty
                <tr><td colspan="7" class="text-center text-muted py-3">কোনো বিল নেই।</td></tr>
              @endforelse
              </tbody>
            </table>
          </div>
          @if($bills->hasPages())<div class="card-footer">{{ $bills->links() }}</div>@endif
        </div>
      </div>
      <div class="tab-pane fade" id="paymentsTab">
        <div class="card">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr><th>তারিখ</th><th>পরিমাণ</th><th>পদ্ধতি</th><th>রেফারেন্স</th><th></th></tr>
              </thead>
              <tbody>
              @forelse($payments as $pay)
                <tr>
                  <td>{{ $pay->payment_date->format('d M Y') }}</td>
                  <td class="fw-bold text-success">৳{{ number_format($pay->amount,2) }}</td>
                  <td><span class="badge bg-secondary">{{ strtoupper($pay->payment_method) }}</span></td>
                  <td>{{ $pay->reference ?? '—' }}</td>
                  <td><a href="{{ route('payments.receipt', $pay) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-receipt"></i></a></td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted py-3">কোনো পেমেন্ট নেই।</td></tr>
              @endforelse
              </tbody>
            </table>
          </div>
          @if($payments->hasPages())<div class="card-footer">{{ $payments->links() }}</div>@endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
