@extends('layouts.app')
@section('title','সেটিংস')
@section('page-title','সিস্টেম সেটিংস')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header py-3">
        <i class="bi bi-gear me-2 text-primary"></i>নবদিগন্ত সমবায় সমিতি — সেটিংস
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('settings.update') }}">
          @csrf

          <h6 class="fw-bold text-muted mb-3 mt-1 border-bottom pb-2">
            <i class="bi bi-building me-2"></i>সমিতির তথ্য
          </h6>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-semibold">সমিতির নাম (বাংলা) <span class="text-danger">*</span></label>
              <input type="text" name="somity_name" class="form-control"
                     value="{{ old('somity_name', $settings['somity_name'] ?? '') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Somity Name (English)</label>
              <input type="text" name="somity_name_en" class="form-control"
                     value="{{ old('somity_name_en', $settings['somity_name_en'] ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">ফোন নম্বর</label>
              <input type="text" name="somity_phone" class="form-control"
                     value="{{ old('somity_phone', $settings['somity_phone'] ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">ইমেইল</label>
              <input type="email" name="somity_email" class="form-control"
                     value="{{ old('somity_email', $settings['somity_email'] ?? '') }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">ঠিকানা</label>
              <textarea name="somity_address" class="form-control" rows="2">{{ old('somity_address', $settings['somity_address'] ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">ট্যাগলাইন</label>
              <input type="text" name="tagline" class="form-control"
                     value="{{ old('tagline', $settings['tagline'] ?? 'একসাথে দিগন্তে') }}">
            </div>
          </div>

          <h6 class="fw-bold text-muted mb-3 border-bottom pb-2">
            <i class="bi bi-receipt me-2"></i>বিলিং নিয়মাবলী (গঠনতন্ত্র অনুযায়ী)
          </h6>
          <div class="row g-3 mb-4">
            <div class="col-md-3">
              <label class="form-label fw-semibold">জমার শুরুর তারিখ <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" name="due_date_start" min="1" max="28" class="form-control"
                       value="{{ old('due_date_start', $settings['due_date_start'] ?? 5) }}" required>
                <span class="input-group-text">তারিখ</span>
              </div>
              <div class="form-text">প্রতি মাসের ৫ তারিখ</div>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">জমার শেষ তারিখ <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" name="due_date_end" min="1" max="28" class="form-control"
                       value="{{ old('due_date_end', $settings['due_date_end'] ?? 15) }}" required>
                <span class="input-group-text">তারিখ</span>
              </div>
              <div class="form-text">প্রতি মাসের ১৫ তারিখ</div>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">বিলম্ব জরিমানা (৳) <span class="text-danger">*</span></label>
              <input type="number" name="late_fee" step="0.01" min="0" class="form-control"
                     value="{{ old('late_fee', $settings['late_fee'] ?? 50) }}" required>
              <div class="form-text">প্রতি মাসে ৳৫০ (গঠনতন্ত্র ধারা ৭)</div>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">স্থগিতাদেশ (মাস)</label>
              <input type="number" name="suspend_after_months" min="1" max="12" class="form-control"
                     value="{{ old('suspend_after_months', $settings['suspend_after_months'] ?? 3) }}">
              <div class="form-text">টানা ৩ মাস চাঁদা না দিলে স্থগিত</div>
            </div>
          </div>

          <h6 class="fw-bold text-muted mb-3 border-bottom pb-2">
            <i class="bi bi-people me-2"></i>সদস্যপদ নিয়মাবলী
          </h6>
          <div class="row g-3 mb-4">
            <div class="col-md-3">
              <label class="form-label fw-semibold">ভর্তি ফি (৳) <span class="text-danger">*</span></label>
              <input type="number" name="entry_fee" step="0.01" min="0" class="form-control"
                     value="{{ old('entry_fee', $settings['entry_fee'] ?? 100) }}" required>
              <div class="form-text">একবার, অফেরতযোগ্য</div>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">শেয়ার মূল্য (৳) <span class="text-danger">*</span></label>
              <input type="number" name="share_value" step="1" min="0" class="form-control"
                     value="{{ old('share_value', $settings['share_value'] ?? 1000) }}" required>
              <div class="form-text">প্রতি শেয়ার ৳১,০০০</div>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">সর্বোচ্চ শেয়ার <span class="text-danger">*</span></label>
              <input type="number" name="max_shares" min="1" max="10" class="form-control"
                     value="{{ old('max_shares', $settings['max_shares'] ?? 2) }}" required>
              <div class="form-text">১ জন সর্বোচ্চ ২টি শেয়ার</div>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">সর্বোচ্চ সদস্য সংখ্যা</label>
              <input type="number" name="max_members" min="20" max="1000" class="form-control"
                     value="{{ old('max_members', $settings['max_members'] ?? 30) }}">
              <div class="form-text">বর্তমানে ৩০ জন</div>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">মুদ্রা চিহ্ন <span class="text-danger">*</span></label>
              <input type="text" name="currency" class="form-control" maxlength="5"
                     value="{{ old('currency', $settings['currency'] ?? '৳') }}" required>
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4">
              <i class="bi bi-save me-1"></i>সেটিংস সংরক্ষণ করুন
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Info box --}}
    <div class="alert alert-info mt-3">
      <i class="bi bi-info-circle me-2"></i>
      <strong>গঠনতন্ত্র অনুযায়ী:</strong> জমার সময়সীমা প্রতি মাসের ৫–১৫ তারিখ। বিলম্বে ৳৫০ জরিমানা।
      টানা ৩ মাস চাঁদা না দিলে সদস্যপদ স্থগিত। ভর্তি ফি ৳১০০, প্রতি শেয়ার ৳১,০০০।
    </div>
  </div>
</div>
@endsection
