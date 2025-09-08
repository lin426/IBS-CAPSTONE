@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="text-white">Edit Project</h2>

    <form method="POST" action="{{ route('projects.update', $project->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="text-white">Project Name</label>
            <input type="text" name="name" class="form-control" value="{{ $project->name }}" required>
        </div>

        <div class="mb-3">
            <label class="text-white">Client</label>
            <select name="client_id" class="form-control" required>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" {{ $project->client_id == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="text-white">Result</label>
            <select name="result" class="form-control">
                <option value="">-- Select Result --</option>
                <option value="good" {{ $project->result == 'good' ? 'selected' : '' }}>Good</option>
                <option value="bad" {{ $project->result == 'bad' ? 'selected' : '' }}>Bad</option>
            </select>
        </div>

        <button class="btn btn-primary">Update Project</button>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
