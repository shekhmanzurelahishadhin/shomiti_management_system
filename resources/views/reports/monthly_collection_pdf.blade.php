<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
  h2 { color: #1a5276; margin-bottom: 2px; }
  table { width: 100%; border-collapse: collapse; margin-top: 15px; }
  th { background: #1a5276; color: #fff; padding: 7px 8px; text-align: left; font-size: 11px; }
  td { padding: 6px 8px; border-bottom: 1px solid #eee; }
  tr:nth-child(even) td { background: #f8f9fa; }
  .total-row td { background: #d1e7dd; font-weight: bold; }
  .header { margin-bottom: 20px; }
  .badge { padding: 2px 8px; border-radius: 4px; font-size: 11px; background: #e2e8f0; }
</style>
</head>
<body>
<div class="header">
  <h2>নবদিগন্ত সমবায় সমিতি</h2>
  <div>Monthly Collection Report — {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</div>
  <div style="color:#888;font-size:11px">Generated: {{ now()->format('d M Y H:i') }}</div>
</div>
<table>
  <thead>
    <tr><th>#</th><th>Member</th><th>Member ID</th><th>Bill Period</th><th>Amount</th><th>Method</th><th>Date</th></tr>
  </thead>
  <tbody>
  @foreach($payments as $i => $p)
    <tr>
      <td>{{ $i+1 }}</td>
      <td>{{ $p->member->name }}</td>
      <td>{{ $p->member->member_id }}</td>
      <td>{{ $p->bill ? $p->bill->month_name.' '.$p->bill->bill_year : '—' }}</td>
      <td>৳{{ number_format($p->amount,2) }}</td>
      <td>{{ strtoupper($p->payment_method) }}</td>
      <td>{{ $p->payment_date->format('d M Y') }}</td>
    </tr>
  @endforeach
  <tr class="total-row">
    <td colspan="4" style="text-align:right">Total Collection:</td>
    <td colspan="3">৳{{ number_format($total,2) }}</td>
  </tr>
  </tbody>
</table>
</body>
</html>
