@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="text-white mb-3">Staff Dashboard</h2>

    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        use Illuminate\Support\Facades\Storage;
    @endphp

    <div class="card bg-dark text-white mb-3">
        <div class="card-body">
            <h5 class="card-title mb-2">
                Welcome, {{ $user->name ?? auth()->user()->name }}
            </h5>
            <p class="mb-1"><strong>Role:</strong> {{ auth()->user()->role }}</p>

            @if(isset($staff) && $staff)
                <p class="mb-1"><strong>Position:</strong> {{ $staff->position ?? 'N/A' }}</p>
                <p class="mb-0"><strong>Rating:</strong> {{ $staff->rating }} / 5</p>
            @else
                <p class="mb-0 text-warning">No Staff profile linked to this account yet.</p>
            @endif
        </div>
    </div>

    @if(isset($stats))
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card bg-dark text-white">
                <div class="card-body d-flex justify-content-between">
                    <span>Total Tasks</span>
                    <strong>{{ $stats['total'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white">
                <div class="card-body d-flex justify-content-between">
                    <span>Open</span>
                    <strong>{{ $stats['open'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white">
                <div class="card-body d-flex justify-content-between">
                    <span>Done</span>
                    <strong>{{ $stats['done'] }}</strong>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card bg-dark text-white">
        <div class="card-body">
            <h5 class="card-title mb-3">My Recent Tasks</h5>

            @if(isset($tasks) && $tasks->count())
                <div class="table-responsive">
                    <table class="table table-dark table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="min-width: 220px;">Title</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Due</th>
                                <th style="width: 280px;">Progress</th>
                                <th style="min-width: 260px;">Attachments</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $t)
                                @php
                                    $canEdit = isset($staff) && $staff && (int)$t->assigned_staff_id === (int)$staff->id;
                                    $progress = (int)($t->progress ?? 0);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $t->title ?? ('Task #'.$t->id) }}</div>
                                        <div class="text-muted small">{{ \Illuminate\Support\Str::limit($t->description ?? '', 120) }}</div>
                                    </td>
                                    <td>{{ ucfirst($t->status ?? 'n/a') }}</td>
                                    <td>{{ ucfirst($t->priority ?? 'normal') }}</td>
                                    <td>
                                        @if(!empty($t->due_date))
                                            {{ \Illuminate\Support\Carbon::parse($t->due_date)->format('M d, Y') }}
                                        @else
                                            —
                                        @endif
                                    </td>

                                    {{-- Progress column --}}
                                    <td>
                                        {{-- Read-only bar (visible to everyone) --}}
                                        <div class="progress" style="height: 14px;">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: {{ $progress }}%;"
                                                 aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $progress }}%
                                            </div>
                                        </div>
                                        @if(!empty($t->progress_note))
                                            <small class="text-muted d-block mt-1">Note: {{ $t->progress_note }}</small>
                                        @endif

                                        {{-- Staff-only editor for assigned staff --}}
                                        @if($canEdit)
                                            <form class="mt-2" method="POST" action="{{ route('staff.portal.tasks.progress', $t) }}">
                                                @csrf
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="range" min="0" max="100" name="progress"
                                                           value="{{ $progress }}"
                                                           oninput="this.nextElementSibling.value=this.value+'%'"
                                                           class="form-range" style="width: 160px;">
                                                    <output>{{ $progress }}%</output>

                                                    <label class="ms-2 mb-0 d-flex align-items-center gap-1">
                                                        <input type="checkbox" name="complete" value="1"
                                                               {{ $progress >= 100 ? 'checked disabled' : '' }}>
                                                        <span class="small">Mark complete</span>
                                                    </label>
                                                </div>

                                                <textarea name="note" class="form-control form-control-sm mt-2"
                                                          rows="2" placeholder="Optional progress note...">{{ old('note', $t->progress_note) }}</textarea>

                                                <button class="btn btn-primary btn-sm mt-2">Save Progress</button>
                                            </form>
                                        @endif
                                    </td>

                                    {{-- Attachments column --}}
                                    <td>
                                        {{-- List existing attachments (everyone can view) --}}
                                        @if($t->attachments && $t->attachments->count())
                                            <ul class="mb-2" style="padding-left: 18px;">
                                                @foreach($t->attachments as $att)
                                                    <li class="small">
                                                        <a href="{{ Storage::url($att->path) }}" target="_blank">
                                                            {{ $att->original_name }}
                                                        </a>
                                                        @if($att->size)
                                                            <span class="text-muted">({{ number_format($att->size / 1024, 1) }} KB)</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted small d-block mb-2">No attachments</span>
                                        @endif

                                        {{-- Staff-only upload (assigned staff) --}}
                                        @if($canEdit)
                                            <form method="POST" action="{{ route('staff.portal.tasks.attachments.upload', $t) }}"
                                                  enctype="multipart/form-data">
                                                @csrf
                                                <input type="file" name="attachments[]" class="form-control form-control-sm" multiple>
                                                <button class="btn btn-secondary btn-sm mt-2">Upload</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $tasks->links() }}
                </div>
            @else
                <p class="text-muted mb-0">No tasks assigned yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
