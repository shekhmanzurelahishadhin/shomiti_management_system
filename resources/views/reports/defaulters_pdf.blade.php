<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
  h2 { color: #922b21; margin-bottom: 2px; }
  table { width: 100%; border-collapse: collapse; margin-top: 15px; }
  th { background: #922b21; color: #fff; padding: 7px 8px; text-align: left; font-size: 11px; }
  td { padding: 6px 8px; border-bottom: 1px solid #eee; }
  tr:nth-child(even) td { background: #fdf2f2; }
  .total-row td { background: #f8d7da; font-weight: bold; }
</style>
</head>
<body>
<div style="text-align:center;margin-bottom:8px"><img src="{{ public_path('images/logo.jpg') }}" style="width:40px;height:40px;object-fit:contain"></div><h2>নবদিগন্ত সমবায় সমিতি — Defaulter Report</h2>
<div>Period: {{ date('F',mktime(0,0,0,$month,1)) }} {{ $year }}</div>
<div style="color:#888;font-size:11px">Generated: {{ now()->format('d M Y H:i') }}</div>
<table>
  <thead>
    <tr><th>#</th><th>Member</th><th>ID</th><th>Phone</th><th>Amount</th><th>Fine</th><th>Paid</th><th>Due</th><th>Status</th></tr>
  </thead>
  <tbody>
  @foreach($defaulters as $i => $bill)
    <tr>
      <td>{{ $i+1 }}</td>
      <td>{{ $bill->member->name }}</td>
      <td>{{ $bill->member->member_id }}</td>
      <td>{{ $bill->member->phone ?? '—' }}</td>
      <td>৳{{ number_format($bill->amount,2) }}</td>
      <td>{{ $bill->fine > 0 ? '৳'.number_format($bill->fine,2) : '—' }}</td>
      <td>৳{{ number_format($bill->paid_amount,2) }}</td>
      <td>৳{{ number_format($bill->total_due,2) }}</td>
      <td>{{ ucfirst($bill->status) }}</td>
    </tr>
  @endforeach
  <tr class="total-row">
    <td colspan="7" style="text-align:right">Total Outstanding:</td>
    <td colspan="2">৳{{ number_format($defaulters->sum(fn($b)=>$b->total_due),2) }}</td>
  </tr>
  </tbody>
</table>
</body>
</html>
