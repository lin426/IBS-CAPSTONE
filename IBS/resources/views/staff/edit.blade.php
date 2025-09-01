@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Staff</h2>
    <form method="POST" action="{{ route('staff.update', $staff->id) }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Name</label>
            <input name="name" class="form-control" value="{{ $staff->name }}" required>
        </div>

        <div class="mb-3">
            <label>Position</label>
            <input name="position" class="form-control" value="{{ $staff->position }}">
        </div>

        <div class="mb-3">
            <label>Rating (1–5)</label>
            <select name="rating" class="form-control" required>
                <option value="">Select</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ $staff->rating == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('staff.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
