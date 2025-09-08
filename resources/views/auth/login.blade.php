@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 420px;">
  <h3 class="mb-3">
    {{ (($context ?? 'client') === 'admin') ? 'Admin Sign in' : 'Sign in' }}
  </h3>

  {{-- flashes --}}
  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('login.attempt') }}">
    @csrf
    {{-- preserve which login page we’re on --}}
    <input type="hidden" name="context" value="{{ $context ?? 'client' }}"/>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input name="email" type="email" class="form-control" value="{{ old('email') }}" required autofocus>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input name="password" type="password" class="form-control" required>
    </div>
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="remember" id="remember">
      <label class="form-check-label" for="remember">Remember me</label>
    </div>
    <button class="btn btn-primary w-100" type="submit">Login</button>
  </form>

  <div class="mt-3">
    @if(($context ?? 'client') === 'admin')
      {{-- Admin page: show a link back to client login --}}
      <a class="link-light" href="{{ route('login') }}">Back to client login</a>
    @else
      {{-- Client page: show Register + Admin links --}}
      <div><a class="link-light" href="{{ route('register.show') }}">Don’t have an account? Create one</a></div>
      <div class="mt-1"><a class="link-light" href="{{ route('admin.login') }}">Admin? Sign in here</a></div>
    @endif
  </div>
</div>
@endsection
