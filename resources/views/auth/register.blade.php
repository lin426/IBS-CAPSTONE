@extends('layouts.guest')

@section('content')
  <h3 class="mb-4 text-center">Create account</h3>

  {{-- Errors --}}
  @if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('register.store') }}" novalidate>
    @csrf

    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name" class="form-control form-control-lg" value="{{ old('name') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input name="email" type="email" class="form-control form-control-lg" value="{{ old('email') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>
      <input name="password" type="password" class="form-control form-control-lg" required>
    </div>

    <div class="mb-4">
      <label class="form-label">Confirm Password</label>
      <input name="password_confirmation" type="password" class="form-control form-control-lg" required>
    </div>

    <div class="d-grid gap-2">
      <button class="btn btn-primary btn-lg" type="submit">Register</button>
      <a class="btn btn-outline-light" href="{{ route('login') }}">Back to Sign in</a>
    </div>
  </form>
@endsection
