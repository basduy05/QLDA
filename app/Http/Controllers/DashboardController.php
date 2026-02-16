<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $projectQuery = Project::query();
        $taskQuery = Task::query();

        if (!$user->isAdmin()) {
            $projectQuery->where('owner_id', $user->id);
            $taskQuery->where(function ($query) use ($user) {
                $query->where('assignee_id', $user->id)
                    ->orWhereHas('project', function ($projectQuery) use ($user) {
                        $projectQuery->where('owner_id', $user->id);
                    });
            });
        }

        $projectsCount = (clone $projectQuery)->count();
        $activeProjectsCount = (clone $projectQuery)->where('status', 'active')->count();
        $tasksCount = (clone $taskQuery)->count();
        $openTasksCount = (clone $taskQuery)->where('status', '!=', 'done')->count();

        $recentProjects = $projectQuery->with('owner')
            ->latest()
            ->limit(5)
            ->get();

        $upcomingTasks = $taskQuery->with(['project', 'assignee'])
            ->whereNotNull('due_date')
            ->where('status', '!=', 'done')
            ->orderBy('due_date')
            ->limit(6)
            ->get();

        $overdueTasks = $taskQuery->with(['project', 'assignee'])
            ->whereNotNull('due_date')
            ->where('status', '!=', 'done')
            ->whereDate('due_date', '<', Carbon::today())
            ->orderBy('due_date')
            ->limit(6)
            ->get();

        $projectStatuses = ['planning', 'active', 'on_hold', 'completed'];
        $projectStatusData = collect($projectStatuses)
            ->map(function (string $status) use ($projectQuery) {
                return [
                    'label' => Str::headline($status),
                    'value' => (clone $projectQuery)->where('status', $status)->count(),
                ];
            })
            ->values();

        $taskStatuses = ['todo', 'in_progress', 'done'];
        $taskStatusData = collect($taskStatuses)
            ->map(function (string $status) use ($taskQuery) {
                return [
                    'label' => Str::headline($status),
                    'value' => (clone $taskQuery)->where('status', $status)->count(),
                ];
            })
            ->values();

        $months = collect(range(5, 0, -1))
            ->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset))
            ->push(now()->startOfMonth())
            ->values();

        $taskTrendData = $months->map(function (Carbon $month) use ($taskQuery) {
            $start = $month->copy();
            $end = $month->copy()->endOfMonth();

            return [
                'label' => $month->translatedFormat('M'),
                'created' => (clone $taskQuery)
                    ->whereBetween('created_at', [$start, $end])
                    ->count(),
                'completed' => (clone $taskQuery)
                    ->where('status', 'done')
                    ->whereBetween('updated_at', [$start, $end])
                    ->count(),
            ];
        })->values();

        $doneTasksCount = (clone $taskQuery)->where('status', 'done')->count();
        $completionRate = $tasksCount > 0
            ? (int) round(($doneTasksCount / $tasksCount) * 100)
            : 0;

        return view('dashboard', [
            'projectsCount' => $projectsCount,
            'activeProjectsCount' => $activeProjectsCount,
            'tasksCount' => $tasksCount,
            'openTasksCount' => $openTasksCount,
            'doneTasksCount' => $doneTasksCount,
            'completionRate' => $completionRate,
            'recentProjects' => $recentProjects,
            'upcomingTasks' => $upcomingTasks,
            'overdueTasks' => $overdueTasks,
            'projectStatusData' => $projectStatusData,
            'taskStatusData' => $taskStatusData,
            'taskTrendData' => $taskTrendData,
            'isAdmin' => $user->isAdmin(),
        ]);
    }
}
