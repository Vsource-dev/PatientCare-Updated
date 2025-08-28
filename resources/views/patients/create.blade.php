{{-- resources/views/patients/create.blade.php --}}
@extends('layouts.admission')

@section('content')

  <div>
      <div class="col">
        <h4 class="fw-bold">🆕 New Patient Admission</h4>
        <p class="text-muted">Always double check the information you will put it to avoid problems later.</p>
      </div>

      <div class="">
        <form method="POST" action="{{ route('admission.patients.store') }}" novalidate>
          {{-- resources/views/patients/_form.blade.php --}}
          @include('patients._form')
          <div class="mt-4 text-end">
            <a href="{{ route('admission.patients.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-success">Save</button>
          </div>
        </form>
      </div>
  </div>

@endsection
