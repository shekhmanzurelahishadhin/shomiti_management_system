@extends('layouts.app')
@section('title','Add Expense')
@section('page-title','Add Expense')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header py-3"><i class="bi bi-wallet2 me-2 text-danger"></i>New Expense</div>
      <div class="card-body">
        <form method="POST" action="{{ route('expenses.store') }}">
          @csrf
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
              <select name="category" class="form-select" required>
                @foreach($categories as $cat)
                  <option value="{{ $cat }}" {{ old('category')==$cat?'selected':'' }}>{{ ucfirst($cat) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Amount (৳) <span class="text-danger">*</span></label>
              <input type="number" name="amount" step="0.01" min="0" class="form-control"
                     value="{{ old('amount') }}" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
              <input type="date" name="expense_date" class="form-control"
                     value="{{ old('expense_date', date('Y-m-d')) }}" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
          </div>
          <hr class="mt-4">
          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
            <button class="btn btn-danger"><i class="bi bi-plus me-1"></i>Add Expense</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
