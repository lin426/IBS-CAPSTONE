@extends('layouts.app')

@section('content')
<div class="container">
  <h3 class="mb-3">Client Request</h3>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card mb-3">
    <div class="card-body">
      <div class="mb-2"><strong>Subject:</strong> {{ $req->subject }}</div>

      <div class="mb-2">
        <strong>From:</strong>
        {{ $req->client->name ?? 'Client' }}
        ({{ $req->client->email ?? 'unknown' }})
      </div>

      <div class="mb-2">
        <strong>Related Task:</strong>
        @if ($req->task)
          {{ $req->task->title }}
        @else
          —
        @endif
      </div>

      <div class="mb-2">
        <strong>Status:</strong> {{ ucfirst($req->status) }}
      </div>

      <div class="mb-2">
        <strong>Sent:</strong> {{ $req->created_at->format('Y-m-d H:i') }}
      </div>

      <hr>

      <div class="mb-2"><strong>Message</strong></div>
      <div class="p-3 rounded" style="background:#111;border:1px solid #333;white-space:pre-wrap;">
        {{ $req->message }}
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <h5 class="mb-3">Update Status</h5>
      <form method="POST" action="{{ route('admin.requests.update', $req) }}">
        @csrf @method('PATCH')
        <div class="row g-2 align-items-end">
          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="open"     @selected($req->status==='open')>Open</option>
              <option value="resolved" @selected($req->status==='resolved')>Resolved</option>
            </select>
          </div>
          <div class="col-md-8">
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('admin.requests.index') }}" class="btn btn-secondary ms-2">Back</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
