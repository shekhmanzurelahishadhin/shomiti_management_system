@extends('layouts.guest')
@section('title', 'Forgot Password')
@section('content')
<p class="text-muted small mb-4">Enter your email and we'll send a password reset link.</p>
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-semibold">Email Address</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Send Reset Link</button>
    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-decoration-none small">Back to login</a>
    </div>
</form>
@endsection
