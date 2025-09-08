@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="text-white">Add New Project</h2>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

 <form method="POST" action="{{ route('projects.store') }}">
    @csrf

    <div class="mb-3">
        <label class="text-white">Project Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="text-white">Client</label>
        <select name="client_id" class="form-control" required>
            <option value="">-- Select Client --</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}">{{ $client->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="text-white">Result</label>
        <select name="result" class="form-control">
            <option value="">-- Select Result --</option>
            <option value="good">Good</option>
            <option value="bad">Bad</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="text-white">Creation Date</label>
        <input type="date" name="created_at" class="form-control" value="{{ date('Y-m-d') }}" required>
    </div>

    <button class="btn btn-success">Save Project</button>
    <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
</form>



@endsection
