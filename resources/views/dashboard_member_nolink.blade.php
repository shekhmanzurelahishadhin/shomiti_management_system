@extends('layouts.app')
@section('title','ড্যাশবোর্ড')
@section('page-title','ড্যাশবোর্ড')
@section('content')
<div class="row justify-content-center mt-5">
  <div class="col-md-6 text-center">
    <div class="card border-warning">
      <div class="card-body py-5">
        <i class="bi bi-person-x fs-1 text-warning d-block mb-3"></i>
        <h5 class="fw-bold">প্রোফাইল লিঙ্ক করা হয়নি</h5>
        <p class="text-muted">আপনার ব্যবহারকারী অ্যাকাউন্টের সাথে কোনো সদস্য প্রোফাইল সংযুক্ত নেই।</p>
        <p class="text-muted small">Admin এর সাথে যোগাযোগ করুন এবং আপনার অ্যাকাউন্টে সদস্য প্রোফাইল লিঙ্ক করতে বলুন।</p>
        <div class="mt-3">
          <a href="mailto:{{ \App\Models\Setting::get('somity_email','nabadigantaltd@gmail.com') }}" class="btn btn-warning">
            <i class="bi bi-envelope me-2"></i>Admin এ যোগাযোগ করুন
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
