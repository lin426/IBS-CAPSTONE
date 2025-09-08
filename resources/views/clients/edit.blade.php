@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="text-white">Edit Client</h2>

    <form method="POST" action="{{ route('clients.update', $client->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="text-white">Name</label>
            <input type="text" name="name" class="form-control" value="{{ $client->name }}" required>
        </div>

        <div class="mb-3">
            <label class="text-white">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $client->email }}" required>
        </div>

        <button class="btn btn-primary">Update Client</button>
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
