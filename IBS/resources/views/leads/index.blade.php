@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Sales Pipeline</h1>

    <a href="{{ route('leads.create') }}" class="btn btn-primary mb-3">+ Add New Lead</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
    <table class="table table-bordered table-dark">
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Stage</th>
                <th>Value</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leads as $lead)
            <tr>
                <td>{{ $lead->name }}</td>
                <td>{{ $lead->contact }}</td>
                <td>{{ $lead->stage }}</td>
                <td>{{ $lead->value }}</td>
                <td>{{ $lead->status }}</td>
                <td>
                    <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this lead?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


@endsection