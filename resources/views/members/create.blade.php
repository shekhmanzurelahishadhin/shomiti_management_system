@extends('layouts.app')
@section('title','নতুন সদস্য নিবন্ধন')
@section('page-title','নতুন সদস্য নিবন্ধন')
@section('content')

{{-- Registration form styled to match official membership form --}}
<div class="row justify-content-center">
<div class="col-xl-10">

<div class="card border-0 shadow-sm mb-4">
  {{-- Header --}}
  <div class="card-header py-3 d-flex align-items-center gap-3"
       style="background:linear-gradient(135deg,#1a5276,#2e86c1);color:#fff">
    <i class="bi bi-person-plus fs-4"></i>
    <div>
      <div class="fw-bold fs-5">নিবন্ধন ফর্ম — নবদিগন্ত সমবায় সমিতি</div>
      <div style="font-size:.85rem;opacity:.8">Registration Form — Nabadiganta Somobai Somiti</div>
    </div>
  </div>

  <div class="card-body p-4">
  <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data">
  @csrf

  {{-- ── SECTION 1: সদস্য পরিচিতি ── --}}
  <div class="section-title d-flex align-items-center gap-2 mb-3 pb-2 border-bottom"
       style="color:#1a5276">
    <i class="bi bi-person-badge fs-5"></i>
    <strong>সদস্য পরিচিতি (Member Information)</strong>
  </div>

  <div class="row g-3 mb-4">

    {{-- Photo upload --}}
    <div class="col-md-2 text-center">
      <label class="form-label fw-semibold small">ছবি (Photo)</label>
      <div class="border rounded d-flex align-items-center justify-content-center bg-light"
           style="height:110px;cursor:pointer" id="photoBox" onclick="document.getElementById('photoInput').click()">
        <img id="photoPreview" src="#" alt="" class="d-none rounded" style="max-height:105px;max-width:100%">
        <span id="photoPlaceholder" class="text-muted small text-center px-2">
          <i class="bi bi-camera fs-3 d-block mb-1"></i>২.৫ সে.মি
        </span>
      </div>
      <input type="file" name="photo" id="photoInput" accept="image/*" class="d-none"
             onchange="previewPhoto(this)">
      <div class="form-text">JPG/PNG, max 2MB</div>
    </div>

    <div class="col-md-10">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">নাম <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name') }}" placeholder="পূর্ণ নাম লিখুন" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">পিতার নাম</label>
          <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}" placeholder="পিতার নাম">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">মাতার নাম</label>
          <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name') }}" placeholder="মাতার নাম">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">স্বামী/স্ত্রীর নাম</label>
          <input type="text" name="spouse_name" class="form-control" value="{{ old('spouse_name') }}" placeholder="স্বামী/স্ত্রীর নাম">
        </div>
      </div>
    </div>

    {{-- Row 2 --}}
    <div class="col-md-3">
      <label class="form-label fw-semibold">জন্ম তারিখ</label>
      <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
    </div>
    <div class="col-md-2">
      <label class="form-label fw-semibold">বয়স</label>
      <input type="text" id="ageDisplay" class="form-control" readonly placeholder="স্বয়ংক্রিয়">
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">লিঙ্গ <span class="text-danger">*</span></label>
      <div class="d-flex gap-4 mt-1">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="gender" value="male"
                 id="male" {{ old('gender','male')=='male'?'checked':'' }}>
          <label class="form-check-label" for="male">পুরুষ</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="gender" value="female"
                 id="female" {{ old('gender')=='female'?'checked':'' }}>
          <label class="form-check-label" for="female">মহিলা</label>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">বৈবাহিক অবস্থা <span class="text-danger">*</span></label>
      <div class="d-flex gap-3 mt-1 flex-wrap">
        @foreach(['married'=>'বিবাহিত','unmarried'=>'অবিবাহিত','divorced'=>'তালাকপ্রাপ্ত','widowed'=>'বিধবা/বিপত্নীক'] as $val=>$lbl)
        <div class="form-check">
          <input class="form-check-input" type="radio" name="marital_status"
                 value="{{ $val }}" id="ms_{{ $val }}"
                 {{ old('marital_status','unmarried')==$val?'checked':'' }}>
          <label class="form-check-label" for="ms_{{ $val }}">{{ $lbl }}</label>
        </div>
        @endforeach
      </div>
    </div>

    <div class="col-md-6">
      <label class="form-label fw-semibold">জাতীয় পরিচয় পত্র / জন্ম সনদ নং</label>
      <input type="text" name="nid_or_birth_cert" class="form-control"
             value="{{ old('nid_or_birth_cert') }}" placeholder="NID / জন্ম নিবন্ধন নম্বর">
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">মোবাইল নম্বর</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="01XXXXXXXXX">
      </div>
    </div>
  </div>

  {{-- ── SECTION 2: বর্তমান ঠিকানা ── --}}
  <div class="section-title d-flex align-items-center gap-2 mb-3 pb-2 border-bottom"
       style="color:#1a5276">
    <i class="bi bi-geo-alt fs-5"></i>
    <strong>বর্তমান ঠিকানা (Present Address)</strong>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <label class="form-label fw-semibold">গ্রাম/বাসা</label>
      <input type="text" name="present_village" class="form-control" value="{{ old('present_village') }}" placeholder="গ্রাম / বাসা নম্বর">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">ডাকঘর</label>
      <input type="text" name="present_post_office" class="form-control" value="{{ old('present_post_office') }}" placeholder="ডাকঘর">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">ইউনিয়ন/পৌরসভা</label>
      <input type="text" name="present_union" class="form-control" value="{{ old('present_union') }}" placeholder="ইউনিয়ন / পৌরসভা">
    </div>
    <div class="col-md-2">
      <label class="form-label fw-semibold">ওয়ার্ড নং</label>
      <input type="text" name="present_ward" class="form-control" value="{{ old('present_ward') }}" placeholder="ওয়ার্ড">
    </div>
    <div class="col-md-5">
      <label class="form-label fw-semibold">উপজেলা</label>
      <input type="text" name="present_upazila" class="form-control" value="{{ old('present_upazila') }}" placeholder="উপজেলা">
    </div>
    <div class="col-md-5">
      <label class="form-label fw-semibold">জেলা</label>
      <input type="text" name="present_district" class="form-control" value="{{ old('present_district') }}" placeholder="জেলা">
    </div>
  </div>

  {{-- ── SECTION 3: স্থায়ী ঠিকানা ── --}}
  <div class="d-flex align-items-center gap-2 mb-2">
    <div class="section-title d-flex align-items-center gap-2 pb-2 border-bottom flex-fill" style="color:#1a5276">
      <i class="bi bi-house fs-5"></i>
      <strong>স্থায়ী ঠিকানা (Permanent Address)</strong>
    </div>
    <div class="ms-3">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="sameAddress" onchange="copyAddress(this)">
        <label class="form-check-label small" for="sameAddress">বর্তমান ঠিকানার মতো</label>
      </div>
    </div>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <label class="form-label fw-semibold">গ্রাম/বাসা</label>
      <input type="text" name="permanent_village" id="perm_village" class="form-control" value="{{ old('permanent_village') }}" placeholder="গ্রাম / বাসা নম্বর">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">ডাকঘর</label>
      <input type="text" name="permanent_post_office" id="perm_post" class="form-control" value="{{ old('permanent_post_office') }}" placeholder="ডাকঘর">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">ইউনিয়ন/পৌরসভা</label>
      <input type="text" name="permanent_union" id="perm_union" class="form-control" value="{{ old('permanent_union') }}" placeholder="ইউনিয়ন / পৌরসভা">
    </div>
    <div class="col-md-2">
      <label class="form-label fw-semibold">ওয়ার্ড নং</label>
      <input type="text" name="permanent_ward" id="perm_ward" class="form-control" value="{{ old('permanent_ward') }}" placeholder="ওয়ার্ড">
    </div>
    <div class="col-md-5">
      <label class="form-label fw-semibold">উপজেলা</label>
      <input type="text" name="permanent_upazila" id="perm_upazila" class="form-control" value="{{ old('permanent_upazila') }}" placeholder="উপজেলা">
    </div>
    <div class="col-md-5">
      <label class="form-label fw-semibold">জেলা</label>
      <input type="text" name="permanent_district" id="perm_district" class="form-control" value="{{ old('permanent_district') }}" placeholder="জেলা">
    </div>
  </div>

  {{-- ── SECTION 4: নমিনি পরিচিতি ── --}}
  <div class="section-title d-flex align-items-center gap-2 mb-3 pb-2 border-bottom"
       style="color:#1a5276">
    <i class="bi bi-person-heart fs-5"></i>
    <strong>নমিনি পরিচিতি (Nominee Information)</strong>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <label class="form-label fw-semibold">নমিনির নাম</label>
      <input type="text" name="nominee_name" class="form-control" value="{{ old('nominee_name') }}" placeholder="নমিনির পূর্ণ নাম">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">পিতা/স্বামী</label>
      <input type="text" name="nominee_father_spouse" class="form-control" value="{{ old('nominee_father_spouse') }}" placeholder="নমিনির পিতা/স্বামীর নাম">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">সম্পর্ক</label>
      <input type="text" name="nominee_relation" class="form-control" value="{{ old('nominee_relation') }}" placeholder="যেমন: স্ত্রী, পুত্র, কন্যা">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">মোবাইল</label>
      <input type="text" name="nominee_phone" class="form-control" value="{{ old('nominee_phone') }}" placeholder="01XXXXXXXXX">
    </div>
    <div class="col-md-8">
      <label class="form-label fw-semibold">জাতীয় পরিচয় পত্র / জন্ম সনদ নং</label>
      <input type="text" name="nominee_nid_or_birth_cert" class="form-control" value="{{ old('nominee_nid_or_birth_cert') }}" placeholder="NID / জন্ম নিবন্ধন নম্বর">
    </div>
  </div>

  {{-- ── SECTION 5: সদস্যপদ ও আর্থিক তথ্য ── --}}
  <div class="section-title d-flex align-items-center gap-2 mb-3 pb-2 border-bottom"
       style="color:#1a5276">
    <i class="bi bi-cash-coin fs-5"></i>
    <strong>সদস্যপদ ও আর্থিক তথ্য (Membership & Financial)</strong>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <label class="form-label fw-semibold">ভর্তি তারিখ <span class="text-danger">*</span></label>
      <input type="date" name="join_date" class="form-control @error('join_date') is-invalid @enderror"
             value="{{ old('join_date', date('Y-m-d')) }}" required>
      @error('join_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">ভর্তি ফি (৳) <span class="text-danger">*</span></label>
      <input type="number" name="entry_fee" step="0.01" min="0" class="form-control"
             value="{{ old('entry_fee', $entryFee) }}" required>
      <div class="form-text text-muted">এককালীন, অফেরতযোগ্য</div>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">শেয়ার সংখ্যা <span class="text-danger">*</span>
        <small class="text-muted">(সর্বোচ্চ {{ $maxShares }}টি)</small>
      </label>
      <select name="share_count" class="form-select" id="shareCount" onchange="calcShareTotal()" required>
        @for($i=1;$i<=$maxShares;$i++)
          <option value="{{ $i }}" {{ old('share_count',1)==$i?'selected':'' }}>{{ $i }} শেয়ার</option>
        @endfor
      </select>
      <div class="form-text">প্রতি শেয়ার = ৳{{ number_format($shareValue) }}</div>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">মোট শেয়ার মূল্য</label>
      <input type="text" id="shareTotalDisplay" class="form-control" readonly
             value="৳{{ number_format($shareValue) }}">
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">মাসিক চাঁদা (৳) <span class="text-danger">*</span></label>
      <input type="number" name="monthly_deposit" step="0.01" min="0"
             class="form-control @error('monthly_deposit') is-invalid @enderror"
             value="{{ old('monthly_deposit', 500) }}" required>
      <div class="form-text">জমার সময়সীমা: প্রতি মাসের ৫-১৫ তারিখ</div>
      @error('monthly_deposit')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">যাহারা মাধ্যমে ভর্তি (নাম ও সদস্য নং)</label>
      <select name="referred_by_member_id" class="form-select">
        <option value="">— নির্বাচন করুন —</option>
        @foreach($existingMembers as $m)
          <option value="{{ $m->id }}" {{ old('referred_by_member_id')==$m->id?'selected':'' }}>
            {{ $m->name }} ({{ $m->member_id }})
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">অবস্থা <span class="text-danger">*</span></label>
      <select name="status" class="form-select" required>
        <option value="active"    {{ old('status','active')=='active'   ?'selected':'' }}>সক্রিয়</option>
        <option value="inactive"  {{ old('status')=='inactive' ?'selected':'' }}>নিষ্ক্রিয়</option>
        <option value="suspended" {{ old('status')=='suspended'?'selected':'' }}>স্থগিত</option>
      </select>
    </div>
  </div>

  {{-- ── Summary box ── --}}
  <div class="alert alert-info border-0 py-3 mb-4" style="background:#e8f4fd">
    <div class="row text-center g-2">
      <div class="col-4">
        <div class="text-muted small">ভর্তি ফি</div>
        <div class="fw-bold text-primary" id="summaryEntry">৳{{ number_format($entryFee, 0) }}</div>
      </div>
      <div class="col-4">
        <div class="text-muted small">শেয়ার মূল্য</div>
        <div class="fw-bold text-success" id="summaryShare">৳{{ number_format($shareValue, 0) }}</div>
      </div>
      <div class="col-4">
        <div class="text-muted small">প্রথমবার জমার পরিমাণ</div>
        <div class="fw-bold text-warning" id="summaryTotal">৳{{ number_format($entryFee + $shareValue, 0) }}</div>
      </div>
    </div>
  </div>

  {{-- ── Submit ── --}}
  <div class="border-top pt-4 d-flex justify-content-between align-items-center">
    <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> বাতিল
    </a>
    <div class="d-flex gap-3">
      <button type="submit" class="btn btn-primary px-5">
        <i class="bi bi-person-check me-2"></i>সদস্য নিবন্ধন করুন
      </button>
    </div>
  </div>

  </form>
  </div>
</div>

</div>
</div>
@endsection

@push('scripts')
<script>
// Photo preview
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photoPreview').src = e.target.result;
            document.getElementById('photoPreview').classList.remove('d-none');
            document.getElementById('photoPlaceholder').classList.add('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Age calculator
document.querySelector('[name=date_of_birth]')?.addEventListener('change', function() {
    if (!this.value) return;
    const dob  = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    document.getElementById('ageDisplay').value = age + ' বছর';
});

// Share calculator
const shareValue = {{ $shareValue }};
const entryFee   = {{ $entryFee }};

function calcShareTotal() {
    const count = parseInt(document.getElementById('shareCount').value) || 1;
    const total = count * shareValue;
    document.getElementById('shareTotalDisplay').value = '৳' + total.toLocaleString('en-IN');
    document.getElementById('summaryShare').textContent = '৳' + total.toLocaleString('en-IN');
    document.getElementById('summaryTotal').textContent = '৳' + (entryFee + total).toLocaleString('en-IN');
}
calcShareTotal();

// Copy present→permanent address
function copyAddress(cb) {
    const fields = ['village','post_office','union','ward','upazila','district'];
    fields.forEach(f => {
        const src = document.querySelector(`[name=present_${f}]`);
        const dst = document.getElementById(`perm_${f}`);
        if (src && dst) dst.value = cb.checked ? src.value : '';
    });
}
</script>
@endpush
