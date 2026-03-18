@extends('layouts.app')
@section('title','বিনিয়োগ রিপোর্ট')
@section('page-title','বিনিয়োগ রিপোর্ট')
@section('content')
<div class="row g-3">
  @php
    $rpts = [
      ['r'=>'investments.reports.requests',  'i'=>'bi-file-earmark-text','c'=>'primary',   't'=>'আবেদন রিপোর্ট',       'd'=>'সকল বিনিয়োগ আবেদনের তালিকা'],
      ['r'=>'investments.reports.active',    'i'=>'bi-graph-up-arrow',   'c'=>'success',   't'=>'সক্রিয় বিনিয়োগ',     'd'=>'বর্তমানে চলমান সকল বিনিয়োগ'],
      ['r'=>'investments.reports.maturity',  'i'=>'bi-alarm',            'c'=>'warning',   't'=>'মেয়াদ রিপোর্ট',       'd'=>'মেয়াদোত্তীর্ণ ও আসন্ন বিনিয়োগ'],
      ['r'=>'investments.reports.ledger',    'i'=>'bi-person-lines-fill','c'=>'info',      't'=>'সদস্যভিত্তিক লেজার',  'd'=>'প্রতিটি সদস্যের বিনিয়োগ ইতিহাস'],
      ['r'=>'investments.reports.profitloss','i'=>'bi-currency-exchange', 'c'=>'danger',   't'=>'লাভ/ক্ষতি রিপোর্ট',  'd'=>'নিষ্পন্ন বিনিয়োগের লাভ-ক্ষতি সারসংক্ষেপ'],
      ['r'=>'investments.reports.settlements','i'=>'bi-check2-circle',   'c'=>'dark',      't'=>'নিষ্পত্তি রিপোর্ট',   'd'=>'সকল নিষ্পন্ন বিনিয়োগের তালিকা'],
      ['r'=>'investments.reports.payments',  'i'=>'bi-cash-coin',        'c'=>'secondary', 't'=>'পেমেন্ট রিপোর্ট',      'd'=>'বিনিয়োগ বিতরণের পেমেন্ট তালিকা'],
    ];
  @endphp
  @foreach($rpts as $r)
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <div class="card-body d-flex align-items-start gap-3 p-4">
        <div class="text-{{ $r['c'] }} bg-{{ $r['c'] }} bg-opacity-10 rounded p-2" style="font-size:1.8rem">
          <i class="bi {{ $r['i'] }}"></i>
        </div>
        <div>
          <h6 class="fw-bold mb-1">{{ $r['t'] }}</h6>
          <p class="text-muted small mb-3">{{ $r['d'] }}</p>
          <a href="{{ route($r['r']) }}" class="btn btn-{{ $r['c'] }} btn-sm">
            <i class="bi bi-arrow-right me-1"></i>দেখুন
          </a>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endsection
