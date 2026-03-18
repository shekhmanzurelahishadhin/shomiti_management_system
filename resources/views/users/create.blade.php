@extends('layouts.app')
@section('title','Add User')
@section('page-title','Add User')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header py-3"><i class="bi bi-person-plus me-2 text-primary"></i>New User</div>
      <div class="card-body">
        <form method="POST" action="{{ route('users.store') }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name') }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
              <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                <option value="">— Select Role —</option>
                @foreach($roles as $role)
                  <option value="{{ $role->name }}" {{ old('role')==$role->name?'selected':'' }}>
                    {{ $role->name }}
                  </option>
                @endforeach
              </select>
              @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                     required minlength="6">
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Status</label>
              <select name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">লিঙ্ক করুন সদস্য প্রোফাইল</label>
              <select name="member_id" class="form-select">
                <option value="">— কোনো সদস্য লিঙ্ক নেই (Admin/Treasurer) —</option>
                @foreach($members as $m)
                  <option value="{{ $m->id }}" {{ old('member_id')==$m->id?'selected':'' }}>
                    {{ $m->name }} ({{ $m->member_id }})
                  </option>
                @endforeach
              </select>
              <div class="form-text">Member রোলের জন্য তাদের সদস্য প্রোফাইল লিঙ্ক করুন যাতে তারা শুধু নিজের তথ্য দেখতে পান।</div>
            </div>
          </div>
          <hr class="mt-4">
          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
            <button class="btn btn-primary"><i class="bi bi-person-check me-1"></i>Create User</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
