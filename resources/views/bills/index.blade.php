@extends('layouts.app')
@section('title','Bills')
@section('page-title','Bill Management')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0 fw-bold"><i class="bi bi-receipt me-2 text-primary"></i>Bills</h5>
    <div class="d-flex gap-2">
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#genModal">
            <i class="bi bi-lightning me-1"></i>Generate Monthly
        </button>
        <form method="POST" action="{{ route('bills.apply-fines') }}" class="d-inline"
              onsubmit="return confirm('Apply late fines to all overdue bills?')">
            @csrf
            <button class="btn btn-danger btn-sm"><i class="bi bi-exclamation-triangle me-1"></i>Apply Fines</button>
        </form>
        <a href="{{ route('bills.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus me-1"></i>New Bill
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Member name or ID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="month" class="form-select form-select-sm">
                    <option value="">All Months</option>
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ request('month')==$m?'selected':'' }}>{{ date('F',mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <select name="year" class="form-select form-select-sm">
                    <option value="">All Years</option>
                    @for($y=now()->year;$y>=2020;$y--)
                        <option value="{{ $y }}" {{ request('year')==$y?'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    @foreach(['pending','paid','partial','overdue'] as $s)
                        <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-search"></i></button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Member</th><th>Period</th><th>Amount</th>
                    <th>Fine</th><th>Paid</th><th>Due</th><th>Due Date</th><th>Status</th><th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($bills as $bill)
                <tr>
                    <td class="text-muted small">{{ $bill->id }}</td>
                    <td>
                        <a href="{{ route('members.show', $bill->member) }}" class="text-decoration-none fw-semibold">
                            {{ $bill->member->name }}
                        </a>
                        <div class="small text-muted">{{ $bill->member->member_id }}</div>
                    </td>
                    <td>{{ $bill->month_name }} {{ $bill->bill_year }}</td>
                    <td>৳{{ number_format($bill->amount,2) }}</td>
                    <td>{{ $bill->fine > 0 ? '৳'.number_format($bill->fine,2) : '—' }}</td>
                    <td class="text-success">৳{{ number_format($bill->paid_amount,2) }}</td>
                    <td class="fw-bold {{ $bill->total_due > 0 ? 'text-danger' : 'text-muted' }}">
                        ৳{{ number_format($bill->total_due,2) }}
                    </td>
                    <td class="{{ $bill->due_date->isPast() && $bill->status !== 'paid' ? 'text-danger' : '' }}">
                        {{ $bill->due_date->format('d M Y') }}
                    </td>
                    <td><span class="badge badge-{{ $bill->status }}">{{ ucfirst($bill->status) }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('bills.show', $bill) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('bills.edit', $bill) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                        @can('collect payments')
                        @if($bill->status !== 'paid')
                        <a href="{{ route('payments.create', ['member_id'=>$bill->member_id,'bill_id'=>$bill->id]) }}"
                           class="btn btn-sm btn-outline-success" title="Collect">
                            <i class="bi bi-cash"></i>
                        </a>
                        @endif
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center text-muted py-4">No bills found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($bills->hasPages())
        <div class="card-footer">{{ $bills->links() }}</div>
    @endif
</div>

<!-- Generate Bills Modal -->
<div class="modal fade" id="genModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('bills.generate-monthly') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Generate Monthly Bills</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
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
                        @for($y=now()->year;$y>=2020;$y--)<option value="{{ $y }}">{{ $y }}</option>@endfor
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning"><i class="bi bi-lightning me-1"></i>Generate</button>
            </div>
        </form>
    </div>
</div>
@endsection
