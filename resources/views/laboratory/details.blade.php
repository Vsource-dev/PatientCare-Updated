{{-- resources/views/laboratory/details.blade.php --}}
@extends('layouts.laboratory')

@section('content')
<div class="container-fluid py-4">

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm mb-4">
      <li class="breadcrumb-item">
        <a href="{{ route('laboratory.dashboard') }}">Dashboard</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{ route('laboratory.queue') }}">Queue</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        Request #{{ $assignment->assignment_id }}
      </li>
    </ol>
  </nav>

  {{-- Details Card --}}
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between">
      <h5 class="mb-0">
        <i class="fa-solid fa-vial me-2"></i>
        Lab Request Details
      </h5>
      <a href="{{ route('laboratory.queue') }}" class="btn btn-light btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Queue
      </a>
    </div>

    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Assignment ID</dt>
        <dd class="col-sm-9">#{{ $assignment->assignment_id }}</dd>

        <dt class="col-sm-3">Patient</dt>
        <dd class="col-sm-9">
          {{ optional($assignment->patient)->patient_first_name ?? '—' }}
          {{ optional($assignment->patient)->patient_last_name  ?? '' }}
          <br>
          <small class="text-muted">
            ID: {{ optional($assignment->patient)->patient_id ?? '—' }}
          </small>
        </dd>

        <dt class="col-sm-3">Doctor</dt>
        <dd class="col-sm-9">
          {{ optional($assignment->doctor)->doctor_name ?? '—' }}
        </dd>

        <dt class="col-sm-3">Service</dt>
        <dd class="col-sm-9">
          {{ optional($assignment->service)->service_name ?? '—' }}
          <small class="text-muted">
            ({{ optional($assignment->service->department)->department_name ?? '—' }})
          </small>
        </dd>

        <dt class="col-sm-3">Description</dt>
        <dd class="col-sm-9">
          {{ optional($assignment->service)->description ?? '—' }}
        </dd>

        <dt class="col-sm-3">Date Assigned</dt>
        <dd class="col-sm-9">
          {{-- use created_at, since you don’t have a datetime column --}}
         {{ optional($assignment->created_at)->format('M j, Y g:i A') ?? '—' }}

        </dd>

        <dt class="col-sm-3">Amount</dt>
        <dd class="col-sm-9">
          ₱{{ number_format($assignment->amount, 2) }}
        </dd>
      </dl>
    </div>

    <div class="card-footer bg-white text-end">
      @if($assignment->service_status === 'pending')
        <form action="{{ route('laboratory.details.complete', $assignment) }}"
              method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-check me-1"></i>
            Mark as Completed
          </button>
        </form>
      @else
        <span class="badge bg-success text-white">Completed</span>
      @endif
    </div>
  </div>
</div>
@endsection
