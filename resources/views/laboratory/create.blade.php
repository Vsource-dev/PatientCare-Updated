@extends('layouts.laboratory')

@section('content')
<div class="container-fluid">
  <h1 class="h4 mb-3">Add New Laboratory Charge</h1>

  {{-- SINGLE form wrapper --}}
  <form method="POST" action="{{ route('laboratory.store') }}" id="lab-form">
    @include('laboratory._form')      {{-- all fields & JS live here --}}

    {{-- Submit / cancel --}}
    <div class="text-end">
      <a href="{{ route('laboratory.dashboard') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Create Charge</button>
    </div>
  </form>
</div>
@endsection
