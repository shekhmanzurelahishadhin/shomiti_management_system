@extends('layouts.app')
@section('title','নতুন নির্বাচন')
@section('page-title','নতুন কমিটি নির্বাচন তৈরি')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">
<div class="card">
  <div class="card-header py-3" style="background:linear-gradient(135deg,#1a5276,#2e86c1);color:#fff">
    <i class="bi bi-person-badge me-2 fs-5"></i>বার্ষিক কমিটি নির্বাচন
  </div>
  <div class="card-body p-4">
  <form method="POST" action="{{ route('elections.store') }}" id="electionForm">
  @csrf

  <div class="row g-3 mb-4">
    <div class="col-md-8">
      <label class="form-label fw-semibold">নির্বাচনের শিরোনাম <span class="text-danger">*</span></label>
      <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
             value="{{ old('title', date('Y').' সালের বার্ষিক কমিটি নির্বাচন') }}" required>
      @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold">নির্বাচনের বছর <span class="text-danger">*</span></label>
      <input type="number" name="election_year" class="form-control" min="2020" max="2100"
             value="{{ old('election_year', date('Y')) }}" required>
    </div>
    <div class="col-12">
      <label class="form-label fw-semibold">বিবরণ</label>
      <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
    </div>
  </div>

  <div class="row g-3 mb-4 p-3 bg-light rounded">
    <h6 class="fw-bold text-muted mb-1"><i class="bi bi-calendar me-2"></i>তারিখ নির্ধারণ</h6>
    <div class="col-md-3">
      <label class="form-label fw-semibold">মনোনয়ন শুরু <span class="text-danger">*</span></label>
      <input type="date" name="nomination_start" class="form-control"
             value="{{ old('nomination_start') }}" required>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">মনোনয়ন শেষ <span class="text-danger">*</span></label>
      <input type="date" name="nomination_end" class="form-control"
             value="{{ old('nomination_end') }}" required>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">ভোটগ্রহণ শুরু <span class="text-danger">*</span></label>
      <input type="date" name="voting_start" class="form-control"
             value="{{ old('voting_start') }}" required>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold">ভোটগ্রহণ শেষ <span class="text-danger">*</span></label>
      <input type="date" name="voting_end" class="form-control"
             value="{{ old('voting_end') }}" required>
    </div>
  </div>

  {{-- Positions --}}
  <div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h6 class="fw-bold text-muted mb-0"><i class="bi bi-list-ul me-2"></i>নির্বাচনী পদসমূহ</h6>
      <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPosition()">
        <i class="bi bi-plus me-1"></i>পদ যোগ করুন
      </button>
    </div>
    <div id="positionsList">
      @php
        $defaultPositions = ['সভাপতি','সহ-সভাপতি','সাধারণ সম্পাদক','সহ-সাধারণ সম্পাদক','কোষাধ্যক্ষ','দপ্তর সম্পাদক'];
      @endphp
      @foreach($defaultPositions as $i => $pos)
      <div class="row g-2 mb-2 position-row" id="pos_{{ $i }}">
        <div class="col-md-7">
          <input type="text" name="positions[{{ $i }}][name]" class="form-control"
                 value="{{ old("positions.{$i}.name", $pos) }}"
                 placeholder="পদের নাম" required>
        </div>
        <div class="col-md-3">
          <div class="input-group">
            <span class="input-group-text text-muted small">আসন</span>
            <input type="number" name="positions[{{ $i }}][seats]" class="form-control"
                   value="{{ old("positions.{$i}.seats", 1) }}" min="1" max="10" required>
          </div>
        </div>
        <div class="col-md-2">
          @if($i > 0)
          <button type="button" class="btn btn-outline-danger btn-sm w-100"
                  onclick="removePosition({{ $i }})">
            <i class="bi bi-trash"></i>
          </button>
          @endif
        </div>
      </div>
      @endforeach
    </div>

    {{-- Existing member position quick-add --}}
    <div class="mt-2">
      <select id="quickAddPos" class="form-select form-select-sm" style="max-width:280px;display:inline-block">
        @foreach($positions as $p)
          <option>{{ $p }}</option>
        @endforeach
      </select>
      <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
              onclick="quickAddPosition()">+ দ্রুত যোগ</button>
    </div>
  </div>

  <div class="border-top pt-4 d-flex justify-content-between">
    <a href="{{ route('elections.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>বাতিল
    </a>
    <button type="submit" class="btn btn-primary px-4">
      <i class="bi bi-check-circle me-1"></i>নির্বাচন তৈরি করুন
    </button>
  </div>
  </form>
  </div>
</div>
</div>
</div>
@endsection
@push('scripts')
<script>
let posIdx = {{ count($defaultPositions ?? []) }};

function addPosition(name='', seats=1) {
    const list = document.getElementById('positionsList');
    const div  = document.createElement('div');
    div.className = 'row g-2 mb-2 position-row';
    div.id = 'pos_' + posIdx;
    div.innerHTML = `
      <div class="col-md-7">
        <input type="text" name="positions[${posIdx}][name]" class="form-control"
               value="${name}" placeholder="পদের নাম" required>
      </div>
      <div class="col-md-3">
        <div class="input-group">
          <span class="input-group-text text-muted small">আসন</span>
          <input type="number" name="positions[${posIdx}][seats]" class="form-control"
                 value="${seats}" min="1" max="10" required>
        </div>
      </div>
      <div class="col-md-2">
        <button type="button" class="btn btn-outline-danger btn-sm w-100"
                onclick="removePosition(${posIdx})">
          <i class="bi bi-trash"></i>
        </button>
      </div>`;
    list.appendChild(div);
    posIdx++;
}

function removePosition(id) {
    document.getElementById('pos_' + id)?.remove();
}

function quickAddPosition() {
    const sel = document.getElementById('quickAddPos');
    addPosition(sel.value, 1);
}
</script>
@endpush
