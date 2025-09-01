<?php

namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Project; // ✅ move this here
use Illuminate\Support\Facades\DB; // ✅ move this here



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

        $chartData = $leads->groupBy('stage')->map(fn ($group) => $group->count());
        

        // 📊 Project Line Chart Data
        $projectTrends = Project::selectRaw("strftime('%Y-%m', created_at) as month, result, COUNT(*) as total")

            ->groupBy('month', 'result')
            ->orderBy('month')
            ->get()
            ->groupBy('result');

        $goodData = $projectTrends['good'] ?? collect();
        $badData = $projectTrends['bad'] ?? collect();

// Define month range (3 months back to 3 months forward)
$start = now()->subMonths(3)->startOfMonth();
$end = now()->addMonths(3)->endOfMonth();

$monthLabels = collect();
$current = $start->copy();

while ($current <= $end) {
    $monthLabels->push($current->format('Y-m'));
    $current->addMonth();
}

// Initialize counts
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
            'leads' => $leads,
            'totalLeads' => $totalLeads,
            'totalValue' => $totalValue,
            'conversionRate' => $conversionRate,
            'chartData' => $chartData,
            'noData' => $noData,
            'labels' => $monthLabels,
            'goodCounts' => $goodCounts,
            'badCounts' => $badCounts,
            'upcomingTasks' => $upcomingTasks
        ]);
    }
}
