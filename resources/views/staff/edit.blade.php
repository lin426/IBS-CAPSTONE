@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Staff</h2>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('staff.update', $staff->id) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" value="{{ old('name', $staff->name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Position</label>
            <input name="position" class="form-control" value="{{ old('position', $staff->position) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Rating (1–5)</label>
            <select name="rating" class="form-control" required>
                <option value="">Select</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ old('rating', $staff->rating) == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>

        {{-- Email (updates the linked User) --}}
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email', $staff->user->email ?? '') }}" required>
            <small class="text-muted">Used as the staff login email.</small>
        </div>

        {{-- Reset Password (optional) --}}
        <div class="mb-3">
            <label class="form-label">
                Reset Password
                <small class="text-muted">(leave blank to keep current password)</small>
            </label>
            <input type="password" name="password" class="form-control"
                   minlength="6" autocomplete="new-password"
                   placeholder="Enter a new password or leave blank">
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('staff.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
