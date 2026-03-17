@extends('layouts.app')
@section('title','New Committee')
@section('page-title','New Committee')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header py-3"><i class="bi bi-diagram-3 me-2 text-primary"></i>Create Committee</div>
      <div class="card-body">
        <form method="POST" action="{{ route('committees.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-semibold">Committee Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
          </div>
          <div class="mb-4">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('committees.index') }}" class="btn btn-secondary">Cancel</a>
            <button class="btn btn-primary"><i class="bi bi-plus me-1"></i>Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
