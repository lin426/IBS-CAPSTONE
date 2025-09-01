<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ config('app.name', 'InsightBlitz') }}</title>

  {{-- Bootstrap (same look/feel as login) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  {{-- Your existing overrides / background --}}
  <link rel="stylesheet" href="{{ asset('css.css') }}">
</head>
<body class="text-light">
  {{-- Full-height center --}}
  <div class="min-vh-100 d-flex align-items-center justify-content-center px-3">

    {{-- Glassy card wrapper (shared for all guest pages) --}}
    <div class="card shadow-lg rounded-4 border-0"
         style="max-width: 560px; width: 100%; background: rgba(0,0,0,.78); backdrop-filter: blur(6px);">
      <div class="card-body p-4 p-sm-5">
        @yield('content')
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
