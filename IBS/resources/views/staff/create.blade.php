@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Staff</h2>
    <form method="POST" action="{{ route('staff.store') }}">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Position</label>
            <input name="position" class="form-control">
        </div>

        <div class="mb-3">
            <label>Rating (1–5)</label>
            <select name="rating" class="form-control" required>
                <option value="">Select</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <button class="btn btn-success">Save</button>
        <a href="{{ route('staff.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
