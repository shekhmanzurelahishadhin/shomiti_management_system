@extends('layouts.app')
@section('title','Expenses')
@section('page-title','Expense Management')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-wallet2 me-2 text-danger"></i>Expenses</h5>
    <a href="{{ route('expenses.create') }}" class="btn btn-danger btn-sm">
        <i class="bi bi-plus me-1"></i>Add Expense
    </a>
</div>

<div class="row g-3 mb-3">
  <div class="col-6 col-md-3">
    <div class="card text-center p-3">
      <div class="text-muted small">This Month</div>
      <div class="fs-4 fw-bold text-danger">৳{{ number_format($monthTotal,2) }}</div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Search title..." value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <select name="category" class="form-select form-select-sm">
          <option value="">All Categories</option>
          @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category')==$cat?'selected':'' }}>{{ ucfirst($cat) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
      </div>
      <div class="col-md-2">
        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
      </div>
      <div class="col-md-1">
        <button class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-search"></i></button>
      </div>
      <div class="col-md-2">
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Title</th><th>Category</th><th>Amount</th><th>Date</th><th>Added By</th><th class="text-end">Actions</th></tr>
      </thead>
      <tbody>
      @forelse($expenses as $e)
        <tr>
          <td>
            <div class="fw-semibold">{{ $e->title }}</div>
            @if($e->description)
              <small class="text-muted">{{  \Illuminate\Support\Str::limit($e->description,50) }}</small>
            @endif
          </td>
          <td><span class="badge bg-light text-dark">{{ ucfirst($e->category) }}</span></td>
          <td class="fw-bold text-danger">৳{{ number_format($e->amount,2) }}</td>
          <td>{{ $e->expense_date->format('d M Y') }}</td>
          <td>{{ $e->creator->name ?? '—' }}</td>
          <td class="text-end">
            <a href="{{ route('expenses.edit',$e) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
            <form method="POST" action="{{ route('expenses.destroy',$e) }}" class="d-inline"
                  onsubmit="return confirm('Delete expense?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-4">No expenses found.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($expenses->hasPages())
    <div class="card-footer">{{ $expenses->links() }}</div>
  @endif
</div>
@endsection
