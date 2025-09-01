@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="text-white">Staff List</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('staff.create') }}" class="btn btn-primary mb-3">+ Add Staff</a>
<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
    <table class="table table-dark table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Rating</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($staff as $member)
            <tr>
                <td>{{ $member->name }}</td>
                <td>{{ $member->position ?? 'N/A' }}</td>
                <td>{{ $member->rating }} / 5</td>
                <td>
                    <a href="{{ route('staff.edit', $member->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('staff.destroy', $member->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this staff?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection