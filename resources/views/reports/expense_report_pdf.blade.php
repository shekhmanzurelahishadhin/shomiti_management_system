<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
  h2 { color: #922b21; }
  table { width: 100%; border-collapse: collapse; margin-top: 15px; }
  th { background: #922b21; color: #fff; padding: 7px 8px; text-align: left; }
  td { padding: 6px 8px; border-bottom: 1px solid #eee; }
  tr:nth-child(even) td { background: #fdf2f2; }
  .total-row td { background: #f8d7da; font-weight: bold; }
</style>
</head>
<body>
<div style="text-align:center;margin-bottom:8px"><img src="{{ public_path('images/logo.jpg') }}" style="width:40px;height:40px;object-fit:contain"></div><h2>নবদিগন্ত সমবায় সমিতি — Expense Report</h2>
<div style="color:#888;font-size:11px">Generated: {{ now()->format('d M Y H:i') }}</div>
<table>
  <thead>
    <tr><th>#</th><th>Title</th><th>Category</th><th>Amount</th><th>Date</th></tr>
  </thead>
  <tbody>
  @foreach($expenses as $i => $e)
    <tr>
      <td>{{ $i+1 }}</td>
      <td>{{ $e->title }}</td>
      <td>{{ ucfirst($e->category) }}</td>
      <td>৳{{ number_format($e->amount,2) }}</td>
      <td>{{ $e->expense_date->format('d M Y') }}</td>
    </tr>
  @endforeach
  <tr class="total-row">
    <td colspan="3" style="text-align:right">Total:</td>
    <td colspan="2">৳{{ number_format($total,2) }}</td>
  </tr>
  </tbody>
</table>
</body>
</html>
