@extends('layouts.app')
@section('title','Users')
@section('page-title','User Management')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2 text-primary"></i>Users</h5>
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-person-plus me-1"></i>Add User
    </a>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th class="text-end">Actions</th></tr>
      </thead>
      <tbody>
      @forelse($users as $user)
        <tr>
          <td class="fw-semibold">{{ $user->name }}</td>
          <td class="text-muted">{{ $user->email }}</td>
          <td>{{ $user->phone ?? '—' }}</td>
          <td>
            @foreach($user->roles as $role)
              <span class="badge bg-primary">{{ $role->name }}</span>
            @endforeach
          </td>
          <td><span class="badge badge-{{ $user->status }}">{{ ucfirst($user->status) }}</span></td>
          <td class="text-end">
            <a href="{{ route('users.edit',$user) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                    data-bs-target="#resetModal{{ $user->id }}"><i class="bi bi-key"></i></button>
            @if($user->id !== auth()->id())
            <form method="POST" action="{{ route('users.destroy',$user) }}" class="d-inline"
                  onsubmit="return confirm('Delete user?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
            @endif
          </td>
        </tr>

        {{-- Reset Password Modal --}}
        <div class="modal fade" id="resetModal{{ $user->id }}" tabindex="-1">
          <div class="modal-dialog">
            <form method="POST" action="{{ route('users.reset-password',$user) }}" class="modal-content">
              @csrf
              <div class="modal-header">
                <h5 class="modal-title">Reset Password — {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">New Password</label>
                  <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="mb-3">
                  <label class="form-label">Confirm Password</label>
                  <input type="password" name="password_confirmation" class="form-control" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">Reset Password</button>
              </div>
            </form>
          </div>
        </div>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($users->hasPages())
    <div class="card-footer">{{ $users->links() }}</div>
  @endif
</div>
@endsection
