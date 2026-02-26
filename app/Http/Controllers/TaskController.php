<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    private array $statuses = ['todo', 'in_progress', 'done'];
    private array $priorities = ['low', 'medium', 'high'];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $query = Task::with(['project.owner', 'project.members', 'assignee'])->latest();

        $manageableProjects = $user->isAdmin()
        ? Project::orderBy('name')->select('id', 'name')->get()
        : Project::where('owner_id', $user->id)
            ->orWhereHas('members', function ($q) use ($user) {
                $q->where('users.id', $user->id)
                  ->whereIn('project_members.role', [Project::ROLE_LEAD, Project::ROLE_DEPUTY]);
            })
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $projectFilterQuery = Project::query()->select('id', 'name')->orderBy('name');

        if (!$user->isAdmin()) {
            $projectFilterQuery->where(function ($projectQuery) use ($user) {
                $projectQuery->where('owner_id', $user->id)
                    ->orWhereHas('members', function ($memberQuery) use ($user) {
                        $memberQuery->where('users.id', $user->id);
                    })
                    ->orWhereHas('tasks', function ($taskQuery) use ($user) {
                        $taskQuery->where('assignee_id', $user->id);
                    });
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($projectQuery) use ($search) {
                      $projectQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', (int) $request->input('project_id'));
        }

        if (!$user->isAdmin()) {
            $query->where(function ($taskQuery) use ($user) {
                $taskQuery->where('assignee_id', $user->id)
                    ->orWhereHas('project', function ($projectQuery) use ($user) {
                        $projectQuery->where('owner_id', $user->id)
                            ->orWhereHas('members', function ($memberQuery) use ($user) {
                                $memberQuery->where('users.id', $user->id);
                            });
                    });
            });
        }

        return view('tasks.index', [
            'tasks' => $query->paginate(15)->withQueryString(),
            'projectsFilter' => $projectFilterQuery->get(),
            'isAdmin' => $user->isAdmin(),
            'manageableProjects' => $manageableProjects,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        $this->ensureTaskManageAccess($project);

        return view('tasks.create', [
            'project' => $project,
            'users' => $this->projectAssignableUsers($project),
            'statuses' => $this->statuses,
            'priorities' => $this->priorities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $this->ensureTaskManageAccess($project);

        $assignableIds = $this->projectAssignableUserIds($project);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', $this->statuses)],
            'priority' => ['required', 'in:' . implode(',', $this->priorities)],
            'due_date' => ['nullable', 'date'],
            'assignee_id' => ['nullable', Rule::in($assignableIds)],
        ]);

        $data['project_id'] = $project->id;
        $data['created_by'] = Auth::id();

        $task = Task::create($data);

        if ($task->assignee_id) {
            $assignee = User::find($task->assignee_id);
            if ($assignee && $assignee->id !== Auth::id()) {
                $assignee->notify(new TaskAssignedNotification($task));
            }
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('status', __('Task created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->ensureTaskViewAccess($task);

        $task->load(['project', 'assignee', 'creator', 'comments.user']);

        /** @var User $user */
        $user = Auth::user();

        return view('tasks.show', [
            'task' => $task,
            'mentionableUsers' => $this->projectAssignableUsers($task->project),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->ensureTaskManageAccess($task->project);

        return view('tasks.edit', [
            'task' => $task,
            'project' => $task->project,
            'users' => $this->projectAssignableUsers($task->project),
            'statuses' => $this->statuses,
            'priorities' => $this->priorities,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->ensureTaskManageAccess($task->project);

        $assignableIds = $this->projectAssignableUserIds($task->project);

        $originalAssigneeId = $task->assignee_id;

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', $this->statuses)],
            'priority' => ['required', 'in:' . implode(',', $this->priorities)],
            'due_date' => ['nullable', 'date'],
            'assignee_id' => ['nullable', Rule::in($assignableIds)],
        ]);

        $task->update($data);

        if ($task->assignee_id && $task->assignee_id !== $originalAssigneeId) {
            $assignee = User::find($task->assignee_id);
            if ($assignee && $assignee->id !== Auth::id()) {
                $assignee->notify(new TaskAssignedNotification($task));
            }
        }

        return redirect()
            ->route('projects.show', $task->project)
            ->with('status', __('Task updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->ensureTaskManageAccess($task->project);

        $task->delete();

        return redirect()
            ->route('projects.show', $task->project)
            ->with('status', __('Task deleted successfully.'));
    }

    private function ensureTaskManageAccess(Project $project): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        if ($project->userHasRole($user, [Project::ROLE_LEAD, Project::ROLE_DEPUTY])) {
            return;
        }

        abort(403);
    }

    private function ensureTaskViewAccess(Task $task): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        if ($task->project->owner_id === $user->id) {
            return;
        }

        if ($task->project->members()->where('users.id', $user->id)->exists()) {
            return;
        }

        if ($task->assignee_id === $user->id) {
            return;
        }

        abort(403);
    }

    private function projectAssignableUserIds(Project $project): array
    {
        $memberIds = $project->members()->pluck('users.id')->map(fn ($id) => (int) $id)->all();

        return collect($memberIds)
            ->push((int) $project->owner_id)
            ->unique()
            ->values()
            ->all();
    }

    private function projectAssignableUsers(Project $project)
    {
        return User::whereIn('id', $this->projectAssignableUserIds($project))
            ->orderBy('name')
            ->get();
    }
}
