@extends('layouts.app')
@section('title','Edit User')
@section('page-title','Edit User')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header py-3"><i class="bi bi-pencil me-2 text-warning"></i>Edit: {{ $user->name }}</div>
      <div class="card-body">
        <form method="POST" action="{{ route('users.update',$user) }}">
          @csrf @method('PUT')
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Full Name</label>
              <input type="text" name="name" class="form-control" value="{{ old('name',$user->name) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" name="email" class="form-control" value="{{ old('email',$user->email) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone',$user->phone) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Role</label>
              <select name="role" class="form-select" required>
                @foreach($roles as $role)
                  <option value="{{ $role->name }}"
                    {{ $user->hasRole($role->name)?'selected':'' }}>
                    {{ $role->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Status</label>
              <select name="status" class="form-select">
                <option value="active"   {{ $user->status=='active'  ?'selected':'' }}>Active</option>
                <option value="inactive" {{ $user->status=='inactive'?'selected':'' }}>Inactive</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">লিঙ্ক করুন সদস্য প্রোফাইল</label>
              <select name="member_id" class="form-select">
                <option value="">— কোনো সদস্য লিঙ্ক নেই —</option>
                @foreach($members as $m)
                  <option value="{{ $m->id }}" {{ $user->member_id==$m->id?'selected':'' }}>
                    {{ $m->name }} ({{ $m->member_id }})
                  </option>
                @endforeach
              </select>
              @if($user->member)
                <div class="form-text text-success"><i class="bi bi-check-circle me-1"></i>বর্তমানে লিঙ্কড: {{ $user->member->name }}</div>
              @else
                <div class="form-text text-muted">Member রোলের জন্য সদস্য প্রোফাইল লিঙ্ক করুন।</div>
              @endif
            </div>
          </div>
          <hr class="mt-4">
          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
            <button class="btn btn-warning"><i class="bi bi-save me-1"></i>Update User</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
