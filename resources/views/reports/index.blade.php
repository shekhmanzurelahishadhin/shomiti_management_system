@extends('layouts.app')
@section('title','রিপোর্ট')
@section('page-title','রিপোর্ট ও বিশ্লেষণ')
@section('content')

@if($isMember)
{{-- ── MEMBER: only sees own reports ────────────────────────────────── --}}
<div class="alert alert-info border-0 mb-4">
  <i class="bi bi-person-check me-2"></i>
  আপনি শুধুমাত্র <strong>আপনার নিজের</strong> তথ্যের রিপোর্ট দেখতে পারবেন।
</div>
<div class="row g-3">
  @php
    $memberReports = [
      ['route'=>'reports.member-deposit', 'icon'=>'bi-person-lines-fill','color'=>'primary',  'title'=>'আমার জমার বিবরণী', 'desc'=>'বার্ষিক চাঁদা জমার ইতিহাস'],
      ['route'=>'reports.annual-summary', 'icon'=>'bi-bar-chart-line',   'color'=>'success',  'title'=>'আমার বার্ষিক সারসংক্ষেপ','desc'=>'বছরওয়ারি বিল ও পেমেন্ট'],
      ['route'=>'reports.defaulters',     'icon'=>'bi-exclamation-triangle','color'=>'warning','title'=>'আমার বকেয়া বিল',  'desc'=>'পরিশোধ না হওয়া বিলের তালিকা'],
      ['route'=>'reports.daily-transaction','icon'=>'bi-calendar-day',   'color'=>'info',    'title'=>'আমার দৈনিক লেনদেন','desc'=>'যেকোনো দিনের পেমেন্ট বিবরণ'],
    ];
  @endphp
  @foreach($memberReports as $r)
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body d-flex align-items-start gap-3 p-4">
        <div class="text-{{ $r['color'] }} bg-{{ $r['color'] }} bg-opacity-10 rounded p-2" style="font-size:1.8rem">
          <i class="bi {{ $r['icon'] }}"></i>
        </div>
        <div>
          <h6 class="fw-bold mb-1">{{ $r['title'] }}</h6>
          <p class="text-muted small mb-3">{{ $r['desc'] }}</p>
          <a href="{{ route($r['route']) }}" class="btn btn-{{ $r['color'] }} btn-sm">
            <i class="bi bi-arrow-right me-1"></i>দেখুন
          </a>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

@else
{{-- ── ADMIN/TREASURER: full reports ─────────────────────────────────── --}}
<div class="row g-3">
  @php
    $reports = [
      ['route'=>'reports.monthly-collection','icon'=>'bi-calendar-check',    'color'=>'primary',  'title'=>'মাসিক সংগ্রহ রিপোর্ট',    'desc'=>'নির্দিষ্ট মাসের সকল পেমেন্ট'],
      ['route'=>'reports.defaulters',        'icon'=>'bi-exclamation-triangle','color'=>'danger',  'title'=>'বকেয়া তালিকা',             'desc'=>'পেমেন্ট না করা সদস্যের তালিকা'],
      ['route'=>'reports.member-deposit',    'icon'=>'bi-person-lines-fill',  'color'=>'success',  'title'=>'সদস্যভিত্তিক জমা রিপোর্ট', 'desc'=>'একজন সদস্যের বার্ষিক জমার ইতিহাস'],
      ['route'=>'reports.annual-summary',    'icon'=>'bi-bar-chart-line',     'color'=>'warning',  'title'=>'বার্ষিক সারসংক্ষেপ',        'desc'=>'পূর্ণ বছরের আয়-ব্যয় ও ব্যালেন্স'],
      ['route'=>'reports.expense-report',    'icon'=>'bi-wallet2',            'color'=>'secondary','title'=>'খরচ রিপোর্ট',               'desc'=>'শ্রেণিভিত্তিক খরচের বিবরণ'],
      ['route'=>'reports.daily-transaction', 'icon'=>'bi-calendar-day',       'color'=>'info',     'title'=>'দৈনিক লেনদেন',              'desc'=>'যেকোনো দিনের পেমেন্ট ও খরচ'],
    ];
  @endphp
  @foreach($reports as $r)
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <div class="card-body d-flex align-items-start gap-3 p-4">
        <div class="text-{{ $r['color'] }} bg-{{ $r['color'] }} bg-opacity-10 rounded p-2" style="font-size:1.8rem">
          <i class="bi {{ $r['icon'] }}"></i>
        </div>
        <div>
          <h6 class="fw-bold mb-1">{{ $r['title'] }}</h6>
          <p class="text-muted small mb-3">{{ $r['desc'] }}</p>
          <a href="{{ route($r['route']) }}" class="btn btn-{{ $r['color'] }} btn-sm">
            <i class="bi bi-arrow-right me-1"></i>দেখুন
          </a>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endif

@endsection
