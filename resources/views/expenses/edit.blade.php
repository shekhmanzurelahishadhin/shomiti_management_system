@extends('layouts.app')
@section('title','Edit Expense')
@section('page-title','Edit Expense')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header py-3"><i class="bi bi-pencil me-2 text-warning"></i>Edit Expense</div>
      <div class="card-body">
        <form method="POST" action="{{ route('expenses.update',$expense) }}">
          @csrf @method('PUT')
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Title</label>
              <input type="text" name="title" class="form-control" value="{{ old('title',$expense->title) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Category</label>
              <select name="category" class="form-select">
                @foreach($categories as $cat)
                  <option value="{{ $cat }}" {{ old('category',$expense->category)==$cat?'selected':'' }}>{{ ucfirst($cat) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Amount (৳)</label>
              <input type="number" name="amount" step="0.01" class="form-control" value="{{ old('amount',$expense->amount) }}" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Date</label>
              <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date',$expense->expense_date->format('Y-m-d')) }}" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description" class="form-control" rows="3">{{ old('description',$expense->description) }}</textarea>
            </div>
          </div>
          <hr class="mt-4">
          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
            <button class="btn btn-warning"><i class="bi bi-save me-1"></i>Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
