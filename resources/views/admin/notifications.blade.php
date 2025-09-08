@extends('layouts.app')
@section('content')
<div class="container">
  <h3 class="mb-3">Notifications</h3>

  <form method="POST" action="{{ route('admin.notifications.readAll') }}" class="mb-3">
    @csrf
    <button class="btn btn-sm btn-outline-light">Mark all as read</button>
  </form>

  <div class="list-group">
    @forelse($notifications as $n)
      <div class="list-group-item bg-dark text-white mb-2">
        <div class="d-flex justify-content-between">
          <div>
            <strong>{{ $n->data['title'] ?? 'Task' }}</strong> — {{ $n->data['message'] ?? '' }}
            @if(!empty($n->data['task_id']))
              <a class="ms-2" href="{{ url('/tasks/'.$n->data['task_id']) }}">View</a>
            @endif
          </div>
          <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
        </div>
      </div>
    @empty
      <p>No notifications.</p>
    @endforelse
  </div>

  <div class="mt-3">{{ $notifications->links() }}</div>
</div>
@endsection
