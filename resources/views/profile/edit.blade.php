@extends('layouts.app')
@section('title','Profile')
@section('page-title','My Profile')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card mb-3">
      <div class="card-header py-3"><i class="bi bi-person me-2 text-primary"></i>Profile Information</div>
      <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}">
          @csrf @method('PATCH')
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Full Name</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Role</label>
              <input type="text" class="form-control" value="{{ $user->roles->pluck('name')->join(', ') }}" readonly>
            </div>
          </div>
          <hr class="mt-4">
          <div class="d-flex justify-content-end">
            <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Save Changes</button>
          </div>
        </form>
        @if(session('status') === 'profile-updated')
          <div class="alert alert-success mt-3 mb-0"><i class="bi bi-check-circle me-2"></i>Profile updated.</div>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-header py-3"><i class="bi bi-lock me-2 text-warning"></i>Change Password</div>
      <div class="card-body">
        <form method="POST" action="{{ route('password.update') }}">
          @csrf @method('PUT')
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Current Password</label>
              <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">New Password</label>
              <input type="password" name="password" class="form-control" required minlength="8">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>
          </div>
          <hr class="mt-4">
          <div class="d-flex justify-content-end">
            <button class="btn btn-warning"><i class="bi bi-key me-1"></i>Update Password</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
