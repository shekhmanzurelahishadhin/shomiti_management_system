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
  .summary { display: flex; gap: 30px; margin: 10px 0; }
  .stat { padding: 8px 15px; border-radius: 6px; }
</style>
</head>
<body>
<h2>নবদিগন্ত সমবায় সমিতি — Annual Summary {{ $year }}</h2>
<div style="color:#888;font-size:11px">Generated: {{ now()->format('d M Y H:i') }}</div>
<table>
  <thead>
    <tr><th>Month</th><th>Billed</th><th>Collected</th><th>Expenses</th><th>Balance</th></tr>
  </thead>
  <tbody>
  @foreach($monthlyData as $row)
    <tr>
      <td>{{ $row['month'] }}</td>
      <td>৳{{ number_format($row['billed'],2) }}</td>
      <td>৳{{ number_format($row['collected'],2) }}</td>
      <td>৳{{ number_format($row['expenses'],2) }}</td>
      <td>৳{{ number_format($row['balance'],2) }}</td>
    </tr>
  @endforeach
  <tr class="total-row">
    <td>Total</td>
    <td>৳{{ number_format(array_sum(array_column($monthlyData,'billed')),2) }}</td>
    <td>৳{{ number_format($totalCollected,2) }}</td>
    <td>৳{{ number_format($totalExpenses,2) }}</td>
    <td>৳{{ number_format($totalCollected-$totalExpenses,2) }}</td>
  </tr>
  </tbody>
</table>
</body>
</html>
