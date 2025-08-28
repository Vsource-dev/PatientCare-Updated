{{-- resources/views/patients/edit.blade.php --}}
@extends('layouts.admission')

@section('content')
  <div class="page-header mb-4">
    <h1 class="h3">Edit Patient Admission #{{ $patient->patient_id }}</h1>
  </div>

  <form method="POST" action="{{ route('admission.patients.update', $patient->patient_id) }}">
    @include('patients._form')
    <div class="mt-4 text-end">
      <a href="{{ route('admission.patients.index') }}" class="btn btn-secondary">Cancel</a>
      <button class="btn btn-primary">Update</button>
    </div>
  </form>
@endsection
