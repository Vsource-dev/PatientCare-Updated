{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Admin • PatientCare</title>

      {{-- Bootstrap 5 --}}
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      {{-- Font Awesome --}}
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
      {{-- SweetAlert2 --}}
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        .section-label {
          text-transform: uppercase;
          font-size: 0.75rem;
          color: rgba(255,255,255,0.5);
          margin: 1rem 0 0.5rem;
        }
      </style>
  </head>
  <body>

    <div class="container-fluid">
      {{-- Sidebar --}}
      <aside class="sidebar text-white">
        {{-- Top --}}
        <div class="text-center mb-4">
          <img src="{{ asset('images/patientcare-logo-white.png') }}"
               class="logo img-fluid mb-3"
               alt="logo">
          <div class="avatar"></div>
          <strong>{{ Auth::user()->username ?? 'Admin User' }}</strong><br>
          <small>Admin ID: {{ Auth::id() }}</small>
        </div>

        {{-- Navigation --}}
        <nav class="nav flex-column mb-4">
          <a href="{{ route('admin.dashboard') }}"
             class="nav-link d-flex align-items-center mb-2 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge fa-lg me-2"></i>
            <span>Dashboard</span>
          </a>

          <a href="{{ route('admin.users.index') }}"
             class="nav-link d-flex align-items-center mb-2 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fas fa-users fa-lg me-2"></i>
            <span>Manage Users</span>
          </a>

          <div class="section-label">Manage Resources</div>

          <a href="{{ route('admin.resources.index') }}"
             class="nav-link d-flex align-items-center mb-2 {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}">
            <i class="fas fa-layer-group fa-lg me-2"></i>
            <span>Rooms &amp; Beds</span>
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
          <small>PatientCare © {{ date('Y') }}</small><br>
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
