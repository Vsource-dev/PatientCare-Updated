{{-- resources/views/supplies/show.blade.php --}}
@extends('layouts.supplies')

@section('content')
<div class="container-fluid py-4">
  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm mb-4">
      <li class="breadcrumb-item"><a href="{{ route('supplies.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('supplies.queue') }}">Queue</a></li>
      <li class="breadcrumb-item active" aria-current="page">
        Charge #{{ $charge->id }}
      </li>
    </ol>
  </nav>

  {{-- Details Card --}}
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between">
      <h5 class="mb-0"><i class="fa-solid fa-box-open me-2"></i>Supply Charge Details</h5>
      <a href="{{ route('supplies.queue') }}" class="btn btn-light btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i>Back to Queue
      </a>
    </div>

    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Charge ID</dt>
        <dd class="col-sm-9">#{{ $charge->id }}</dd>

        <dt class="col-sm-3">Patient</dt>
        <dd class="col-sm-9">
          {{ optional($charge->patient)->patient_first_name }} {{ optional($charge->patient)->patient_last_name }}
          <br><small class="text-muted">MRN: {{ optional($charge->patient)->patient_id }}</small>
        </dd>

        <dt class="col-sm-3">Item</dt>
        <dd class="col-sm-9">{{ optional($charge->service)->service_name }}</dd>

        <dt class="col-sm-3">Quantity</dt>
        <dd class="col-sm-9">{{ $charge->quantity }}</dd>

        <dt class="col-sm-3">Date Added</dt>
        <dd class="col-sm-9">{{ $charge->created_at->format('M j, Y g:i A') }}</dd>

        <dt class="col-sm-3">Amount</dt>
        <dd class="col-sm-9">₱{{ number_format($charge->total, 2) }}</dd>

        <dt class="col-sm-3">Notes</dt>
        <dd class="col-sm-9">{{ $charge->notes ?? '—' }}</dd>
      </dl>
    </div>

    <div class="card-footer bg-white text-end">
      @if($charge->status === 'pending')
        <form action="{{ route('supplies.complete', $charge) }}"
              method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-check me-1"></i>Mark as Completed
          </button>
        </form>
      @else
        <span class="badge bg-success text-white">Completed</span>
      @endif
    </div>
  </div>
</div>
@endsection
