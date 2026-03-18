<!DOCTYPE html><html><head><meta charset="UTF-8">
<style>body{font-family:'DejaVu Sans',sans-serif;font-size:11px;}
h2{color:#1a5276;} table{width:100%;border-collapse:collapse;margin-top:12px;}
th{background:#1a5276;color:#fff;padding:6px 8px;text-align:left;font-size:10px;}
td{padding:5px 8px;border-bottom:1px solid #eee;}
tr:nth-child(even) td{background:#f8f9fa;}
.total-row td{background:#d1e7dd;font-weight:bold;}</style></head>
<body>
<h2>নবদিগন্ত সমবায় সমিতি — বিনিয়োগ রিপোর্ট</h2>
<div style="color:#888;font-size:10px">তৈরি: {{ now()->format('d M Y H:i') }}</div>
<table>
<thead><tr><th>#</th><th>সদস্য</th><th>প্রকল্প</th><th>পরিমাণ</th><th>অবস্থা</th><th>তারিখ</th></tr></thead>
<tbody>
@php
  $rows = isset($data) ? $data : (isset($investments) ? $investments : (isset($settlements) ? $settlements : (isset($payments) ? $payments : collect())));
@endphp
@foreach($rows as $i=>$row)
<tr>
  <td>{{ $i+1 }}</td>
  <td>{{ isset($row->member) ? $row->member->name : ($row->investmentRequest->member->name ?? '—') }}</td>
  <td>{{ isset($row->project_name) ? $row->project_name : $row->investmentRequest->project_name }}</td>
  <td>৳{{ number_format(isset($row->approved_amount) ? ($row->approved_amount ?? $row->requested_amount) : ($row->amount ?? $row->investment_amount ?? $row->return_amount ?? 0), 2) }}</td>
  <td>{{ isset($row->status_label) ? $row->status_label : ($row->outcome_label ?? '—') }}</td>
  <td>{{ ($row->submitted_date ?? $row->payment_date ?? $row->settlement_date ?? $row->approved_return_date ?? now())->format('d M Y') }}</td>
</tr>
@endforeach
</tbody></table>
</body></html>
