<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>InsightBlitz</title>

    <!-- CSS Links -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <link href="{{ asset('css.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS for dropdowns -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Style -->
    <style>
        body {
            background: url('{{ asset('pictures/background.png') }}') no-repeat center center fixed;
            background-size: cover;
            color: #f0f0f0;
        }
        .card {
            background-color: rgba(26, 26, 26, 0.85);
            border: 1px solid #333;
            border-radius: 12px;
            padding: 20px;
            color: #ffffff;
            backdrop-filter: blur(4px);
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .table { background-color: transparent; color: rgb(244, 244, 244); }
        .table th, .table td { border-color: #444; }
        .btn { border-radius: 20px; padding: 6px 14px; }
        .btn-warning, .btn-danger, .btn-primary {
            background-color: rgb(28, 57, 100); color: white; border: none;
        }
        .form-control { background-color: #222; color: white; border: 1px solid #555; }

        /* --- dropdown stacking fix --- */
        .navbar { position: relative; z-index: 1100; overflow: visible; }
        .navbar .container { overflow: visible; }
        .dropdown, .dropdown-menu { overflow: visible; }
        .dropdown-menu { z-index: 1200; } /* above cards/backdrop */
    </style>
</head>
<body>

@auth
@php
    $user = auth()->user();
    $isAdmin = ($user->role ?? 'client') === 'admin';

    // initials for the avatar circle (e.g., "GH")
    $initials = collect(preg_split('/\s+/', trim($user->name ?? '')))
        ->filter()
        ->map(fn($p) => mb_substr($p, 0, 1))
        ->join('');

    // show a small badge with count of open client requests for admins
    $openReq = 0;
    if ($isAdmin) {
        $openReq = \App\Models\ClientRequest::where('status', 'open')->count();
    }
@endphp

<nav class="navbar navbar-expand-lg d-flex justify-content-between"
     style="background: rgba(0,0,0,0.35); backdrop-filter: blur(6px); border:1px solid #333; border-radius: 25px; margin: 15px;
            position: relative; z-index: 1100; overflow: visible;">
  <div class="container d-flex justify-content-between">

    {{-- Brand --}}
    <div class="brand-logo d-flex align-items-center">
      <img src="{{ asset('pictures/ibslogo.jpg') }}" alt="Logo" style="height: 40px; margin-right: 10px;">
      <a class="navbar-brand text-white fw-bold" href="{{ route('home') }}">InsightBlitz</a>
    </div>

    {{-- Links (role-based) --}}
    <div class="d-none d-lg-flex align-items-center gap-3">
      @if($isAdmin)
        <a class="nav-link text-white" href="{{ route('leads.index') }}">Leads</a>
        <a class="nav-link text-white" href="{{ route('dashboard') }}">Dashboard</a>
        <a class="nav-link text-white" href="{{ route('clients.index') }}">Clients</a>
        <a class="nav-link text-white" href="{{ route('tasks.index') }}">Tasks</a>
        <a class="nav-link text-white" href="{{ route('projects.index') }}">Projects</a>
        <a class="nav-link text-white" href="{{ route('staff.index') }}">Staff</a>

        {{-- NEW: Requests link with live badge for open items --}}
        <a class="nav-link text-white d-flex align-items-center" href="{{ route('admin.requests.index') }}">
          Requests
          @if($openReq > 0)
            <span class="badge bg-danger ms-2">{{ $openReq }}</span>
          @endif
        </a>
      @else
        <a class="nav-link text-white" href="{{ route('client.home') }}">Client Home</a>
      @endif
    </div>

    {{-- Avatar dropdown (Settings / Logout) --}}
    <div class="d-flex align-items-center">
      <div class="dropdown" data-bs-display="static">
        <a href="#" class="d-flex align-items-center justify-content-center rounded-circle bg-light text-dark fw-semibold"
           style="width: 36px; height: 36px; text-decoration:none;"
           data-bs-toggle="dropdown" aria-expanded="false" title="{{ $user->name }}">
          {{ $initials ?: 'U' }}
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li><a class="dropdown-item" href="{{ route('settings') }}">Settings</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="POST" action="{{ route('logout') }}" class="px-3">
              @csrf
              <button type="submit" class="btn btn-link p-0 text-danger">Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>

  </div>
</nav>
@endauth

{{-- MAIN CONTENT AREA --}}
<main class="container my-4">
  {{-- Flash messages --}}
  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if (session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  @yield('content')
</main>

</body>
</html>
