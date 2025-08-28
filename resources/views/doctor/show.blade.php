@extends('layouts.doctor')

@section('content')
  <h2 class="fw-bold mb-3">
    Patient: {{ $patient->patient_first_name }} {{ $patient->patient_last_name }}
  </h2>

  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">Demographics</h5>
      <p><strong>Age:</strong> {{ $patient->patient_birthday?->age }}</p>
      <p><strong>Sex / Civil Status:</strong> {{ $patient->civil_status }}</p>
      <p>
        <strong>Room:</strong>
        {{ $patient->admissionDetail->room->room_number ?? '–' }}
      </p>
      {{-- …other fields… --}}
    </div>
  </div>

  {{-- Order Entry Button --}}
  <div class="mb-5 text-end">
    <a
      href="{{ route('doctor.order', ['patient' => $patient->patient_id]) }}"
      class="btn btn-primary"
    >
      <i class="fas fa-prescription fa-fw me-1"></i>
      Create Order
    </a>
  </div>

  {{-- You can drop in your order‐entry tabs/form here, or just link off to the full order‐entry page --}}
@endsection
