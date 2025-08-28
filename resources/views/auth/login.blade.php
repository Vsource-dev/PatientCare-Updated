{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('content')

  <div class="container-fluid bg-secondary px-20" style="margin-top:80px; padding: 30px 45px;">
    <h1 class="fs-1">Billing</h1>
    <h3 class="fs-2">Module</h3>
    <p>Access your Billing Information using the billing portal</p>
  </div>

<div class="container mt-5">

  <div class="row justify-content-center sm:m-5">
    
    {{-- Login Panel --}}
    <div class="col-lg-4 col-md-6 col-sm-6 border rounded p-4">

      <h2 class="mb-4 text-center">Login to PatientCare</h2>

      {{-- Validation errors --}}
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      
      <form method="POST" action="{{ route('login.attempt') }}">
        @csrf

        <div class="mb-3">
          <label>Email address</label>
          <input
            type="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email') }}"
            required
            autofocus
          >
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label>Password</label>
          <input
            type="password"
            name="password"
            class="form-control @error('password') is-invalid @enderror"
            required
          >
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3 form-check">
          <input
            type="checkbox"
            name="remember"
            class="form-check-input"
            id="remember"
          >
          <label class="form-check-label" for="remember">Remember Me</label>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
    </div>

    {{-- Instructions Panel --}}
    <div class="col-lg-4 col-md-6 col-sm-6 bg-white border px-2 py-5">
      <h3 class="text-center">Instructions for Using PatientCare Portal</h3>
      <ol class="list-group list-group-numbered mt-3">
        <li class="list-group-item">Go to admission department and get admitted.</li>
        <li class="list-group-item">Register email at admission.</li>
        <li class="list-group-item">Check your email.</li>
        <li class="list-group-item">Login to the portal.</li>
        <li class="list-group-item">View your bill.</li>
        <li class="list-group-item">Stay updated.</li>
        <li class="list-group-item">Bootstrap is killing me.</li>
      </ol>
    </div>

  </div>
</div>
@endsection
