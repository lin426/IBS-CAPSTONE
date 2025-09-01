@extends('layouts.app')

@section('content')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.querySelector('input[name="title"]');
    const staffSelect = document.querySelector('select[name="assigned_staff_id"]');
    const descriptionBox = document.querySelector('textarea[name="description"]');

    function generateSuggestion() {
        const title = titleInput.value.trim().toLowerCase();
        const staffText = staffSelect.options[staffSelect.selectedIndex]?.text.toLowerCase() || '';
        let suggestions = [];

        if (title.includes('report')) suggestions.push('Prepare a detailed report including key insights and data analysis.');
        if (title.includes('meet')) suggestions.push('Organize and attend a scheduled meeting; include minutes if necessary.');
        if (title.includes('email')) suggestions.push('Draft and send out a professional email regarding the task.');
        if (title.includes('design')) suggestions.push('Create or revise the necessary design materials based on specifications.');
        if (title.includes('survey')) suggestions.push('Distribute and collect survey results, then summarize key takeaways.');
        if (title.includes('present')) suggestions.push('Prepare slides and talking points for an upcoming presentation.');
        if (title.includes('call')) suggestions.push('Set up and attend the call; prepare notes beforehand.');
        if (title.includes('doc')) suggestions.push('Complete or update internal documentation relevant to this task.');
        if (title.includes('review')) suggestions.push('Conduct a review and provide feedback or approval.');
        if (title.includes('test')) suggestions.push('Run quality assurance or functionality tests and report issues.');
        if (title.includes('upload') || title.includes('publish')) suggestions.push('Upload and verify the content or asset to the target platform.');
        if (title.includes('feed')) suggestions.push('Collect user or team feedback and summarize the points.');
        if (title.includes('data')) suggestions.push('Process and analyze data according to task scope.');
        if (title.includes('backend') || title.includes('api')) suggestions.push('Implement or maintain backend logic, including endpoints if necessary.');
        if (title.includes('front')) suggestions.push('Work on user interface elements and ensure responsiveness.');
        if (title.includes('content')) suggestions.push('Write, revise, or approve content as required.');
        if (title.includes('deploy')) suggestions.push('Coordinate deployment, test post-launch, and ensure rollback plan is ready.');
        if (title.includes('engage')) suggestions.push('Engage with community and speak leads to createa and incite collaboration with clients.');

        if (suggestions.length > 0 && descriptionBox.value.trim() === '') {
            descriptionBox.value = suggestions.join(' ');
        }
    }

    titleInput?.addEventListener('input', generateSuggestion);
    staffSelect?.addEventListener('change', generateSuggestion);
});
</script>

<div class="container">
    <h2>Edit Task</h2>

    @if ($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('tasks.update', $task->id) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Title</label>
            <input name="title" value="{{ old('title', $task->title) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ old('description', $task->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="project_id" class="form-label text-white">Assigned Project</label>
            <select name="project_id" class="form-control">
                <option value="">— Select a Project —</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected(old('project_id', $task->project_id)==$project->id)>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Client (User) --}}
        <div class="mb-3">
            <label>Client (User)</label>
            <select name="client_user_id" class="form-control">
                <option value="">— None —</option>
                @foreach($clientUsers as $u)
                    <option value="{{ $u->id }}" @selected(old('client_user_id', $task->client_user_id)==$u->id)>
                        {{ $u->name }} ({{ $u->email }})
                    </option>
                @endforeach
            </select>
            @error('client_user_id') <small class="text-danger d-block">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                @foreach(['pending','in_progress','completed'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $task->status)===$status)>
                        {{ ucfirst(str_replace('_',' ', $status)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Due Date</label>
            <input type="date"
                   name="due_date"
                   class="form-control"
                   value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}">
            @error('due_date') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>

        @php
            $staffList = \App\Models\Staff::orderByDesc('rating')->get();
        @endphp
        <div class="mb-3">
            <label>Assign to Staff</label>
            <select name="assigned_staff_id" class="form-control">
                <option value="">Unassigned</option>
                @foreach($staffList as $staff)
                    <option value="{{ $staff->id }}" @selected(old('assigned_staff_id', $task->assigned_staff_id)==$staff->id)>
                        {{ $staff->name }} – {{ $staff->position ?? 'No position' }} (Rating: {{ $staff->rating }})
                    </option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
