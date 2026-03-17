@extends('layouts.app')
@section('title','সদস্য সম্পাদনা')
@section('page-title','সদস্য তথ্য সম্পাদনা')
@section('content')
<div class="row justify-content-center">
<div class="col-xl-10">
<div class="card border-0 shadow-sm">
  <div class="card-header py-3 d-flex align-items-center justify-content-between"
       style="background:linear-gradient(135deg,#7d6608,#d4ac0d);color:#fff">
    <div class="d-flex align-items-center gap-3">
      <i class="bi bi-pencil fs-4"></i>
      <div>
        <div class="fw-bold fs-5">সদস্য তথ্য আপডেট</div>
        <div style="font-size:.85rem;opacity:.85">{{ $member->name }} — {{ $member->member_id }}</div>
      </div>
    </div>
    <span class="badge bg-light text-dark px-3 py-2">{{ $member->member_id }}</span>
  </div>

  <div class="card-body p-4">
  <form method="POST" action="{{ route('members.update', $member) }}" enctype="multipart/form-data">
  @csrf @method('PUT')

  {{-- SECTION 1 --}}
  <div class="section-title d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="color:#1a5276">
    <i class="bi bi-person-badge fs-5"></i><strong>সদস্য পরিচিতি</strong>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-2 text-center">
      <label class="form-label fw-semibold small">ছবি</label>
      <div class="border rounded d-flex align-items-center justify-content-center bg-light"
           style="height:110px;cursor:pointer" onclick="document.getElementById('photoInput').click()">
        @if($member->photo)
          <img id="photoPreview" src="{{ asset('storage/'.$member->photo) }}" class="rounded" style="max-height:105px;max-width:100%">
        @else
          <img id="photoPreview" src="#" alt="" class="d-none rounded" style="max-height:105px;max-width:100%">
          <span id="photoPlaceholder" class="text-muted small text-center px-2">
            <i class="bi bi-camera fs-3 d-block mb-1"></i>ছবি নেই
          </span>
        @endif
      </div>
      <input type="file" name="photo" id="photoInput" accept="image/*" class="d-none"
             onchange="previewPhoto(this)">
    </div>
    <div class="col-md-10">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">নাম <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" value="{{ old('name',$member->name) }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">পিতার নাম</label>
          <input type="text" name="father_name" class="form-control" value="{{ old('father_name',$member->father_name) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">মাতার নাম</label>
          <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name',$member->mother_name) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">স্বামী/স্ত্রীর নাম</label>
          <input type="text" name="spouse_name" class="form-control" value="{{ old('spouse_name',$member->spouse_name) }}">
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">জন্ম তারিখ</label>
      <input type="date" name="date_of_birth" class="form-control"
             value="{{ old('date_of_birth', $member->date_of_birth?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">লিঙ্গ</label>
      <div class="d-flex gap-4 mt-1">
        @foreach(['male'=>'পুরুষ','female'=>'মহিলা'] as $v=>$l)
        <div class="form-check">
          <input class="form-check-input" type="radio" name="gender" value="{{ $v }}"
                 {{ old('gender',$member->gender)==$v?'checked':'' }}>
          <label class="form-check-label">{{ $l }}</label>
        </div>
        @endforeach
      </div>
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">বৈবাহিক অবস্থা</label>
      <div class="d-flex gap-3 mt-1 flex-wrap">
        @foreach(['married'=>'বিবাহিত','unmarried'=>'অবিবাহিত','divorced'=>'তালাকপ্রাপ্ত','widowed'=>'বিধবা'] as $v=>$l)
        <div class="form-check">
          <input class="form-check-input" type="radio" name="marital_status" value="{{ $v }}"
                 {{ old('marital_status',$member->marital_status)==$v?'checked':'' }}>
          <label class="form-check-label">{{ $l }}</label>
        </div>
        @endforeach
      </div>
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">NID / জন্ম সনদ নং</label>
      <input type="text" name="nid_or_birth_cert" class="form-control" value="{{ old('nid_or_birth_cert',$member->nid_or_birth_cert) }}">
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold">মোবাইল</label>
      <input type="text" name="phone" class="form-control" value="{{ old('phone',$member->phone) }}">
    </div>
  </div>

  {{-- Present Address --}}
  <div class="section-title d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="color:#1a5276">
    <i class="bi bi-geo-alt fs-5"></i><strong>বর্তমান ঠিকানা</strong>
  </div>
  <div class="row g-3 mb-4">
    @foreach([['present_village','গ্রাম/বাসা','col-md-4'],['present_post_office','ডাকঘর','col-md-4'],['present_union','ইউনিয়ন/পৌরসভা','col-md-4'],['present_ward','ওয়ার্ড নং','col-md-2'],['present_upazila','উপজেলা','col-md-5'],['present_district','জেলা','col-md-5']] as [$f,$l,$c])
    <div class="{{ $c }}">
      <label class="form-label fw-semibold">{{ $l }}</label>
      <input type="text" name="{{ $f }}" class="form-control" value="{{ old($f,$member->$f) }}">
    </div>
    @endforeach
  </div>

  {{-- Permanent Address --}}
  <div class="section-title d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="color:#1a5276">
    <i class="bi bi-house fs-5"></i><strong>স্থায়ী ঠিকানা</strong>
  </div>
  <div class="row g-3 mb-4">
    @foreach([['permanent_village','গ্রাম/বাসা','col-md-4'],['permanent_post_office','ডাকঘর','col-md-4'],['permanent_union','ইউনিয়ন/পৌরসভা','col-md-4'],['permanent_ward','ওয়ার্ড নং','col-md-2'],['permanent_upazila','উপজেলা','col-md-5'],['permanent_district','জেলা','col-md-5']] as [$f,$l,$c])
    <div class="{{ $c }}">
      <label class="form-label fw-semibold">{{ $l }}</label>
      <input type="text" name="{{ $f }}" class="form-control" value="{{ old($f,$member->$f) }}">
    </div>
    @endforeach
  </div>

  {{-- Nominee --}}
  <div class="section-title d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="color:#1a5276">
    <i class="bi bi-person-heart fs-5"></i><strong>নমিনি পরিচিতি</strong>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-4"><label class="form-label fw-semibold">নমিনির নাম</label><input type="text" name="nominee_name" class="form-control" value="{{ old('nominee_name',$member->nominee_name) }}"></div>
    <div class="col-md-4"><label class="form-label fw-semibold">পিতা/স্বামী</label><input type="text" name="nominee_father_spouse" class="form-control" value="{{ old('nominee_father_spouse',$member->nominee_father_spouse) }}"></div>
    <div class="col-md-4"><label class="form-label fw-semibold">সম্পর্ক</label><input type="text" name="nominee_relation" class="form-control" value="{{ old('nominee_relation',$member->nominee_relation) }}"></div>
    <div class="col-md-4"><label class="form-label fw-semibold">মোবাইল</label><input type="text" name="nominee_phone" class="form-control" value="{{ old('nominee_phone',$member->nominee_phone) }}"></div>
    <div class="col-md-8"><label class="form-label fw-semibold">NID / জন্ম সনদ নং</label><input type="text" name="nominee_nid_or_birth_cert" class="form-control" value="{{ old('nominee_nid_or_birth_cert',$member->nominee_nid_or_birth_cert) }}"></div>
  </div>

  {{-- Membership --}}
  <div class="section-title d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="color:#1a5276">
    <i class="bi bi-cash-coin fs-5"></i><strong>সদস্যপদ তথ্য</strong>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-3"><label class="form-label fw-semibold">ভর্তি তারিখ</label><input type="date" name="join_date" class="form-control" value="{{ old('join_date',$member->join_date->format('Y-m-d')) }}" required></div>
    <div class="col-md-3"><label class="form-label fw-semibold">ভর্তি ফি (৳)</label><input type="number" name="entry_fee" step="0.01" class="form-control" value="{{ old('entry_fee',$member->entry_fee) }}" required></div>
    <div class="col-md-2">
      <label class="form-label fw-semibold">শেয়ার সংখ্যা</label>
      <select name="share_count" class="form-select">
        @for($i=1;$i<=$maxShares;$i++)
          <option value="{{ $i }}" {{ old('share_count',$member->share_count)==$i?'selected':'' }}>{{ $i }}</option>
        @endfor
      </select>
    </div>
    <div class="col-md-3"><label class="form-label fw-semibold">মাসিক চাঁদা (৳)</label><input type="number" name="monthly_deposit" step="0.01" class="form-control" value="{{ old('monthly_deposit',$member->monthly_deposit) }}" required></div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">রেফার্ড বাই</label>
      <select name="referred_by_member_id" class="form-select">
        <option value="">— নির্বাচন করুন —</option>
        @foreach($existingMembers as $m)
          <option value="{{ $m->id }}" {{ old('referred_by_member_id',$member->referred_by_member_id)==$m->id?'selected':'' }}>
            {{ $m->name }} ({{ $m->member_id }})
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">অবস্থা</label>
      <select name="status" class="form-select">
        @foreach(['active'=>'সক্রিয়','inactive'=>'নিষ্ক্রিয়','suspended'=>'স্থগিত'] as $v=>$l)
          <option value="{{ $v }}" {{ old('status',$member->status)==$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="border-top pt-4 d-flex justify-content-between">
    <a href="{{ route('members.show', $member) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> বাতিল</a>
    <button type="submit" class="btn btn-warning px-5"><i class="bi bi-save me-1"></i>আপডেট করুন</button>
  </div>

  </form>
  </div>
</div>
</div>
</div>
@endsection
@push('scripts')
<script>
function previewPhoto(input) {
    if (!input.files || !input.files[0]) return;
    const r = new FileReader();
    r.onload = e => {
        document.getElementById('photoPreview').src = e.target.result;
        document.getElementById('photoPreview').classList.remove('d-none');
        const ph = document.getElementById('photoPlaceholder');
        if (ph) ph.classList.add('d-none');
    };
    r.readAsDataURL(input.files[0]);
}
</script>
@endpush
