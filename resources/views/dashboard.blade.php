@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#1a5276,#2e86c1)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="small opacity-75 mb-1">Total Members</div>
                    <div class="fs-2 fw-bold">{{ $totalMembers }}</div>
                </div>
                <i class="bi bi-people fs-2 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#1e8449,#27ae60)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="small opacity-75 mb-1">This Month</div>
                    <div class="fs-2 fw-bold">৳{{ number_format($totalCollection,0) }}</div>
                </div>
                <i class="bi bi-cash-coin fs-2 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#7d6608,#d4ac0d)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="small opacity-75 mb-1">Pending Dues</div>
                    <div class="fs-2 fw-bold">৳{{ number_format($pendingDues,0) }}</div>
                </div>
                <i class="bi bi-exclamation-circle fs-2 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#922b21,#e74c3c)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="small opacity-75 mb-1">Overdue Bills</div>
                    <div class="fs-2 fw-bold">{{ $overdueCount }}</div>
                </div>
                <i class="bi bi-calendar-x fs-2 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card p-3">
            <div class="text-muted small">Yearly Collection</div>
            <div class="fs-5 fw-bold text-success">৳{{ number_format($yearlyCollection,0) }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3">
            <div class="text-muted small">Monthly Expenses</div>
            <div class="fs-5 fw-bold text-danger">৳{{ number_format($monthlyExpenses,0) }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3">
            <div class="text-muted small">Net Balance (Month)</div>
            <div class="fs-5 fw-bold text-primary">৳{{ number_format($totalCollection - $monthlyExpenses,0) }}</div>
        </div>
    </div>
    @if($disconnectedMembers > 0)
    <div class="col-6 col-lg-3">
        <div class="card p-3" style="border-left:3px solid #6c757d">
            <div class="text-muted small">সংযোগ বিচ্ছিন্ন</div>
            <div class="fs-5 fw-bold text-secondary">{{ $disconnectedMembers }} জন</div>
        </div>
    </div>
    @endif

{{-- Investment summary row --}}
@can('view investments')
<div class="row g-3 mb-3">
  <div class="col-12">
    <div class="card">
      <div class="card-header py-2 fw-semibold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-graph-up-arrow me-2 text-success"></i>বিনিয়োগ সারসংক্ষেপ</span>
        <a href="{{ route('investments.index') }}" class="btn btn-sm btn-outline-success">সব দেখুন</a>
      </div>
      <div class="card-body py-2">
        <div class="row text-center g-2">
          <div class="col-6 col-md-3">
            <div class="text-muted small">বিবেচনাধীন আবেদন</div>
            <div class="fs-5 fw-bold text-warning">{{ $pendingInvestments }}</div>
          </div>
          <div class="col-6 col-md-3">
            <div class="text-muted small">সক্রিয় বিনিয়োগ</div>
            <div class="fs-5 fw-bold text-success">{{ $activeInvestments }}</div>
          </div>
          <div class="col-6 col-md-3">
            <div class="text-muted small">মোট বিনিয়োগকৃত</div>
            <div class="fs-5 fw-bold text-primary">৳{{ number_format($activeInvestAmount,0) }}</div>
          </div>
          <div class="col-6 col-md-3">
            <div class="text-muted small">মেয়াদ শেষ (নিষ্পত্তি বাকি)</div>
            <div class="fs-5 fw-bold {{ $maturedInvestments>0?'text-danger':'text-muted' }}">{{ $maturedInvestments }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@if($maturedInvestments > 0)
<div class="alert alert-warning py-2 d-flex justify-content-between align-items-center mb-3">
  <span><i class="bi bi-alarm me-2"></i><strong>{{ $maturedInvestments }}টি</strong> বিনিয়োগের মেয়াদ শেষ — নিষ্পত্তির অপেক্ষায়</span>
  <a href="{{ route('investments.index', ['status'=>'matured']) }}" class="btn btn-warning btn-sm">নিষ্পত্তি করুন</a>
</div>
@endif
@if($pendingInvestments > 0)
@can('manage investment agenda')
<div class="alert alert-info py-2 d-flex justify-content-between align-items-center mb-3">
  <span><i class="bi bi-file-earmark-text me-2"></i><strong>{{ $pendingInvestments }}টি</strong> বিনিয়োগ আবেদন সভার জন্য অপেক্ষায়</span>
  <a href="{{ route('investments.meeting.list') }}" class="btn btn-info btn-sm text-white">এজেন্ডায় যোগ করুন</a>
</div>
@endcan
@endif
@endcan


    <div class="col-6 col-lg-3">
        <div class="card p-3">
            <div class="text-muted small">Today</div>
            <div class="fs-5 fw-bold">{{ now()->format('d M Y') }}</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between">
                <span><i class="bi bi-bar-chart me-2 text-primary"></i>Monthly Collection (Last 6 Months)</span>
            </div>
            <div class="card-body">
                <canvas id="collectionChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-3">
                <i class="bi bi-lightning me-2 text-warning"></i>Quick Actions
            </div>
            <div class="card-body d-grid gap-2">
                @can('manage members')
                <a href="{{ route('members.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-person-plus me-2"></i>Add Member
                </a>
                @endcan
                @can('collect payments')
                <a href="{{ route('payments.create') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-cash me-2"></i>Collect Payment
                </a>
                @endcan
                @can('generate bills')
                <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#genBillModal">
                    <i class="bi bi-receipt me-2"></i>Generate Bills
                </button>
                @endcan
                @can('view reports')
                <a href="{{ route('reports.defaulters') }}" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-exclamation-triangle me-2"></i>View Defaulters
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2 text-success"></i>Recent Payments</span>
                @can('collect payments')
                <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Member</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($recentPayments as $p)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $p->member->name ?? '—' }}</div>
                                <small class="text-muted">{{ $p->member->member_id ?? '' }}</small>
                            </td>
                            <td class="fw-bold text-success">৳{{ number_format($p->amount,2) }}</td>
                            <td><span class="badge bg-secondary">{{ strtoupper($p->payment_method) }}</span></td>
                            <td>{{ $p->payment_date->format('d M Y') }}</td>
                            <td><span class="badge badge-paid">Paid</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No recent payments.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Generate Bills Modal -->
@can('generate bills')
<div class="modal fade" id="genBillModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('bills.generate-monthly') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Generate Monthly Bills</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-select" required>
                            @for($m=1;$m<=12;$m++)
                                <option value="{{ $m }}" {{ $m==now()->month?'selected':'' }}>{{ date('F',mktime(0,0,0,$m,1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select" required>
                            @for($y=now()->year;$y>=2020;$y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning"><i class="bi bi-lightning me-1"></i>Generate</button>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('collectionChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($chartData, 'label')) !!},
        datasets: [{
            label: 'Collection (৳)',
            data: {!! json_encode(array_column($chartData, 'amount')) !!},
            backgroundColor: 'rgba(46,134,193,0.7)',
            borderColor: '#1a5276',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => '৳'+v.toLocaleString() } } }
    }
});
</script>
@endpush
