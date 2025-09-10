@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="text-white">Business Intelligence Dashboard</h2>

    {{-- Filters --}}
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="text-white">Status</label>
            <select name="status" class="form-control">
                <option value="">All</option>
                <option value="open">Open</option>
                <option value="won">Won</option>
                <option value="lost">Lost</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="text-white">Stage</label>
            <select name="stage" class="form-control">
                <option value="">All</option>
                <option value="Prospect">Prospect</option>
                <option value="Contacted">Contacted</option>
                <option value="Proposal">Proposal</option>
                <option value="Closed">Closed</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="text-white">From</label>
            <input type="date" name="from" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="text-white">To</label>
            <input type="date" name="to" class="form-control">
        </div>
        <div class="col-md-12">
            <button class="btn btn-primary mt-2">Filter</button>
        </div>
    </form>

    {{-- Key Metrics & Upcoming Tasks --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="text-white">
                <h5>Key Metrics</h5>
                <ul>
                    <li>Total Leads: {{ $totalLeads }}</li>
                    <li>Total Value: ₱{{ number_format($totalValue, 2) }}</li>
                    <li>Conversion Rate: {{ number_format($conversionRate, 2) }}%</li>
                </ul>
            </div>
                    {{-- Predictive Analysis --}}
            <div class="mt-4 text-white">
                    <h5>📈 Predictive Insights</h5>
                <ul>
                    @php
                        $topStage = $chartData->sortDesc()->keys()->first();
                        $leadTrend = $chartData->max() > 1 ? "increasing" : "stable";
                        $projectInsight = $goodCounts->sum() > $badCounts->sum()
                            ? "Good project outcomes are trending higher"
                            : "There are more bad projects than good ones currently";
                        $conversionStatus = $conversionRate >= 50
                            ? "strong"
                            : ($conversionRate >= 25 ? "moderate" : "low");
                    @endphp
                    <li>Most leads are currently in the <strong>{{ $topStage }}</strong> stage.</li>
                    <li>Lead distribution trend appears to be <strong>{{ $leadTrend }}</strong>.</li>
                    <li>{{ $projectInsight }}.</li>
                    <li>Current lead conversion rate of <strong>{{ number_format($conversionRate, 2) }}%</strong> is considered <strong>{{ $conversionStatus }}</strong>.</li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-4">
            <h5 class="text-white mb-3">🗕️ Upcoming Tasks</h5>
            <ul class="list-group">
                @forelse ($upcomingTasks as $task)
                    <li class="list-group-item d-flex justify-content-between align-items-center"
                        style="background-color: #1a1a1a; color: #f0f0f0; border: 1px solid #333;">
                        <div>
                            {{ $task->title }} <br>
                            <small style="color: #ccc;">Due: {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-warning rounded-pill">✏️</a>
                            <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-info rounded-pill">👁️</a>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-white" style="background-color: #1a1a1a; border: 1px solid #333;">
                        No upcoming tasks.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Charts --}}
    @if ($noData)
        <div class="alert alert-warning">
            No data available for the selected date range.
        </div>
    @else
        <div class="mb-4">
            <canvas id="leadChart"></canvas>
        </div>

        <div class="mb-4">
            <h5 class="text-white">Project Results Over Time</h5>
            <canvas id="projectLineChart"></canvas>
        </div>

        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-bordered table-dark">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Stage</th>
                        <th>Status</th>
                        <th>Value</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            <td>{{ $lead->name }}</td>
                            <td>{{ $lead->stage }}</td>
                            <td>{{ $lead->status }}</td>
                            <td>{{ $lead->value }}</td>
                            <td>{{ $lead->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="mb-4">
    <h5 class="text-white">📊 Task Popularity (Top Completed Tasks Per Month)</h5>
    <canvas id="taskPopularityChart"></canvas>
    <p class="text-muted mt-2">
        Last month’s most popular task: 
        <strong>{{ $taskTopNames->last() ?? 'None' }}</strong>
        ({{ $taskTopCounts->last() ?? 0 }})
    </p>
</div>

<div class="mb-4">
    <h5 class="text-white">📊 Project Popularity (Top Finished Projects Per Month)</h5>
    <canvas id="projectPopularityChart"></canvas>
    <p class="text-muted mt-2">
        Last month’s most popular project: 
        <strong>{{ $projectTopNames->last() ?? 'None' }}</strong>
        ({{ $projectTopCounts->last() ?? 0 }})
    </p>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // === Leads by Stage Chart ===
    const ctx = document.getElementById('leadChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData->keys()) !!},
                datasets: [{
                    label: 'Leads by Stage',
                    data: {!! json_encode($chartData->values()) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            }
        });
    }

    // === Project Results Line Chart ===
    const projectCtx = document.getElementById('projectLineChart')?.getContext('2d');
    if (projectCtx) {
        new Chart(projectCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [
                    {
                        label: 'Good Projects',
                        data: {!! json_encode($goodCounts) !!},
                        borderColor: 'rgba(75, 192, 192, 1)',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Bad Projects',
                        data: {!! json_encode($badCounts) !!},
                        borderColor: 'rgba(255, 99, 132, 1)',
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    // === Task Popularity Chart ===
    const taskCtx = document.getElementById('taskPopularityChart')?.getContext('2d');
    if (taskCtx) {
        new Chart(taskCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($popMonths) !!},
                datasets: [{
                    label: 'Top Task Frequency',
                    data: {!! json_encode($taskTopCounts) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const taskNames = {!! json_encode($taskTopNames) !!};
                                return `${taskNames[context.dataIndex] || 'No Task'}: ${context.raw}`;
                            }
                        }
                    }
                },
                responsive: true,
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }

    // === Project Popularity Chart ===
    const projPopCtx = document.getElementById('projectPopularityChart')?.getContext('2d');
    if (projPopCtx) {
        new Chart(projPopCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($popMonths) !!},
                datasets: [{
                    label: 'Top Project Frequency',
                    data: {!! json_encode($projectTopCounts) !!},
                    backgroundColor: 'rgba(255, 99, 132, 0.7)'
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const projectNames = {!! json_encode($projectTopNames) !!};
                                return `${projectNames[context.dataIndex] || 'No Project'}: ${context.raw}`;
                            }
                        }
                    }
                },
                responsive: true,
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }
</script>
@endsection





{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('leadChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData->keys()) !!},
                datasets: [{
                    label: 'Leads by Stage',
                    data: {!! json_encode($chartData->values()) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            }
        });
    }
</script>
<script>
    const projectCtx = document.getElementById('projectLineChart')?.getContext('2d');
    if (projectCtx) {
        new Chart(projectCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [
                    {
                        label: 'Good Projects',
                        data: {!! json_encode($goodCounts) !!},
                        borderColor: 'rgba(75, 192, 192, 1)',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Bad Projects',
                        data: {!! json_encode($badCounts) !!},
                        borderColor: 'rgba(255, 99, 132, 1)',
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
</script>
@endsection
