@extends('layouts.app')
@section('title','মেয়াদ রিপোর্ট')
@section('page-title','বিনিয়োগ মেয়াদ রিপোর্ট')
@section('content')
<div class="d-flex justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-alarm me-2 text-warning"></i>মেয়াদ রিপোর্ট</h5>
  <a href="{{ request()->fullUrlWithQuery(['export'=>'pdf']) }}" class="btn btn-danger btn-sm">
    <i class="bi bi-file-pdf me-1"></i>PDF
  </a>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>সদস্য</th><th>প্রকল্প</th><th>পরিমাণ</th><th>শুরু</th><th>মেয়াদ শেষ</th><th>বাকি দিন</th><th>অবস্থা</th><th></th></tr>
      </thead>
      <tbody>
      @forelse($investments as $i => $inv)
        <tr class="{{ $inv->is_matured ? 'table-warning' : '' }}">
          <td>{{ $i+1 }}</td>
          <td>
            <div class="fw-semibold">{{ $inv->member->name }}</div>
            <small class="text-muted">{{ $inv->member->member_id }}</small>
          </td>
          <td>{{  \Illuminate\Support\Str::limit($inv->project_name, 30) }}</td>
          <td class="fw-bold">৳{{ number_format($inv->approved_amount, 2) }}</td>
          <td>{{ $inv->approved_start_date?->format('d M Y') ?? '—' }}</td>
          <td class="{{ $inv->is_matured ? 'text-danger fw-bold' : '' }}">
            {{ $inv->approved_return_date?->format('d M Y') ?? '—' }}
          </td>
          <td class="{{ ($inv->days_remaining ?? 99) <= 7 ? 'text-danger fw-semibold' : '' }}">
            {{ $inv->days_remaining ?? '—' }} দিন
          </td>
          <td><span class="badge bg-{{ $inv->status_color }}">{{ $inv->status_label }}</span></td>
          <td>
            <a href="{{ route('investments.show', $inv) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
            @if(in_array($inv->status, ['active','matured']) && !$inv->settlement)
            @can('settle investments')
            <a href="{{ route('investments.settlement', $inv) }}" class="btn btn-sm btn-outline-dark" title="নিষ্পত্তি">
              <i class="bi bi-check2-circle"></i>
            </a>
            @endcan
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="text-center text-muted py-4">কোনো তথ্য নেই।</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
