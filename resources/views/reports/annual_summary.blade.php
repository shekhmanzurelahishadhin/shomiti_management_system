@extends('layouts.app')
@section('title','Annual Summary')
@section('page-title','Annual Financial Summary')
@section('content')
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-2">
        <label class="form-label small">Year</label>
        <select name="year" class="form-select form-select-sm">
          @for($y=now()->year;$y>=2020;$y--)
            <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
          @endfor
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>View</button>
      </div>
      <div class="col-md-2">
        <a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-danger btn-sm w-100">
          <i class="bi bi-file-pdf me-1"></i>Export PDF
        </a>
      </div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <div class="card text-center p-3" style="border-left:4px solid #27ae60">
      <div class="text-muted small">Total Collected {{ $year }}</div>
      <div class="fs-3 fw-bold text-success">৳{{ number_format($totalCollected,2) }}</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-3" style="border-left:4px solid #e74c3c">
      <div class="text-muted small">Total Expenses {{ $year }}</div>
      <div class="fs-3 fw-bold text-danger">৳{{ number_format($totalExpenses,2) }}</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-3" style="border-left:4px solid #2e86c1">
      <div class="text-muted small">Net Balance {{ $year }}</div>
      <div class="fs-3 fw-bold text-primary">৳{{ number_format($totalCollected-$totalExpenses,2) }}</div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header py-3"><i class="bi bi-bar-chart me-2 text-primary"></i>Monthly Trend</div>
  <div class="card-body"><canvas id="annualChart" height="80"></canvas></div>
</div>

<div class="card">
  <div class="card-header py-3"><i class="bi bi-table me-2 text-success"></i>Month-wise Summary — {{ $year }}</div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Month</th><th>Billed</th><th>Collected</th><th>Expenses</th><th>Balance</th></tr>
      </thead>
      <tbody>
      @foreach($monthlyData as $row)
        <tr>
          <td class="fw-semibold">{{ $row['month'] }}</td>
          <td>৳{{ number_format($row['billed'],2) }}</td>
          <td class="text-success">৳{{ number_format($row['collected'],2) }}</td>
          <td class="text-danger">৳{{ number_format($row['expenses'],2) }}</td>
          <td class="{{ $row['balance']>=0?'text-success':'text-danger' }} fw-semibold">
            ৳{{ number_format($row['balance'],2) }}
          </td>
        </tr>
      @endforeach
      </tbody>
      <tfoot class="table-light fw-bold">
        <tr>
          <td>Total</td>
          <td>৳{{ number_format(array_sum(array_column($monthlyData,'billed')),2) }}</td>
          <td class="text-success">৳{{ number_format($totalCollected,2) }}</td>
          <td class="text-danger">৳{{ number_format($totalExpenses,2) }}</td>
          <td class="text-primary">৳{{ number_format($totalCollected-$totalExpenses,2) }}</td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@endsection
@push('scripts')
<script>
new Chart(document.getElementById('annualChart'), {
  type: 'bar',
  data: {
    labels: {!! json_encode(array_column($monthlyData,'month')) !!},
    datasets: [
      { label: 'Collected', data: {!! json_encode(array_column($monthlyData,'collected')) !!}, backgroundColor: 'rgba(39,174,96,.7)', borderRadius: 4 },
      { label: 'Expenses',  data: {!! json_encode(array_column($monthlyData,'expenses'))  !!}, backgroundColor: 'rgba(231,76,60,.7)',  borderRadius: 4 },
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'top' } },
    scales: { y: { beginAtZero: true, ticks: { callback: v=>'৳'+v.toLocaleString() } } }
  }
});
</script>
@endpush
