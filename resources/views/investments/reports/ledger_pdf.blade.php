<!DOCTYPE html><html><head><meta charset="UTF-8">
<style>body{font-family:'DejaVu Sans',sans-serif;font-size:12px;}
h2{color:#1a5276;} table{width:100%;border-collapse:collapse;margin-top:12px;}
th{background:#1a5276;color:#fff;padding:6px;text-align:left;}
td{padding:5px;border-bottom:1px solid #eee;}</style></head>
<body>
<h2>নবদিগন্ত সমবায় সমিতি — সদস্যভিত্তিক বিনিয়োগ লেজার</h2>
<div><strong>সদস্য:</strong> {{ $member->name }} ({{ $member->member_id }})</div>
<div style="color:#888;font-size:10px">তৈরি: {{ now()->format('d M Y H:i') }}</div>
<table>
<thead><tr><th>#</th><th>প্রকল্প</th><th>পরিমাণ</th><th>লাভ হার</th><th>অবস্থা</th><th>পেমেন্ট</th><th>নিষ্পত্তি</th><th>লাভ/ক্ষতি</th></tr></thead>
<tbody>
@foreach($ledger as $i=>$inv)
<tr>
  <td>{{ $i+1 }}</td>
  <td>{{ $inv->project_name }}</td>
  <td>৳{{ number_format($inv->approved_amount ?? $inv->requested_amount,2) }}</td>
  <td>{{ $inv->approved_profit_ratio ?? $inv->expected_profit_ratio }}%</td>
  <td>{{ $inv->status_label }}</td>
  <td>{{ $inv->payment ? '৳'.number_format($inv->payment->amount,2) : '—' }}</td>
  <td>{{ $inv->settlement ? '৳'.number_format($inv->settlement->return_amount,2) : '—' }}</td>
  <td>{{ $inv->settlement ? (($inv->settlement->actual_profit_loss>=0?'+':''). '৳'.number_format($inv->settlement->actual_profit_loss,2)) : '—' }}</td>
</tr>
@endforeach
</tbody></table>
</body></html>
