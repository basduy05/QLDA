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
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $query = Project::with(['owner', 'members'])->withCount('tasks')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!$user->isAdmin()) {
            $query->where(function ($projectQuery) use ($user) {
                $projectQuery->where('owner_id', '>=', $user->id)
                    ->orWhereHas('members', function ($memberQuery) use ($user) {
                        $memberQuery->where('users.id', $user->id);
                    })
                    ->orWhereHas('tasks', function ($taskQuery) use ($user) {
                        $taskQuery->where('assignee_id', $user->id);
                    });
            });
        }

        return view('projects.index', [
            'projects' => $query->paginate(10)->withQueryString(),
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
        $this->synchronizeOwnerLeadRole($project);

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

        $project->load(['owner', 'members', 'tasks.assignee']);
        $project->loadCount('tasks');

        /** @var User $user */
        $user = Auth::user();
        $role = $project->roleForUser($user);
        $canUpdateProject = $this->canUpdateProject($project, $user);
        $canManageMembers = $this->canManageMembers($project, $user);
        $canManageTasks = $this->canManageTasks($project, $user);
        $memberIds = $project->members->pluck('id')->push((int) $project->owner_id)->unique()->all();
        $availableUsers = User::whereNotIn('id', $memberIds)->orderBy('name')->get();

        return view('projects.show', [
            'project' => $project,
            'tasks' => $project->tasks()->latest()->get(),
            'isAdmin' => $user->isAdmin(),
            'viewerRole' => $role,
            'canUpdateProject' => $canUpdateProject,
            'canManageMembers' => $canManageMembers,
            'canManageTasks' => $canManageTasks,
            'availableUsers' => $availableUsers,
            'projectRoles' => Project::roles(),
            'memberStats' => $project->getMemberStatistics(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $this->ensureProjectUpdateAccess($project);

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
        $this->ensureProjectUpdateAccess($project);

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
        $this->synchronizeOwnerLeadRole($project);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', __('Project updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->ensureLeadAccess($project);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('status', __('Project deleted successfully.'));
    }

    public function addMember(Request $request, Project $project)
    {
        $this->ensureLeadAccess($project);

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role' => ['required', 'in:' . implode(',', [Project::ROLE_DEPUTY, Project::ROLE_MEMBER])],
        ]);

        $userId = (int) $data['user_id'];
        if ((int) $project->owner_id === $userId) {
            return back()->withErrors(['user_id' => __('Project owner is already lead.')]);
        }

        $project->members()->syncWithoutDetaching([
            $userId => ['role' => $data['role']],
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', __('Project member added successfully.'));
    }

    public function updateMemberRole(Request $request, Project $project, User $member)
    {
        $this->ensureLeadAccess($project);

        if ((int) $project->owner_id === (int) $member->id) {
            return back()->withErrors(['member' => __('Project owner must remain lead.')]);
        }

        if (! $project->members()->where('users.id', $member->id)->exists()) {
            abort(404);
        }

        $data = $request->validate([
            'role' => ['required', 'in:' . implode(',', [Project::ROLE_DEPUTY, Project::ROLE_MEMBER])],
        ]);

        $project->members()->updateExistingPivot($member->id, [
            'role' => $data['role'],
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', __('Project member role updated.'));
    }

    public function removeMember(Project $project, User $member)
    {
        $this->ensureLeadAccess($project);

        if ((int) $project->owner_id === (int) $member->id) {
            return back()->withErrors(['member' => __('Project owner cannot be removed.')]);
        }

        $project->members()->detach($member->id);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', __('Project member removed.'));
    }

    private function synchronizeOwnerLeadRole(Project $project): void
    {
        $project->members()->syncWithoutDetaching([
            $project->owner_id => ['role' => Project::ROLE_LEAD],
        ]);

        $otherLeads = $project->members()
            ->wherePivot('role', Project::ROLE_LEAD)
            ->where('users.id', '!=', $project->owner_id)
            ->pluck('users.id');

        foreach ($otherLeads as $otherLeadId) {
            $project->members()->updateExistingPivot($otherLeadId, [
                'role' => Project::ROLE_DEPUTY,
            ]);
        }
    }
    public function exportReport(Project $project)
    {
        $this->ensureAccess($project);
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ProjectReportExport($project),
            'project_report_' . $project->id . '_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
    private function canUpdateProject(Project $project, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $project->userHasRole($user, [Project::ROLE_LEAD, Project::ROLE_DEPUTY]);
    }

    private function canManageMembers(Project $project, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $project->userHasRole($user, [Project::ROLE_LEAD]);
    }

    private function canManageTasks(Project $project, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $project->userHasRole($user, [Project::ROLE_LEAD, Project::ROLE_DEPUTY]);
    }

    private function ensureProjectUpdateAccess(Project $project): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($this->canUpdateProject($project, $user)) {
            return;
        }

        abort(403);
    }

    private function ensureLeadAccess(Project $project): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($this->canManageMembers($project, $user)) {
            return;
        }

        abort(403);
    }

    private function ensureAccess(Project $project): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        if ($project->owner_id === $user->id) {
            return;
        }

        if ($project->members()->where('users.id', $user->id)->exists()) {
            return;
        }

        if ($project->tasks()->where('assignee_id', $user->id)->exists()) {
            return;
        }

        abort(403);
    }
}
