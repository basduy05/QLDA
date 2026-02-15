<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

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

        return view('dashboard', [
            'projectsCount' => $projectsCount,
            'activeProjectsCount' => $activeProjectsCount,
            'tasksCount' => $tasksCount,
            'openTasksCount' => $openTasksCount,
            'recentProjects' => $recentProjects,
            'upcomingTasks' => $upcomingTasks,
            'overdueTasks' => $overdueTasks,
            'isAdmin' => $user->isAdmin(),
        ]);
    }
}
