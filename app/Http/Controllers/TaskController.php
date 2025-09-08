<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index()
    {
        // Eager-load everything the table needs, including attachments (proof files)
        $tasks = Task::with([
            'handler',
            'assignedStaff',
            'project',
            'clientUser',
            'attachments',   // ← important
        ])->latest('id')->get();

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $projects    = Project::all();
        $clientUsers = User::where('role', 'client')->orderBy('name')->get();

        return view('tasks.create', compact('projects', 'clientUsers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'             => ['required','string','max:255'],
            'description'       => ['nullable','string'],
            'status'            => ['required','string'],
            'due_date'          => ['required','date','after_or_equal:today'],
            'assigned_staff_id' => ['nullable','exists:staff,id'],
            'project_id'        => ['nullable','exists:projects,id'],
            'client_user_id'    => [
                'nullable',
                Rule::exists('users','id')->where(fn ($q) => $q->where('role','client')),
            ],
            'rating'            => ['nullable','integer','min:1','max:5'],
            'recommendation'    => ['nullable','string'],
        ], [
            'due_date.after_or_equal' => 'Cannot add expired date tasks.',
        ]);

        $task = Task::create([
            'title'             => $data['title'],
            'description'       => $data['description'] ?? null,
            'status'            => $data['status'],
            'due_date'          => $data['due_date'],
            'assigned_staff_id' => $data['assigned_staff_id'] ?? null,
            'project_id'        => $data['project_id'] ?? null,
            'client_user_id'    => $data['client_user_id'] ?? null,
        ]);

        if (!empty($data['rating'])) {
            $task->handler()->create([
                'rating'         => $data['rating'],
                'recommendation' => $data['recommendation'] ?? null,
            ]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task added.');
    }

    public function edit(Task $task)
    {
        // Eager-load so the edit page can show existing handler + attachments if needed
        $task->load(['handler', 'attachments', 'assignedStaff', 'project', 'clientUser']);

        $projects    = Project::all();
        $clientUsers = User::where('role','client')->orderBy('name')->get();

        return view('tasks.edit', compact('task', 'projects', 'clientUsers'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title'             => ['required','string','max:255'],
            'description'       => ['nullable','string'],
            'status'            => ['required','string'],
            'due_date'          => ['required','date','after_or_equal:today'],
            'assigned_staff_id' => ['nullable','exists:staff,id'],
            'project_id'        => ['nullable','exists:projects,id'],
            'client_user_id'    => [
                'nullable',
                Rule::exists('users','id')->where(fn ($q) => $q->where('role','client')),
            ],
            'rating'            => ['nullable','integer','min:1','max:5'],
            'recommendation'    => ['nullable','string'],
        ], [
            'due_date.after_or_equal' => 'Cannot add expired date tasks.',
        ]);

        $task->update([
            'title'             => $data['title'],
            'description'       => $data['description'] ?? null,
            'status'            => $data['status'],
            'due_date'          => $data['due_date'],
            'assigned_staff_id' => $data['assigned_staff_id'] ?? null,
            'project_id'        => $data['project_id'] ?? null,
            'client_user_id'    => $data['client_user_id'] ?? null,
        ]);

        if (!empty($data['rating'])) {
            $task->handler
                ? $task->handler->update([
                    'rating'         => $data['rating'],
                    'recommendation' => $data['recommendation'] ?? null,
                ])
                : $task->handler()->create([
                    'rating'         => $data['rating'],
                    'recommendation' => $data['recommendation'] ?? null,
                ]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        // Optional but tidy: remove attached files from storage
        $task->load('attachments');
        foreach ($task->attachments as $att) {
            if (!empty($att->path)) {
                Storage::disk('public')->delete($att->path);
            }
        }
        $task->attachments()->delete();

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }

    public function show(Task $task)
    {
        $task->load(['handler', 'attachments', 'assignedStaff', 'project', 'clientUser']);

        return view('tasks.show', compact('task'));
    }
}
