@extends('layouts.app')
@section('title','Edit Committee')
@section('page-title','Edit Committee')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header py-3"><i class="bi bi-pencil me-2 text-warning"></i>Edit: {{ $committee->name }}</div>
      <div class="card-body">
        <form method="POST" action="{{ route('committees.update',$committee) }}">
          @csrf @method('PUT')
          <div class="mb-3">
            <label class="form-label fw-semibold">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name',$committee->name) }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description',$committee->description) }}</textarea>
          </div>
          <div class="mb-4">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
              @foreach(['active','completed','inactive'] as $s)
                <option value="{{ $s }}" {{ old('status',$committee->status)==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
              @endforeach
            </select>
          </div>
          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('committees.show',$committee) }}" class="btn btn-secondary">Cancel</a>
            <button class="btn btn-warning"><i class="bi bi-save me-1"></i>Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
