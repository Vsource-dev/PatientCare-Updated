{{-- resources/views/layouts/admission.blade.php --}}
<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>Admission Panel • PatientCare</title>

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
          top: 0;
          bottom: 0;
          left: 0;
          width: 240px;
          display: flex;
          flex-direction: column;
          background-color: #00529A;
          overflow-y: auto;
          padding: 1rem;
        }
        main {
          margin-left: 240px;
          height: 100vh;
          overflow-y: auto;
          padding: 1.5rem;
        }
        .logo { width: 80px; }
        .avatar {
          width: 90px;
          height: 90px;
          background-color: aliceblue;
          border-radius: 50%;
          margin: 0 auto 1rem;
        }
        .nav-link {
          transition: background-color 0.2s ease;
          border-radius: 0.375rem;
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
        .footer {
          margin-top: auto;
        }
      </style>
  </head>
  <body>

    <div class="container-fluid p-2">
      {{-- Sidebar --}}
      <aside class="sidebar text-white">
        {{-- Top Section --}}
        <div class="text-center mb-4">
          <img src="{{ asset('images/patientcare-logo-white.png') }}"
               class="logo img-fluid mb-3"
               alt="PatientCare Logo">
          <div class="avatar"></div>
          <strong>{{ Auth::user()->username ?? 'Admission User' }}</strong><br>
          <small>Admission ID: {{ Auth::id() }}</small>
        </div>

        {{-- Navigation --}}
        <nav class="nav flex-column mb-4">
          <a href="{{ route('admission.dashboard') }}"
             class="nav-link d-flex align-items-center mb-2 {{ request()->routeIs('admission.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home fa-lg me-2"></i>
            <span>Home</span>
          </a>

          <a href="{{ route('admission.patients.index') }}"
             class="nav-link d-flex align-items-center mb-2
             {{ request()->routeIs('admission.patients.index') || request()->routeIs('admission.patients.show') ? 'active' : '' }}">
            <i class="fas fa-users fa-lg me-2"></i>
            <span>Patients</span>
          </a>

          <a href="{{ route('admission.patients.create') }}"
             class="nav-link d-flex align-items-center mb-2 {{ request()->routeIs('admission.patients.create') ? 'active' : '' }}">
            <i class="fas fa-user-plus fa-lg me-2"></i>
            <span>Add Patient</span>
          </a>
        </nav>

        {{-- Footer --}}
        <div class="footer text-center">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="btn btn-sm btn-outline-light w-100 text-start mb-3">
              <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
          </form>
          <small>PatientCare © {{ date('Y') }}</small>
        </div>
      </aside>

      {{-- Main Content --}}
      <main>
        @yield('content')
      </main>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
  </body>
</html>
