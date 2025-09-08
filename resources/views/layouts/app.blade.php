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

        /* bell icon badge */
        .nav-bell { position: relative; display: inline-flex; align-items: center; }
        .nav-bell .badge {
            position: absolute;
            top: -4px;
            right: -8px;
            font-size: .65rem;
            line-height: 1;
            padding: .25rem .4rem;
        }
        .nav-bell svg { display: block; }
    </style>
</head>
<body>

@auth
@php
    $user = auth()->user();
    $role = strtolower(trim($user->role ?? 'client'));
    $isAdmin = $role === 'admin';
    $isStaff = $role === 'staff';

    // initials for the avatar circle (e.g., "GH")
    $initials = collect(preg_split('/\s+/', trim($user->name ?? '')))
        ->filter()
        ->map(fn($p) => mb_substr($p, 0, 1))
        ->join('');

    // Admin badges
    $openReq = 0;
    $notifUnread = 0;
    if ($isAdmin) {
        $openReq = \App\Models\ClientRequest::where('status', 'open')->count();
        $notifUnread = $user->unreadNotifications()->count(); // 🔔 unread notifications
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

        {{-- Requests with badge --}}
        <a class="nav-link text-white d-flex align-items-center" href="{{ route('admin.requests.index') }}">
          Requests
          @if($openReq > 0)
            <span class="badge bg-danger ms-2">{{ $openReq }}</span>
          @endif
        </a>

        {{-- 🔔 Notifications bell (icon + unread badge) --}}
        <a class="nav-link text-white nav-bell"
           href="{{ \Illuminate\Support\Facades\Route::has('admin.notifications') ? route('admin.notifications') : url('/notifications') }}"
           title="Notifications">
          {{-- Bootstrap Icons bell (inline SVG, no CDN needed) --}}
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor"
               class="bi bi-bell" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M8 16a2 2 0 0 0 1.985-1.75H6.015A2 2 0 0 0 8 16M8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92z"/>
          </svg>
          @if($notifUnread > 0)
            <span class="badge bg-danger">{{ $notifUnread }}</span>
          @endif
          <span class="visually-hidden">Notifications</span>
        </a>

      @elseif($isStaff)
        {{-- Staff --}}
        <a class="nav-link text-white" href="{{ route('staff.portal.dashboard') }}">Staff Home</a>

      @else
        {{-- Client --}}
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
