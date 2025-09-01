@extends('layouts.app')

@section('title', 'Clients')
@section('header', 'Clients')

@section('content')
<div class="container mt-4">
    <h2 class="text-white">Clients</h2>
    <a href="{{ route('clients.create') }}" class="btn btn-primary mb-3">+ Add New Client</a>

    @if ($clients->isEmpty())
        <p class="text-white">No clients yet.</p>
    @else
    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered table-dark text-white">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clients as $client)
                    <tr>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->email }}</td>
                        <td>
                            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('clients.destroy', $client->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this client?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>


@endsection
