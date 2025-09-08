<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Lead;
use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        $leads = $query->get();
        $noData = $leads->isEmpty();

        $totalLeads = $leads->count();
        $totalValue = $leads->sum('value');
        $conversionRate = $totalLeads ? $leads->where('status', 'won')->count() / $totalLeads * 100 : 0;

        $chartData = $leads->groupBy('stage')->map(fn($group) => $group->count());

        // 📊 Project Line Chart Data
        $projectTrends = Project::selectRaw("strftime('%Y-%m', created_at) as month, result, COUNT(*) as total")
            ->groupBy('month', 'result')
            ->orderBy('month')
            ->get()
            ->groupBy('result');

        $start = now()->subMonths(3)->startOfMonth();
        $end   = now()->addMonths(3)->endOfMonth();

        $monthLabels = collect();
        $current = $start->copy();
        while ($current <= $end) {
            $monthLabels->push($current->format('Y-m'));
            $current->addMonth();
        }

        $goodCounts = $monthLabels->map(function ($month) use ($projectTrends) {
            return optional($projectTrends->get('good'))->firstWhere('month', $month)->total ?? 0;
        });

        $badCounts = $monthLabels->map(function ($month) use ($projectTrends) {
            return optional($projectTrends->get('bad'))->firstWhere('month', $month)->total ?? 0;
        });

        $upcomingTasks = Task::whereNotNull('due_date')
            ->whereDate('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            'leads'          => $leads,
            'totalLeads'     => $totalLeads,
            'totalValue'     => $totalValue,
            'conversionRate' => $conversionRate,
            'chartData'      => $chartData,
            'noData'         => $noData,
            'labels'         => $monthLabels,
            'goodCounts'     => $goodCounts,
            'badCounts'      => $badCounts,
            'upcomingTasks'  => $upcomingTasks,
        ]);
    }

    /**
     * Admin: list database notifications (newest first).
     * Route: GET /admin/notifications  (name: admin.notifications)
     */
    public function notifications(Request $request)
    {
        $notifications = $request->user()
            ->notifications()       // includes read + unread; use ->unreadNotifications() if you want only unread
            ->latest()
            ->paginate(20);

        return view('admin.notifications', compact('notifications'));
    }

    /**
     * Admin: mark all unread notifications as read.
     * Route: POST /admin/notifications/read-all  (name: admin.notifications.readAll)
     */
    public function readAllNotifications(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }
}
