<!DOCTYPE html><html><head><meta charset="UTF-8">
<style>
body{font-family:'DejaVu Sans',sans-serif;font-size:12px;}
.center{text-align:center;} .title{font-size:18px;font-weight:bold;color:#1c2833;}
table{width:100%;border-collapse:collapse;margin-top:12px;}
td{padding:6px 5px;} .label{color:#777;} .border-top td{border-top:2px solid #eee;}
.amount{font-size:22px;font-weight:bold;color:#1a5276;text-align:right;}
.footer{text-align:center;font-size:10px;color:#aaa;margin-top:20px;border-top:1px solid #eee;padding-top:8px;}
.box{border:2px solid #1c2833;padding:20px;max-width:480px;margin:0 auto;}
.profit{color:#1e8449;font-weight:bold;} .loss{color:#c0392b;font-weight:bold;}
</style></head><body>
<div class="box">
  <div class="center"><div style="text-align:center;margin-bottom:6px"><img src="{{ public_path('images/logo.jpg') }}" style="width:40px;height:40px;object-fit:contain"></div><div class="title">নবদিগন্ত সমবায় সমিতি</div>
  <div style="color:#555;font-size:11px">বিনিয়োগ নিষ্পত্তি ভাউচার</div>
  <div style="color:#888;font-size:10px">ভাউচার নং: {{ $settlement->voucher_number }}</div></div>
  <table>
    <tr><td class="label">সদস্য</td><td style="text-align:right;font-weight:bold">{{ $settlement->member->name }} ({{ $settlement->member->member_id }})</td></tr>
    <tr><td class="label">প্রকল্প</td><td style="text-align:right">{{ $settlement->investmentRequest->project_name }}</td></tr>
    <tr><td class="label">বিনিয়োগ পরিমাণ</td><td style="text-align:right">৳{{ number_format($settlement->investment_amount,2) }}</td></tr>
    <tr><td class="label">{{ $settlement->outcome_label }}</td>
        <td style="text-align:right" class="{{ $settlement->actual_profit_loss>=0?'profit':'loss' }}">
          {{ $settlement->actual_profit_loss>=0?'+':'' }}৳{{ number_format($settlement->actual_profit_loss,2) }}
        </td>
    </tr>
    <tr><td class="label">পদ্ধতি</td><td style="text-align:right">{{ strtoupper($settlement->payment_method) }}</td></tr>
    @if($settlement->reference)<tr><td class="label">রেফারেন্স</td><td style="text-align:right">{{ $settlement->reference }}</td></tr>@endif
    <tr><td class="label">তারিখ</td><td style="text-align:right">{{ $settlement->settlement_date->format('d M Y') }}</td></tr>
    <tr class="border-top"><td style="font-size:14px;font-weight:bold;padding-top:10px">মোট রিটার্ন পরিমাণ</td><td class="amount">৳{{ number_format($settlement->return_amount,2) }}</td></tr>
  </table>
  <div class="footer">বিনিয়োগ নিষ্পন্ন — {{ $settlement->outcome_label }}<br>তৈরি: {{ now()->format('d M Y H:i') }}</div>
</div></body></html>
