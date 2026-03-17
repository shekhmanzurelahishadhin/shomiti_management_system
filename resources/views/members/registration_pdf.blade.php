<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #222; }
  .page { width: 100%; padding: 15px; }

  /* Header */
  .header { text-align: center; border: 3px solid #2e7d32; border-radius: 6px;
            padding: 10px; margin-bottom: 12px; position: relative; }
  .header .logo-area { margin-bottom: 6px; }
  .header .somity-name { font-size: 20px; font-weight: bold; color: #2e7d32; }
  .header .somity-sub  { font-size: 12px; color: #555; }
  .header .tagline     { font-size: 11px; color: #888; font-style: italic; }
  .form-title { font-size: 16px; font-weight: bold; text-align: center;
                background: #2e7d32; color: #fff; padding: 6px; margin-bottom: 10px; border-radius: 3px; }

  /* Boxes */
  .top-right { position: absolute; top: 10px; right: 15px; }
  .info-box  { border: 1px solid #aaa; padding: 3px 8px; font-size: 10px;
               text-align: center; margin-bottom: 4px; min-width: 120px; }
  .info-box .label { font-size: 9px; color: #777; }

  /* Photo box */
  .photo-box { width: 70px; height: 85px; border: 1.5px dashed #888;
               display: inline-block; vertical-align: top;
               text-align: center; font-size: 9px; color: #aaa;
               padding-top: 28px; margin-right: 10px; }

  /* Sections */
  .section { border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px; overflow: hidden; }
  .section-title { background: #e8f5e9; color: #2e7d32; font-weight: bold;
                   font-size: 11px; padding: 4px 10px; border-bottom: 1px solid #ccc; }
  .section-body  { padding: 8px 10px; }

  /* Fields */
  table.fields { width: 100%; border-collapse: collapse; }
  table.fields td { padding: 3px 5px; vertical-align: bottom; }
  .field-label { font-size: 9px; color: #666; white-space: nowrap; }
  .field-line  { border-bottom: 1px solid #555; display: block; min-width: 60px;
                 padding-bottom: 1px; font-size: 11px; color: #111; min-height: 16px; }
  .field-value { font-size: 11px; color: #111; }

  /* NID box */
  .nid-boxes { display: inline-flex; gap: 1px; }
  .nid-box   { width: 15px; height: 16px; border: 1px solid #555;
               text-align: center; font-size: 10px; line-height: 16px; }

  /* Checkboxes */
  .checkbox-group { display: flex; gap: 12px; flex-wrap: wrap; }
  .cb-item { display: flex; align-items: center; gap: 4px; font-size: 10px; }
  .cb-box  { width: 12px; height: 12px; border: 1px solid #555;
             text-align: center; font-size: 9px; line-height: 12px; display: inline-block; }

  /* Signature row */
  .sig-row { display: flex; justify-content: space-between; margin-top: 12px;
             padding-top: 10px; border-top: 1px solid #ddd; }
  .sig-box { text-align: center; width: 28%; }
  .sig-line { border-top: 1px solid #555; margin-top: 30px; padding-top: 3px; font-size: 9px; color: #555; }

  /* Footer */
  .footer { text-align: center; font-size: 9px; color: #888; margin-top: 8px;
            border-top: 1px dashed #ccc; padding-top: 6px; }
</style>
</head>
<body>
<div class="page">

  {{-- Header --}}
  <div class="header">
    <div class="top-right">
      @if($member->photo)
        <img src="{{ public_path('storage/'.$member->photo) }}" width="70" height="85"
             style="border:1px solid #ccc;object-fit:cover">
      @else
        <div class="photo-box">ছবি<br>২.৫ সে.মি</div>
      @endif
      <div style="margin-top:5px">
        <div class="info-box"><div class="label">সদস্য নং</div><strong>{{ $member->member_id }}</strong></div>
        <div class="info-box"><div class="label">তারিখ</div>{{ $member->join_date->format('d/m/Y') }}</div>
      </div>
    </div>
    <div class="somity-name">নবদিগন্ত সমবায় সমিতি</div>
    <div class="somity-sub">Nabadiganta Somobai Somiti</div>
    <div class="tagline">একসাথে দিগন্তে — Together on the Horizon</div>
    <div style="font-size:9px;color:#888;margin-top:3px">
      ৪১৭-৪১৮/এ, তেজগাঁও শিল্প এলাকা, ঢাকা-১২০৮ &nbsp;|&nbsp;
      +880 1722-784150 &nbsp;|&nbsp; nabadigantaltd@gmail.com
    </div>
  </div>

  <div class="form-title">নিবন্ধন ফর্ম (Registration Form)</div>

  {{-- SECTION 1: সদস্য পরিচিতি --}}
  <div class="section">
    <div class="section-title">সদস্য পরিচিতি (Member Information)</div>
    <div class="section-body">
      <table class="fields">
        <tr>
          <td width="50%">
            <span class="field-label">নাম:</span>
            <span class="field-line">{{ $member->name }}</span>
          </td>
          <td width="50%">
            <span class="field-label">পিতার নাম:</span>
            <span class="field-line">{{ $member->father_name ?? '' }}</span>
          </td>
        </tr>
        <tr>
          <td>
            <span class="field-label">মাতার নাম:</span>
            <span class="field-line">{{ $member->mother_name ?? '' }}</span>
          </td>
          <td>
            <span class="field-label">স্বামী/স্ত্রীর নাম:</span>
            <span class="field-line">{{ $member->spouse_name ?? '' }}</span>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <span class="field-label">বর্তমান ঠিকানা — গ্রাম/বাসা:</span>
            <span class="field-line">{{ $member->present_village ?? '' }}</span>
          </td>
        </tr>
        <tr>
          <td>
            <span class="field-label">ডাকঘর:</span>
            <span class="field-line">{{ $member->present_post_office ?? '' }}</span>
          </td>
          <td>
            <span class="field-label">ইউনিয়ন/পৌরসভা:</span>
            <span class="field-line">{{ $member->present_union ?? '' }}</span>
          </td>
        </tr>
        <tr>
          <td>
            <span class="field-label">ওয়ার্ড নং: </span>
            <span class="field-value">{{ $member->present_ward ?? '___' }}</span>
            &nbsp;&nbsp;
            <span class="field-label">উপজেলা:</span>
            <span class="field-value">{{ $member->present_upazila ?? '________________' }}</span>
          </td>
          <td>
            <span class="field-label">জেলা:</span>
            <span class="field-line">{{ $member->present_district ?? '' }}</span>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <span class="field-label">মোবাইল:</span>
            <div class="nid-boxes">
              @for($i=0;$i<11;$i++)
                <div class="nid-box">{{ isset($member->phone[$i]) ? $member->phone[$i] : '' }}</div>
              @endfor
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <span class="field-label">স্থায়ী ঠিকানা — গ্রাম/বাসা:</span>
            <span class="field-line">{{ $member->permanent_village ?? '' }}</span>
          </td>
        </tr>
        <tr>
          <td>
            <span class="field-label">ইউনিয়ন/পৌরসভা:</span>
            <span class="field-line">{{ $member->permanent_union ?? '' }}</span>
          </td>
          <td>
            <span class="field-label">ওয়ার্ড নং: </span>
            <span class="field-value">{{ $member->permanent_ward ?? '___' }}</span>
            &nbsp;&nbsp;
            <span class="field-label">উপজেলা:</span>
            <span class="field-value">{{ $member->permanent_upazila ?? '' }}</span>
          </td>
        </tr>
        <tr>
          <td>
            <span class="field-label">জেলা:</span>
            <span class="field-line">{{ $member->permanent_district ?? '' }}</span>
          </td>
          <td>
            <span class="field-label">জন্ম তারিখ:</span>
            <span class="field-value">
              {{ $member->date_of_birth ? $member->date_of_birth->format('d/m/Y') : '___/___/______' }}
            </span>
            &nbsp;
            <span class="field-label">বয়স:</span>
            <span class="field-value">
              {{ $member->date_of_birth ? $member->date_of_birth->age : '___' }} বছর
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <span class="field-label">লিঙ্গ (✓): </span>
            <span class="cb-box">{{ $member->gender=='male' ? '✓' : '' }}</span> পুরুষ &nbsp;
            <span class="cb-box">{{ $member->gender=='female' ? '✓' : '' }}</span> মহিলা
          </td>
          <td>
            <span class="field-label">বৈবাহিক অবস্থা (✓): </span>
            <span class="cb-box">{{ $member->marital_status=='married' ? '✓' : '' }}</span> বিবাহিত &nbsp;
            <span class="cb-box">{{ $member->marital_status=='unmarried' ? '✓' : '' }}</span> অবিবাহিত
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <span class="field-label">জাতীয় পরিচয় পত্র / জন্ম সনদ নং:</span>
            <div class="nid-boxes" style="margin-left:5px">
              @php $nid = $member->nid_or_birth_cert ?? ''; @endphp
              @for($i=0;$i<17;$i++)
                <div class="nid-box">{{ $nid[$i] ?? '' }}</div>
              @endfor
            </div>
          </td>
        </tr>
      </table>
    </div>
  </div>

  {{-- SECTION 2: নমিনি পরিচিতি --}}
  <div class="section">
    <div class="section-title">নমিনি পরিচিতি (Nominee Information)</div>
    <div class="section-body">
      <table class="fields">
        <tr>
          <td width="50%">
            <span class="field-label">নমিনির নাম:</span>
            <span class="field-line">{{ $member->nominee_name ?? '' }}</span>
          </td>
          <td width="50%">
            <span class="field-label">পিতা/স্বামী:</span>
            <span class="field-line">{{ $member->nominee_father_spouse ?? '' }}</span>
          </td>
        </tr>
        <tr>
          <td>
            <span class="field-label">সম্পর্ক:</span>
            <span class="field-line">{{ $member->nominee_relation ?? '' }}</span>
          </td>
          <td>
            <span class="field-label">মোবাইল:</span>
            <div class="nid-boxes">
              @php $np = $member->nominee_phone ?? ''; @endphp
              @for($i=0;$i<11;$i++)
                <div class="nid-box">{{ $np[$i] ?? '' }}</div>
              @endfor
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <span class="field-label">জাতীয় পরিচয় পত্র / জন্ম সনদ নং:</span>
            <div class="nid-boxes" style="margin-left:5px">
              @php $nnid = $member->nominee_nid_or_birth_cert ?? ''; @endphp
              @for($i=0;$i<17;$i++)
                <div class="nid-box">{{ $nnid[$i] ?? '' }}</div>
              @endfor
            </div>
          </td>
        </tr>
      </table>
    </div>
  </div>

  {{-- SECTION 3: আর্থিক তথ্য --}}
  <div class="section">
    <div class="section-title">সদস্যপদ তথ্য (Membership Details)</div>
    <div class="section-body">
      <table class="fields">
        <tr>
          <td width="33%">
            <span class="field-label">ভর্তি তারিখ:</span>
            <span class="field-line">{{ $member->join_date->format('d/m/Y') }}</span>
          </td>
          <td width="33%">
            <span class="field-label">ভর্তি ফি:</span>
            <span class="field-line">৳{{ number_format($member->entry_fee, 2) }} (অগ্রিম/প্রাপ্তি)</span>
          </td>
          <td width="34%">
            <span class="field-label">জমাকৃত অর্থ:</span>
            <span class="field-line">৳{{ number_format($member->share_value, 2) }}</span>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <span class="field-label">টাকা (কথায়):</span>
            <span class="field-line">&nbsp;</span>
          </td>
          <td>
            <span class="field-label">শেয়ার সংখ্যা:</span>
            <span class="field-value fw-bold">{{ $member->share_count }}টি (প্রতিটি ৳১,০০০)</span>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <span class="field-label">যাহারা মাধ্যমে ভর্তি (নাম):</span>
            <span class="field-line">{{ $member->referredBy ? $member->referredBy->name : '' }}</span>
          </td>
          <td>
            <span class="field-label">সদস্য নং:</span>
            <span class="field-value">{{ $member->referredBy ? $member->referredBy->member_id : '' }}</span>
          </td>
        </tr>
        <tr>
          <td colspan="3" style="padding-top:8px">
            <span class="field-label">আবেদনকারীর স্বাক্ষর:</span>
            <span class="field-line" style="min-width:180px">&nbsp;</span>
          </td>
        </tr>
      </table>
    </div>
  </div>

  {{-- Signature row --}}
  <div class="sig-row">
    <div class="sig-box">
      <div class="sig-line">কোষাধ্যক্ষ<br>(স্বাক্ষর)</div>
    </div>
    <div class="sig-box">
      <div class="sig-line">সাধারণ সম্পাদক<br>(স্বাক্ষর)</div>
    </div>
    <div class="sig-box">
      <div class="sig-line">সভাপতি<br>(স্বাক্ষর)</div>
    </div>
  </div>

  <div class="footer">
    নবদিগন্ত সমবায় সমিতি — ৪১৭-৪১৮/এ, তেজগাঁও শিল্প এলাকা, ঢাকা-১২০৮ &nbsp;|&nbsp;
    +880 1722-784150 &nbsp;|&nbsp; nabadigantaltd@gmail.com
  </div>

</div>
</body>
</html>
