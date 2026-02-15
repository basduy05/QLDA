<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    private array $statuses = ['todo', 'in_progress', 'done'];
    private array $priorities = ['low', 'medium', 'high'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $query = Task::with(['project', 'assignee'])->latest();

        if (!$user->isAdmin()) {
            $query->where(function ($taskQuery) use ($user) {
                $taskQuery->where('assignee_id', $user->id)
                    ->orWhereHas('project', function ($projectQuery) use ($user) {
                        $projectQuery->where('owner_id', $user->id);
                    });
            });
        }

        return view('tasks.index', [
            'tasks' => $query->paginate(10),
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        $this->ensureProjectOwnerAccess($project);

        return view('tasks.create', [
            'project' => $project,
            'users' => User::orderBy('name')->get(),
            'statuses' => $this->statuses,
            'priorities' => $this->priorities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $this->ensureProjectOwnerAccess($project);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', $this->statuses)],
            'priority' => ['required', 'in:' . implode(',', $this->priorities)],
            'due_date' => ['nullable', 'date'],
            'assignee_id' => ['nullable', 'exists:users,id'],
        ]);

        $data['project_id'] = $project->id;
        $data['created_by'] = Auth::id();

        $task = Task::create($data);

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

        $task->load(['project', 'assignee', 'creator']);

        /** @var User $user */
        $user = Auth::user();

        return view('tasks.show', [
            'task' => $task,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->ensureProjectOwnerAccess($task->project);

        return view('tasks.edit', [
            'task' => $task,
            'project' => $task->project,
            'users' => User::orderBy('name')->get(),
            'statuses' => $this->statuses,
            'priorities' => $this->priorities,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->ensureProjectOwnerAccess($task->project);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', $this->statuses)],
            'priority' => ['required', 'in:' . implode(',', $this->priorities)],
            'due_date' => ['nullable', 'date'],
            'assignee_id' => ['nullable', 'exists:users,id'],
        ]);

        $task->update($data);

        return redirect()
            ->route('projects.show', $task->project)
            ->with('status', __('Task updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->ensureProjectOwnerAccess($task->project);

        $task->delete();

        return redirect()
            ->route('projects.show', $task->project)
            ->with('status', __('Task deleted successfully.'));
    }

    private function ensureProjectOwnerAccess(Project $project): void
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isAdmin() && $project->owner_id !== $user->id) {
            abort(403);
        }
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

        if ($task->assignee_id === $user->id) {
            return;
        }

        abort(403);
    }
}
