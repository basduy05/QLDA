<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    private array $statuses = ['planning', 'active', 'on_hold', 'completed'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $query = Project::with('owner')->withCount('tasks')->latest();

        if (!$user->isAdmin()) {
            $query->where('owner_id', $user->id);
        }

        return view('projects.index', [
            'projects' => $query->paginate(10),
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /** @var User $user */
        $user = Auth::user();

        return view('projects.create', [
            'owners' => $user->isAdmin() ? User::orderBy('name')->get() : collect([$user]),
            'statuses' => $this->statuses,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', $this->statuses)],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];

        if ($user->isAdmin()) {
            $rules['owner_id'] = ['required', 'exists:users,id'];
        }

        $data = $request->validate($rules);

        if (!$user->isAdmin()) {
            $data['owner_id'] = $user->id;
        }

        $project = Project::create($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', __('Project created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->ensureAccess($project);

        $project->load(['owner', 'tasks.assignee']);

        /** @var User $user */
        $user = Auth::user();

        return view('projects.show', [
            'project' => $project,
            'tasks' => $project->tasks()->latest()->get(),
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $this->ensureAccess($project);

        /** @var User $user */
        $user = Auth::user();

        return view('projects.edit', [
            'project' => $project,
            'owners' => $user->isAdmin() ? User::orderBy('name')->get() : collect([$user]),
            'statuses' => $this->statuses,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->ensureAccess($project);

        /** @var User $user */
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', $this->statuses)],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];

        if ($user->isAdmin()) {
            $rules['owner_id'] = ['required', 'exists:users,id'];
        }

        $data = $request->validate($rules);

        if (!$user->isAdmin()) {
            $data['owner_id'] = $project->owner_id;
        }

        $project->update($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', __('Project updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->ensureAccess($project);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('status', __('Project deleted successfully.'));
    }

    private function ensureAccess(Project $project): void
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isAdmin() && $project->owner_id !== $user->id) {
            abort(403);
        }
    }
}
