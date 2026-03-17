@extends('layouts.app')
@section('title','Expense Report')
@section('page-title','Expense Report')
@section('content')
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="From"></div>
      <div class="col-md-2"><input type="date" name="date_to"   class="form-control form-control-sm" value="{{ request('date_to') }}"   placeholder="To"></div>
      <div class="col-md-3">
        <select name="category" class="form-select form-select-sm">
          <option value="">All Categories</option>
          @foreach(['general','office','utilities','maintenance','salary','miscellaneous'] as $c)
            <option value="{{ $c }}" {{ request('category')==$c?'selected':'' }}>{{ ucfirst($c) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Filter</button></div>
      <div class="col-md-2">
        <a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-danger btn-sm w-100">
          <i class="bi bi-file-pdf me-1"></i>Export PDF
        </a>
      </div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <div class="card p-3 text-center">
      <div class="text-muted small">Total Expenses</div>
      <div class="fs-3 fw-bold text-danger">৳{{ number_format($total,2) }}</div>
    </div>
  </div>
  @foreach($byCategory as $cat => $amt)
  <div class="col-md-4 col-lg-2">
    <div class="card p-3 text-center">
      <div class="text-muted small">{{ ucfirst($cat) }}</div>
      <div class="fw-bold text-warning">৳{{ number_format($amt,2) }}</div>
    </div>
  </div>
  @endforeach
</div>

<div class="card">
  <div class="card-header py-3 d-flex justify-content-between">
    <span><i class="bi bi-wallet2 me-2 text-danger"></i>Expense List</span>
    <span class="badge bg-danger">{{ $expenses->count() }} records</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>Title</th><th>Category</th><th>Amount</th><th>Date</th><th>Description</th></tr>
      </thead>
      <tbody>
      @forelse($expenses as $i => $e)
        <tr>
          <td>{{ $i+1 }}</td>
          <td class="fw-semibold">{{ $e->title }}</td>
          <td><span class="badge bg-light text-dark">{{ ucfirst($e->category) }}</span></td>
          <td class="fw-bold text-danger">৳{{ number_format($e->amount,2) }}</td>
          <td>{{ $e->expense_date->format('d M Y') }}</td>
          <td class="text-muted small">{{ Str::limit($e->description,60) ?? '—' }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-4">No expenses found.</td></tr>
      @endforelse
      </tbody>
      @if($expenses->count())
      <tfoot class="table-light fw-bold">
        <tr><td colspan="3" class="text-end">Total:</td><td class="text-danger">৳{{ number_format($total,2) }}</td><td colspan="2"></td></tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection
