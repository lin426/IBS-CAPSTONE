 <!-- this is for the client POV not the client add -->
@extends('layouts.app')

@section('content')
<div class="row g-4">
  <div class="col-lg-8">
    <div class="card mb-3">
      <div class="card-body">
        <h3 class="mb-3">Client Portal</h3>
        <p class="text-muted mb-4">Welcome! Your client portal is active. (More features can be added later.)</p>

        {{-- Tasks table (read-only) --}}
        <h5 class="mb-3">Tasks</h5>
         <a href="{{ route('client.export.pdf') }}" class="btn btn-sm btn-outline-light">
    Download PDF
     </a>
        <div class="table-responsive">
          <table class="table table-dark table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Assigned To</th>
                <th>Handler Rating</th>
                <th>Your Rating</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($tasks as $task)
                <tr>
                  <td>{{ $task->title ?? '—' }}</td>
                  <td>{{ $task->status ?? '—' }}</td>
                  <td>{{ optional($task->due_date)->format('Y-m-d') ?? ($task->due_date ?? '—') }}</td>
                  <td>
                    {{-- if your schema stores a name string: --}}
                    {{ $task->assigned_to ?? ($task->assignedStaff->name ?? '—') }}
                  </td>
                  <td>{{ $task->handler_rating ?? '—' }}</td>
                  <td>
                    @if ($task->client_rating)
                      {{ $task->client_rating }} / 5
                    @else
                      <form method="POST" action="{{ route('client.tasks.rate', $task) }}" class="d-flex gap-2">
                        @csrf
                        <select name="rating" class="form-select form-select-sm" style="width: 90px;" required>
                          <option value="" hidden>Rate</option>
                          @for ($i=1; $i<=5; $i++)
                            <option value="{{ $i }}">{{ $i }} / 5</option>
                          @endfor
                        </select>
                        <button class="btn btn-primary btn-sm">Submit</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted">No tasks yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $tasks->links() }}
        </div>
      </div>
    </div>
  </div>

  {{-- Right column --}}
  <div class="col-lg-4">
    {{-- Live date & time --}}
    <div class="card mb-3">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-1">Date &amp; Time</h5>
          <div class="text-muted">Your local time</div>
        </div>
        <div id="liveClock" class="h5 mb-0 fw-bold"></div>
      </div>
    </div>

    {{-- Request to admin --}}
    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">Request to Admin</h5>

        @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

        <form method="POST" action="{{ route('client.requests.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Subject</label>
            <input class="form-control" name="subject" required maxlength="255" value="{{ old('subject') }}">
          </div>

          <div class="mb-3">
            <label class="form-label">Related Task (optional)</label>
            <select name="task_id" class="form-select">
              <option value="">— None —</option>
              @foreach ($tasks as $t)
                <option value="{{ $t->id }}" @selected(old('task_id')==$t->id)>{{ $t->title }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea class="form-control" name="message" rows="4" required>{{ old('message') }}</textarea>
          </div>

          <button class="btn btn-primary w-100">Send request</button>
        </form>

        @if(isset($requests) && $requests->count())
          <hr class="my-4">
          <h6 class="mb-2">Recent Requests</h6>
          <ul class="list-unstyled small mb-0">
            @foreach ($requests as $r)
              <li class="mb-2">
                <strong>{{ $r->subject }}</strong>
                <div class="text-muted">
                  {{ $r->created_at->format('Y-m-d H:i') }} • Status: {{ ucfirst($r->status) }}
                </div>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Live clock --}}
<script>
(function(){
  function updateClock(){
    const el = document.getElementById('liveClock');
    if (!el) return;
    const d = new Date();
    el.textContent = d.toLocaleString();
  }
  updateClock();
  setInterval(updateClock, 1000);
})();
</script>
@endsection
