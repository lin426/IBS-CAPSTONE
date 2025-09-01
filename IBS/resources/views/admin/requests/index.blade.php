@extends('layouts.app')

@section('content')
<div class="card">
  <div class="card-body">
    <h3 class="mb-3">Client Requests</h3>

    <ul class="nav nav-pills mb-3">
      <li class="nav-item">
        <a class="nav-link {{ ($activeTab ?? 'open') === 'open' ? 'active' : '' }}"
           href="{{ route('admin.requests.index') }}">
          Open <span class="badge bg-secondary">{{ $openCount ?? 0 }}</span>
        </a>
      </li>
      <li class="nav-item ms-2">
        <a class="nav-link {{ ($activeTab ?? '') === 'history' ? 'active' : '' }}"
           href="{{ route('admin.requests.history') }}">
          History <span class="badge bg-secondary">{{ $resolvedCount ?? 0 }}</span>
        </a>
      </li>
    </ul>

    <div class="table-responsive">
      <table class="table table-dark table-hover align-middle">
        <thead>
        <tr>
          <th>Subject</th>
          <th>Client</th>
          <th>Related Task</th>
          <th>Status</th>
          <th>Received</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse ($requests as $r)
          <tr>
            <td>{{ $r->subject }}</td>
            <td>{{ $r->client?->name }} ({{ $r->client?->email }})</td>
            <td>{{ $r->task?->title ?? '—' }}</td>
            <td>{{ ucfirst($r->status) }}</td>
            <td>{{ $r->created_at?->format('Y-m-d H:i') }}</td>
            <td>
              <a href="{{ route('admin.requests.show', $r) }}" class="btn btn-sm btn-primary">View</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted">No requests.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $requests->links() }}
    </div>
  </div>
</div>
@endsection
