{{-- resources/views/layouts/patients.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Patient Panel • PatientCare</title>

  {{-- Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  {{-- Font Awesome --}}
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    html, body {
      height: 100%;
      margin: 0;
    }
    .sidebar {
      position: fixed;
      top: 0; bottom: 0; left: 0;
      width: 240px;
      display: flex;
      flex-direction: column;
      background-color: #00529A;
      padding: 1rem;
      overflow-y: auto;
    }
    main {
      margin-left: 240px;
      height: 100vh;
      overflow-y: auto;
      padding: 1.5rem;
    }
    .logo { width: 80px; }
    .avatar {
      width: 90px; height: 90px;
      border-radius: 50%;
      object-fit: cover;
      background-color: aliceblue;
      border: 3px solid #fff;
      margin: 0 auto 1rem;
    }
    .nav-link {
      transition: background-color .2s;
      border-radius: .375rem;
      color: #fff;
    }
    .nav-link:hover {
      background-color: rgba(255,255,255,0.2);
      color: #fff !important;
    }
    .nav-link.active {
      background-color: #fff;
      color: #00529A !important;
    }
    .icon { width: 30px; text-align: center; }
    .footer {
      margin-top: auto;
    }
    .section-label {
      text-transform: uppercase;
      font-size: 0.75rem;
      color: rgba(255,255,255,0.5);
      margin: 1rem 0 0.5rem;
    }
  </style>
</head>
<body>
@php
  $user    = Auth::user();
  $patient = $user->patient;
  $unread  = $user->unreadNotifications->count();
@endphp

{{-- Sidebar --}}
<aside class="sidebar text-white">
  {{-- Logo --}}
  <div class="text-center mb-4">
    <img src="{{ asset('images/patientcare-logo-white.png') }}"
         alt="PatientCare Logo"
         class="logo img-fluid">
  </div>

  {{-- User Info --}}
  <div class="text-center mb-4">
    @if($patient && $patient->profile_photo)
      <img src="{{ asset('storage/patient/images/'.$patient->profile_photo) }}"
           class="avatar d-block mx-auto" alt="Avatar">
    @else
      <div class="avatar"></div>
    @endif
    <strong>{{ $user->username }}</strong><br>
    @if($patient)
      <small>{{ $patient->patient_first_name }} {{ $patient->patient_last_name }}</small><br>
      <small>ID: {{ str_pad($patient->patient_id,8,'0',STR_PAD_LEFT) }}</small>
    @endif
  </div>

  {{-- Navigation --}}
  <nav class="nav flex-column mb-4">
    <a href="{{ route('patient.dashboard') }}"
       class="nav-link d-flex align-items-center mb-2 {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">
      <i class="fas fa-home fa-lg icon"></i>
      <span class="ms-2">Dashboard</span>
    </a>

    <div class="section-label">My Profile</div>

    <a href="{{ route('patient.account') }}"
       class="nav-link d-flex align-items-center mb-2 {{ request()->routeIs('patient.account') ? 'active' : '' }}">
      <i class="fas fa-user-circle fa-lg icon"></i>
      <span class="ms-2">My Account</span>
    </a>

    <div class="section-label">My Records</div>

    <a href="{{ route('patient.billing') }}"
       class="nav-link d-flex align-items-center mb-2 {{ request()->routeIs('patient.billing*') ? 'active' : '' }}">
      <i class="fa-solid fa-file-invoice-dollar fa-lg icon"></i>
      <span class="ms-2">Billing</span>
    </a>

    <a href="{{ route('patient.notification') }}"
       class="nav-link d-flex align-items-center position-relative mb-2 {{ request()->routeIs('patient.notification') ? 'active' : '' }}">
      <i class="fa-solid fa-bell fa-lg icon"></i>
      <span class="ms-2">Notifications</span>
      @if($unread)
        <span class="badge bg-danger position-absolute top-0 end-0 translate-middle">{{ $unread }}</span>
      @endif
    </a>
  </nav>

  {{-- Footer --}}
  <div class="footer text-center">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-sm btn-outline-light w-100 text-start mb-3">
        <i class="fas fa-sign-out-alt me-2"></i> Logout
      </button>
    </form>
    <small class="d-block text-white-50">PatientCare © {{ date('Y') }}</small>
    <sup class="text-white-50">V1.0.0</sup>
  </div>
</aside>

{{-- Main Content --}}
<main>
  @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
