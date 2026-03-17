<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; color: #222; }
  .center { text-align: center; }
  .title { font-size: 18px; font-weight: bold; color: #1a5276; }
  table { width: 100%; border-collapse: collapse; margin-top: 15px; }
  td { padding: 7px 5px; }
  .label { color: #777; }
  .border-top td { border-top: 2px solid #eee; }
  .amount { font-size: 22px; font-weight: bold; color: #1e8449; text-align: right; }
  .badge { background: #eee; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
  .status-paid { background: #d1e7dd; color: #0a3622; }
  .footer { margin-top: 30px; text-align: center; color: #aaa; font-size: 11px; border-top: 1px solid #eee; padding-top: 10px; }
  .receipt-box { border: 2px dashed #ccc; padding: 20px; max-width: 500px; margin: 0 auto; }
</style>
</head>
<body>
<div class="receipt-box">
  <div class="center">
    <div class="title">নবদিগন্ত সমবায় সমিতি</div>
    <div style="color:#555">Nabadiganta Somobai Somiti</div>
    <div style="color:#888;font-size:12px">{{ \App\Models\Setting::get('somity_address') }}</div>
    <hr style="border:1px solid #eee;margin:10px 0">
    <strong style="font-size:15px">PAYMENT RECEIPT</strong><br>
    <span style="color:#999;font-size:12px">Receipt #{{ str_pad($payment->id,6,'0',STR_PAD_LEFT) }}</span>
  </div>

  <table>
    <tr><td class="label">Member Name</td><td style="text-align:right;font-weight:bold">{{ $payment->member->name }}</td></tr>
    <tr><td class="label">Member ID</td><td style="text-align:right">{{ $payment->member->member_id }}</td></tr>
    <tr><td class="label">Bill Period</td><td style="text-align:right">{{ $payment->bill->month_name }} {{ $payment->bill->bill_year }}</td></tr>
    <tr><td class="label">Payment Method</td><td style="text-align:right">{{ strtoupper($payment->payment_method) }}</td></tr>
    @if($payment->reference)
    <tr><td class="label">Reference</td><td style="text-align:right">{{ $payment->reference }}</td></tr>
    @endif
    <tr><td class="label">Date</td><td style="text-align:right">{{ $payment->payment_date->format('d M Y') }}</td></tr>
    <tr><td class="label">Collected By</td><td style="text-align:right">{{ $payment->collector->name ?? 'System' }}</td></tr>
    <tr class="border-top">
      <td style="font-weight:bold;font-size:15px;padding-top:12px">Amount Paid</td>
      <td class="amount">৳{{ number_format($payment->amount,2) }}</td>
    </tr>
    <tr>
      <td class="label">Remaining Due</td>
      <td style="text-align:right;color:{{ $payment->bill->total_due > 0 ? '#c0392b' : '#1e8449' }};font-weight:bold">
        ৳{{ number_format($payment->bill->total_due,2) }}
      </td>
    </tr>
  </table>

  <div class="center" style="margin-top:15px">
    <span class="badge {{ $payment->bill->status=='paid' ? 'status-paid' : '' }}">
      Bill: {{ ucfirst($payment->bill->status) }}
    </span>
  </div>

  <div class="footer">
    Thank you for your payment.<br>
    Printed: {{ now()->format('d M Y H:i') }}
  </div>
</div>
</body>
</html>
