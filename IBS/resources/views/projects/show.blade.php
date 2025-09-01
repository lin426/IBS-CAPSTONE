<form action="{{ route('messages.store') }}" method="POST">
    @csrf
    <input type="hidden" name="project_id" value="{{ $project->id }}">
    <input type="text" name="sender" value="client" hidden>
    <textarea name="content" required></textarea>
    <button type="submit">Send</button>
</form>
