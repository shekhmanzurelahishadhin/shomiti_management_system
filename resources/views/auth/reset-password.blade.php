@extends('layouts.guest')
@section('title', 'Reset Password')
@section('content')
<form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $request->email) }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">New Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-4">
        <label class="form-label fw-semibold">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Reset Password</button>
</form>
@endsection
