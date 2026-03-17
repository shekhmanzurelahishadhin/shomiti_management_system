@extends('layouts.app')
@section('title','Reports')
@section('page-title','Reports & Analytics')
@section('content')
<div class="row g-3">
  @php
    $reports = [
      ['route'=>'reports.monthly-collection','icon'=>'bi-calendar-check','color'=>'primary','title'=>'Monthly Collection','desc'=>'View all payments collected in a given month'],
      ['route'=>'reports.defaulters',        'icon'=>'bi-exclamation-triangle','color'=>'danger', 'title'=>'Defaulter List',      'desc'=>'Members with pending or overdue bills'],
      ['route'=>'reports.member-deposit',    'icon'=>'bi-person-lines-fill','color'=>'success','title'=>'Member Deposit Report','desc'=>'Yearly deposit history for a member'],
      ['route'=>'reports.annual-summary',    'icon'=>'bi-bar-chart-line','color'=>'warning','title'=>'Annual Summary',       'desc'=>'Full year income, expenses, and balance'],
      ['route'=>'reports.expense-report',    'icon'=>'bi-wallet2',       'color'=>'secondary','title'=>'Expense Report',       'desc'=>'Filterable expense breakdown by category'],
      ['route'=>'reports.daily-transaction', 'icon'=>'bi-calendar-day',  'color'=>'info',    'title'=>'Daily Transaction',    'desc'=>'All payments and expenses for a specific day'],
    ];
  @endphp
  @foreach($reports as $r)
  <div class="col-md-6 col-lg-4">
    <div class="card h-100 hover-shadow">
      <div class="card-body d-flex align-items-start gap-3 p-4">
        <div class="text-{{ $r['color'] }} bg-{{ $r['color'] }} bg-opacity-10 rounded p-2" style="font-size:1.8rem">
          <i class="bi {{ $r['icon'] }}"></i>
        </div>
        <div>
          <h6 class="fw-bold mb-1">{{ $r['title'] }}</h6>
          <p class="text-muted small mb-3">{{ $r['desc'] }}</p>
          <a href="{{ route($r['route']) }}" class="btn btn-{{ $r['color'] }} btn-sm">
            <i class="bi bi-arrow-right me-1"></i>View Report
          </a>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endsection
