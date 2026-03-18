<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
  h2 { color: #1a5276; }
  table { width: 100%; border-collapse: collapse; margin-top: 15px; }
  th { background: #1a5276; color: #fff; padding: 7px 8px; text-align: left; }
  td { padding: 6px 8px; border-bottom: 1px solid #eee; }
  tr:nth-child(even) td { background: #f8f9fa; }
  .total-row td { background: #d1e7dd; font-weight: bold; }
</style>
</head>
<body>
<div style="text-align:center;margin-bottom:8px"><img src="{{ public_path('images/logo.jpg') }}" style="width:40px;height:40px;object-fit:contain"></div><h2>নবদিগন্ত সমবায় সমিতি</h2>
<div><strong>Member:</strong> {{ $member->name }} ({{ $member->member_id }})</div>
<div><strong>Year:</strong> {{ $year }}</div>
<div style="color:#888;font-size:11px">Generated: {{ now()->format('d M Y H:i') }}</div>
<table>
  <thead>
    <tr><th>Month</th><th>Amount</th><th>Fine</th><th>Discount</th><th>Paid</th><th>Due</th><th>Status</th></tr>
  </thead>
  <tbody>
  @php $totalBilled=0; $totalPaid=0; $totalDue=0; @endphp
  @for($m=1;$m<=12;$m++)
    @php $bill = $bills->firstWhere('bill_month',$m); @endphp
    <tr>
      <td>{{ date('F',mktime(0,0,0,$m,1)) }}</td>
      @if($bill)
        @php $totalBilled+=$bill->amount; $totalPaid+=$bill->paid_amount; $totalDue+=$bill->total_due; @endphp
        <td>৳{{ number_format($bill->amount,2) }}</td>
        <td>{{ $bill->fine>0?'৳'.number_format($bill->fine,2):'—' }}</td>
        <td>{{ $bill->discount>0?'৳'.number_format($bill->discount,2):'—' }}</td>
        <td>৳{{ number_format($bill->paid_amount,2) }}</td>
        <td>৳{{ number_format($bill->total_due,2) }}</td>
        <td>{{ ucfirst($bill->status) }}</td>
      @else
        <td colspan="6">No bill</td><td>—</td>
      @endif
    </tr>
  @endfor
  <tr class="total-row">
    <td>Total</td>
    <td>৳{{ number_format($totalBilled,2) }}</td>
    <td></td><td></td>
    <td>৳{{ number_format($totalPaid,2) }}</td>
    <td>৳{{ number_format($totalDue,2) }}</td>
    <td></td>
  </tr>
  </tbody>
</table>
</body>
</html>
