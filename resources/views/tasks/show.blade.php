@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="text-white">Task Details</h2>
        <div class="card bg-dark text-white p-3">
            <h5>{{ $task->title }}</h5>
            <p><strong>Description:</strong> {{ $task->description ?? '—' }}</p>
            <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</p>
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary mt-3">⬅ Back to Tasks</a>
        </div>
    </div>
@endsection
