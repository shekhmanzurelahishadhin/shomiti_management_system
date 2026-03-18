@extends('layouts.app')
@section('title','আমার বার্ষিক সারসংক্ষেপ')
@section('page-title','আমার বার্ষিক সারসংক্ষেপ')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>আমার বার্ষিক সারসংক্ষেপ</h5>
  <div class="d-flex gap-2">
    <form method="GET" class="d-flex gap-2">
      <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
        @for($y=now()->year;$y>=2020;$y--)
          <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
        @endfor
      </select>
    </form>
    <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-outline-primary">
      <i class="bi bi-person me-1"></i>প্রোফাইল
    </a>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-4">
    <div class="card text-center p-3" style="border-left:4px solid #1a3a1c">
      <div class="text-muted small">{{ $year }} সালে মোট বিল</div>
      <div class="fs-4 fw-bold text-primary">৳{{ number_format($totalBilled,0) }}</div>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="card text-center p-3" style="border-left:4px solid #27ae60">
      <div class="text-muted small">মোট পরিশোধ</div>
      <div class="fs-4 fw-bold text-success">৳{{ number_format($totalPaid,0) }}</div>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="card text-center p-3" style="border-left:4px solid {{ ($totalBilled-$totalPaid)>0?'#e74c3c':'#27ae60' }}">
      <div class="text-muted small">বকেয়া</div>
      <div class="fs-4 fw-bold {{ ($totalBilled-$totalPaid)>0?'text-danger':'text-success' }}">
        ৳{{ number_format(max(0,$totalBilled-$totalPaid),0) }}
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header py-3 fw-semibold">
    <i class="bi bi-table me-2 text-primary"></i>মাসভিত্তিক বিবরণ — {{ $year }}
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>মাস</th><th>বিলের পরিমাণ</th><th>পরিশোধ</th><th>বকেয়া</th></tr>
      </thead>
      <tbody>
      @foreach($monthlyData as $row)
        <tr class="{{ $row['balance']>0?'table-warning':'' }}">
          <td class="fw-semibold">{{ $row['month'] }}</td>
          <td>৳{{ number_format($row['billed'],2) }}</td>
          <td class="text-success">৳{{ number_format($row['paid'],2) }}</td>
          <td class="{{ $row['balance']>0?'text-danger fw-semibold':'text-muted' }}">
            ৳{{ number_format(max(0,$row['balance']),2) }}
          </td>
        </tr>
      @endforeach
      </tbody>
      <tfoot class="table-light fw-bold">
        <tr>
          <td>মোট</td>
          <td>৳{{ number_format($totalBilled,2) }}</td>
          <td class="text-success">৳{{ number_format($totalPaid,2) }}</td>
          <td class="{{ ($totalBilled-$totalPaid)>0?'text-danger':'text-success' }}">
            ৳{{ number_format(max(0,$totalBilled-$totalPaid),2) }}
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@endsection
