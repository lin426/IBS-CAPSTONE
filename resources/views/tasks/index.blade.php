@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4 px-5">
    <h2 class="text-white">Task Management</h2>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary mb-3">+ Add Task</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <style>
        table th, table td { vertical-align: middle; }

        /* widths for 10 columns */
        th:nth-child(1), td:nth-child(1) { width: 16%; } /* Title */
        th:nth-child(2), td:nth-child(2) { width: 9%;  } /* Status */
        th:nth-child(3), td:nth-child(3) { width: 13%; } /* Project */
        th:nth-child(4), td:nth-child(4) { width: 12%; } /* Due Date */
        th:nth-child(5), td:nth-child(5) { width: 17%; } /* Assigned To */
        th:nth-child(6), td:nth-child(6) { width: 12%; } /* Progress */
        th:nth-child(7), td:nth-child(7) { width: 12%; } /* Proof */
        th:nth-child(8), td:nth-child(8) { width: 6%;  text-align:center; } /* Handler Rating */
        th:nth-child(9), td:nth-child(9) { width: 10%; } /* Note */
        th:nth-child(10), td:nth-child(10){ width: 7%;  text-align:center; } /* Actions */

        .progress { height: 14px; }
        .proof-grid { display:flex; flex-wrap:wrap; gap:.35rem; }
        .proof-thumb {
            width: 48px; height: 34px; object-fit: cover;
            border-radius: 4px; border: 1px solid #333;
            background: #111;
        }
        .proof-chip {
            display:inline-flex; align-items:center; gap:.35rem;
            padding: .15rem .45rem; border-radius: 12px;
            background:#1f2937; color:#e5e7eb; font-size:.85rem;
            border:1px solid #374151; text-decoration:none;
        }
        .proof-chip:hover { background:#111827; color:#fff; }
    </style>

    <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
        <table class="table table-bordered table-dark">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Project</th>
                    <th>Due Date</th>
                    <th>Assigned To</th>
                    <th>Progress</th>
                    <th>Proof</th> {{-- NEW --}}
                    <th>Handler Rating</th>
                    <th>Note</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($tasks as $task)
                @php
                    $p = (int) ($task->progress ?? 0);
                    $atts = $task->attachments ?? collect();
                    $isImg = function($path) {
                        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                        return in_array($ext, ['jpg','jpeg','png','gif','webp','bmp']);
                    };
                @endphp
                <tr>
                    <td>{{ $task->title }}</td>

                    <td>{{ $task->status }}</td>

                    <td>{{ $task->project->name ?? '—' }}</td>

                    <td>
                        @if(!empty($task->due_date))
                            {{ \Illuminate\Support\Carbon::parse($task->due_date)->format('Y-m-d H:i:s') }}
                        @else
                            —
                        @endif
                    </td>

                    <td>
                        @if($task->assignedStaff)
                            {{ $task->assignedStaff->name }}
                            — {{ $task->assignedStaff->position ?? 'No position' }}
                        @else
                            <em>Unassigned</em>
                        @endif
                    </td>

                    {{-- Progress (read-only for Admin) --}}
                    <td>
                        <div class="progress" title="{{ $p }}%">
                            <div class="progress-bar" role="progressbar"
                                 style="width: {{ $p }}%;"
                                 aria-valuenow="{{ $p }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $p }}%
                            </div>
                        </div>
                        @if(!empty($task->progress_note))
                            <small class="text-muted d-block mt-1">{{ $task->progress_note }}</small>
                        @endif
                    </td>

                    {{-- NEW: Proof (attachments uploaded by staff) --}}
                    <td>
                        @if($atts->count())
                            <div class="proof-grid">
                                @foreach($atts as $att)
                                    @php
                                        $url = \Illuminate\Support\Facades\Storage::url($att->path);
                                        $fileName = basename($att->path);
                                    @endphp

                                    @if($isImg($att->path))
                                        <a href="{{ $url }}" target="_blank" title="{{ $fileName }}">
                                            <img class="proof-thumb" src="{{ $url }}" alt="proof">
                                        </a>
                                    @else
                                        <a class="proof-chip" href="{{ $url }}" target="_blank" title="{{ $fileName }}">
                                            📄 <span style="max-width:110px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $fileName }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">None</span>
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
</div>
@endsection
