@extends('layouts.app')
@section('title','Committees')
@section('page-title','Committee Management')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-diagram-3 me-2 text-primary"></i>Committees</h5>
    <a href="{{ route('committees.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus me-1"></i>New Committee
    </a>
</div>
<div class="row g-3">
@forelse($committees as $c)
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <h6 class="fw-bold mb-0">{{ $c->name }}</h6>
          <span class="badge badge-{{ $c->status }}">{{ ucfirst($c->status) }}</span>
        </div>
        @if($c->description)
          <div class="text-muted small mb-2">{{ Str::limit($c->description,80) }}</div>
        @endif
        <div class="d-flex gap-3 mt-3">
          <div class="text-center">
            <div class="fw-bold text-primary">{{ $c->committee_members_count }}</div>
            <div class="text-muted" style="font-size:.75rem">Members</div>
          </div>
          <div class="text-center">
            <div class="fw-bold text-success">৳{{ number_format($c->total_fund,0) }}</div>
            <div class="text-muted" style="font-size:.75rem">Fund</div>
          </div>
        </div>
      </div>
      <div class="card-footer d-flex gap-2">
        <a href="{{ route('committees.show',$c) }}" class="btn btn-sm btn-outline-primary flex-fill">
          <i class="bi bi-eye me-1"></i>View
        </a>
        <a href="{{ route('committees.edit',$c) }}" class="btn btn-sm btn-outline-warning">
          <i class="bi bi-pencil"></i>
        </a>
        <form method="POST" action="{{ route('committees.destroy',$c) }}" class="d-inline"
              onsubmit="return confirm('Delete committee?')">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form>
      </div>
    </div>
  </div>
@empty
  <div class="col-12"><div class="alert alert-info">No committees yet. <a href="{{ route('committees.create') }}">Create one</a>.</div></div>
@endforelse
</div>
@if($committees->hasPages())
  <div class="mt-3">{{ $committees->links() }}</div>
@endif
@endsection
