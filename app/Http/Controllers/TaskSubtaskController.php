<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskSubtaskController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Task $task)
    {
        $this->ensureAccess($task);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isAdmin = $user->isAdmin();

        $rules = [
            'title' => ['required', 'string', 'max:255'],
        ];

        if ($isAdmin) {
            $rules['points'] = ['nullable', 'numeric', 'min:0.1', 'max:10'];
        }

        $validated = $request->validate($rules);

        $task->subtasks()->create([
            'title' => $validated['title'],
            'points' => $isAdmin ? ($validated['points'] ?? 0.2) : 0.2,
            'is_completed' => false,
        ]);

        return back()->with('status', 'Subtask added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subtask $subtask)
    {
        $this->ensureAccess($subtask->task);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isAdmin = $user->isAdmin();

        $rules = [
            'is_completed' => ['nullable', 'boolean'],
            'title' => ['nullable', 'string', 'max:255'],
        ];

        if ($isAdmin) {
             $rules['points'] = ['nullable', 'numeric', 'min:0.1', 'max:10'];
        }

        $validated = $request->validate($rules);

        $subtask->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('status', 'Subtask updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subtask $subtask)
    {
        $this->ensureAccess($subtask->task);

        $subtask->delete();

        return back()->with('status', 'Subtask deleted.');
    }

    private function ensureAccess(Task $task): void
    {
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

        abort(403);
    }
}
