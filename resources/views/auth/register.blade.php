@extends('layouts.guest')
@section('title','নিবন্ধন')
@section('content')
<div class="text-center py-3">
  <i class="bi bi-lock-fill fs-1 text-muted d-block mb-3"></i>
  <h6 class="fw-bold">স্ব-নিবন্ধন বন্ধ আছে</h6>
  <p class="text-muted small">নতুন অ্যাকাউন্টের জন্য Admin এর সাথে যোগাযোগ করুন।</p>
  <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-box-arrow-in-right me-1"></i>লগইন করুন
  </a>
</div>
@endsection
