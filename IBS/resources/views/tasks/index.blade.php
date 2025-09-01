@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4 px-5">
    <h2 class="text-white">Task Management</h2>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary mb-3">+ Add Task</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <style>
    table th, table td {
        vertical-align: middle;
    }
    th:nth-child(1), td:nth-child(1) { width: 18%; }
    th:nth-child(2), td:nth-child(2) { width: 10%; }
    th:nth-child(3), td:nth-child(3) { width: 12%; }
    th:nth-child(4), td:nth-child(4) { width: 18%; }
    th:nth-child(5), td:nth-child(5) { width: 10%; text-align: center; }
    th:nth-child(6), td:nth-child(6) { width: 22%; }
    th:nth-child(7), td:nth-child(7) { width: 10%; text-align: center; }
</style>

<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
    <table class="table table-bordered table-dark">
<thead>
    <tr>
        <th>Title</th>
        <th>Status</th>
        <th>Project</th>
        <th>Due Date</th>
        <th>Assigned To</th> {{-- New --}}
        <th>Handler Rating</th> {{-- ADDED RATING --}}
        <th>Note</th> {{-- ADDED NOTE --}}
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    @foreach($tasks as $task)
    <tr>
        <td>{{ $task->title }}</td>
<td>{{ $task->status }}</td>
<td>{{ $task->project->name ?? '—' }}</td> {{-- ✅ Project column --}}
<td>{{ $task->due_date }}</td> {{-- ✅ Moved to proper place --}}
<td>
    @if($task->assignedStaff)
        {{ $task->assignedStaff->name }} – {{ $task->assignedStaff->position ?? 'No position' }}
    @else
        <em>Unassigned</em>
    @endif
</td>
<td>
    @if($task->handler)
        {{ $task->handler->rating }} / 5
    @else
        —
    @endif
</td>
<td>
    @if($task->handler)
        {{ $task->handler->recommendation ?? 'None' }}
    @else
        —
    @endif
</td>
<td>
    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning btn-sm">Edit</a>
    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline;">
        @csrf @method('DELETE')
        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this task?')">Delete</button>
    </form>
</td>

    </tr>
    @endforeach
</tbody>
    </table>
</div>
@endsection
