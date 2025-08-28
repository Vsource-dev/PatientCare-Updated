<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>Operating-Room Panel • PatientCare</title>

      {{-- Bootstrap 5 --}}
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      {{-- Font-Awesome --}}
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

      <style>
          body           { overflow-x: hidden; }
          .sidebar{
              width:240px;
              position:fixed; top:0; bottom:0; left:0;
              z-index:1;
              display:flex; flex-direction:column;
              background:#00529A;
              overflow-y:auto;      /* scroll inside if needed */
          }
          .logo   { width:80px; }
          .avatar { width:90px;height:90px;background:aliceblue; }

          .nav-link{transition:background-color .2s;border-radius:.375rem;}
          .nav-link:hover{background:rgba(255,255,255,.2);color:#fff!important;}
          .nav-link.active{background:rgba(255,255,255,.3)!important;color:#fff!important;}

          .icon{width:30px;}
          .hdng{font-size:1.5em;color:#00529A;font-weight:bold;}
      </style>
  </head>
  <body>

    {{-- Sidebar --}}
    <aside class="sidebar text-white p-3">

      {{-- Top --}}
      <div class="text-center mb-4">
          <img src="{{ asset('images/patientcare-logo-white.png') }}" class="logo img-fluid mt-2 mb-4" alt="Logo">
          <div class="avatar rounded-circle mx-auto mb-2"></div>
          <strong>{{ Auth::user()->username ?? 'OR User' }}</strong><br>
          <small>OR ID: {{ Auth::id() }}</small>
      </div>

      {{-- Navigation --}}
      <nav class="mb-auto ms-2 mt-3">

          <a href="{{ route('operating.dashboard') }}"
             class="nav-link d-flex text-white px-2 py-2 gap-2 {{ request()->routeIs('operating.dashboard') ? 'active' : '' }}">
              <span class="icon d-flex justify-content-center align-items-center">
                  <i class="fas fa-home fa-xl"></i>
              </span>
              <span class="ms-2">Home</span>
          </a>

          <a href="{{ route('operating.queue') }}"
             class="nav-link d-flex text-white px-2 py-2 gap-2 {{ request()->routeIs('operating.queue') ? 'active' : '' }}">
              <span class="icon d-flex justify-content-center align-items-center">
                  <i class="fa-solid fa-users fa-xl"></i>
              </span>
              <span class="ms-2">Patient Management</span>
          </a>

          <a href="{{ route('operating.create') }}"
             class="nav-link d-flex text-white px-2 py-2 gap-2 {{ request()->routeIs('operating.create') ? 'active' : '' }}">
              <span class="icon d-flex justify-content-center align-items-center">
                  <i class="fa-solid fa-file-medical fa-xl"></i>
              </span>
              <span class="ms-2">Add Charge</span>
          </a>
      </nav>

      {{-- Footer --}}
      <div class="footer text-center mt-auto pt-4">
          <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-light w-100 text-start p-2">
                  <i class="fas fa-sign-out-alt fa-xl me-2"></i> Logout
              </button>
          </form>
          <small class="d-block mt-3">PatientCare © {{ date('Y') }}</small>
          <sup>V1.0.0</sup>
      </div>

    </aside>

    {{-- Main content --}}
    <main class="p-4" style="margin-left: 240px;">
        @yield('content')
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
  </body>
</html>
