<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $projectQuery = Project::query();
        $taskQuery = Task::query();

        if (!$user->isAdmin()) {
            $projectQuery->where(function (Builder $query) use ($user) {
                $query->where('owner_id', $user->id)
                    ->orWhereHas('tasks', function (Builder $taskQuery) use ($user) {
                        $taskQuery->where('assignee_id', $user->id);
                    });
            });

            $taskQuery->where(function ($query) use ($user) {
                $query->where('assignee_id', $user->id)
                    ->orWhereHas('project', function ($projectQuery) use ($user) {
                        $projectQuery->where('owner_id', $user->id);
                    });
            });
        }

        $projectsCount = (clone $projectQuery)->distinct('projects.id')->count('projects.id');
        $activeProjectsCount = (clone $projectQuery)->where('status', 'active')->distinct('projects.id')->count('projects.id');
        $tasksCount = (clone $taskQuery)->distinct('tasks.id')->count('tasks.id');
        $openTasksCount = (clone $taskQuery)->where('status', '!=', 'done')->distinct('tasks.id')->count('tasks.id');

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

        $projectStatusRaw = (clone $projectQuery)
            ->selectRaw("COALESCE(status, 'unknown') as status_key, COUNT(DISTINCT projects.id) as total")
            ->groupBy('status_key')
            ->pluck('total', 'status_key');

        $taskStatusRaw = (clone $taskQuery)
            ->selectRaw("COALESCE(status, 'unknown') as status_key, COUNT(DISTINCT tasks.id) as total")
            ->groupBy('status_key')
            ->pluck('total', 'status_key');

        $projectPreferredOrder = ['planning', 'active', 'on_hold', 'completed'];
        $projectStatusData = collect($projectPreferredOrder)
            ->filter(fn (string $status) => $projectStatusRaw->has($status))
            ->concat($projectStatusRaw->keys()->reject(fn (string $key) => in_array($key, $projectPreferredOrder, true)))
            ->values()
            ->map(fn (string $status) => [
                'label' => Str::headline($status),
                'value' => (int) ($projectStatusRaw[$status] ?? 0),
            ]);

        $taskPreferredOrder = ['todo', 'in_progress', 'done'];
        $taskStatusData = collect($taskPreferredOrder)
            ->filter(fn (string $status) => $taskStatusRaw->has($status))
            ->concat($taskStatusRaw->keys()->reject(fn (string $key) => in_array($key, $taskPreferredOrder, true)))
            ->values()
            ->map(fn (string $status) => [
                'label' => Str::headline($status),
                'value' => (int) ($taskStatusRaw[$status] ?? 0),
            ]);

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

        $doneTasksCount = (clone $taskQuery)->where('status', 'done')->distinct('tasks.id')->count('tasks.id');
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
