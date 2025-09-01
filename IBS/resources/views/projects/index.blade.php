@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="text-white">Projects</h2>

    <a href="{{ route('projects.create') }}" class="btn btn-primary mb-3">+ Add New Project</a>

    @if(session('success'))
    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-dark">
        <thead>
            <tr>
                <th>Name</th>
                <th>Client</th>
                <th>Result</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td>{{ $project->name }}</td>
                    <td>{{ $project->client->name ?? 'Unknown' }}</td>
                    <td>{{ $project->result }}</td>
                    <td>{{ $project->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this project?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
