<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// NEW: for PDF export
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ClientPortalController extends Controller
{
    /**
     * Client dashboard: tasks, recent requests
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Only tasks tied to this client user; eager-load to avoid N+1
        $tasks = Task::with(['assignedStaff', 'handler'])
            ->where('client_user_id', $user->id)
            ->orderBy('due_date')
            ->paginate(10);

        // Recent requests by this client
        $requests = ClientRequest::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('client.index', compact('tasks', 'requests'));
    }

    /**
     * Client rates a task (1..5) — only if the task belongs to this client
     */
    public function rateTask(Task $task, Request $request)
    {
        abort_if($task->client_user_id !== $request->user()->id, 403);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $task->client_rating = $data['rating'];
        $task->save();

        return back()->with('success', 'Thanks for your rating!');
    }

    /**
     * Client sends a request/message to admin; task_id optional (must belong to the client if provided)
     */
    public function storeRequest(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'task_id' => [
                'nullable',
                Rule::exists('tasks', 'id')->where(fn ($q) => $q->where('client_user_id', $user->id)),
            ],
        ]);

        ClientRequest::create([
            'user_id' => $user->id,
            'task_id' => $data['task_id'] ?? null,
            'subject' => $data['subject'],
            'message' => $data['message'],
            // status defaults to "open" from the migration
        ]);

        return back()->with('success', 'Request sent to admin.');
    }

    /**
     * EXPORT: download this client's data (tasks + requests) as a PDF
     */
    public function exportPdf(Request $request)
    {
        $user = $request->user();

        // All tasks for this client (no pagination for export)
        $tasks = Task::with(['assignedStaff', 'project', 'handler'])
            ->where('client_user_id', $user->id)
            ->orderBy('due_date')
            ->get();

        // All requests from this client
        $requests = ClientRequest::with('task')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $now = Carbon::now()->format('Y-m-d H:i:s');

        $pdf = Pdf::loadView('client.pdf', [
            'user'        => $user,
            'tasks'       => $tasks,
            'requests'    => $requests,
            'generated_at'=> $now,
        ])->setPaper('a4', 'portrait');

        $filename = 'InsightBlitz_' . preg_replace('/\s+/', '_', $user->name ?? 'client') . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
        // If you prefer in-browser preview:
        // return $pdf->stream($filename);
    }
}
