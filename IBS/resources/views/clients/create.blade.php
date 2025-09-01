@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="text-white">Add New Client</h2>

    <form method="POST" action="{{ route('clients.store') }}">
        @csrf

        <div class="mb-3">
            <label class="text-white">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="text-white">Email</label>
            <input type="email" name="email" class="form-control" placeholder="e.g. john@example.com" required>
        </div>

        <button class="btn btn-success">Add Client</button>
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
