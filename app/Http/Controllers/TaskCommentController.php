<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use App\Notifications\TaskCommented;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class TaskCommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->ensureTaskViewAccess($task);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'body' => $data['body'],
        ]);

        $comment->load(['task.project', 'user']);

        $mentionedUsers = $this->resolveMentions($data['body']);
        $comment->update([
            'mentions' => $mentionedUsers->pluck('id')->values()->all(),
        ]);

        $mentionedRecipients = $mentionedUsers
            ->reject(fn (User $mentioned) => $mentioned->id === $user->id)
            ->values();

        $baseRecipients = collect([
            $task->project->owner,
            $task->assignee,
        ])
            ->merge(User::where('role', 'admin')->get())
            ->filter()
            ->unique('id')
            ->reject(fn (User $recipient) => $recipient->id === $user->id)
            ->reject(fn (User $recipient) => $mentionedRecipients->contains('id', $recipient->id))
            ->values();

        if ($mentionedRecipients->isNotEmpty()) {
            Notification::send($mentionedRecipients, new TaskCommented($comment, true));
        }

        if ($baseRecipients->isNotEmpty()) {
            Notification::send($baseRecipients, new TaskCommented($comment, false));
        }

        return redirect()
            ->route('tasks.show', $task)
            ->with('status', __('Comment added.'));
    }

    private function resolveMentions(string $body)
    {
        preg_match_all('/@([\p{L}\p{N}_.-]+)/u', $body, $matches);

        $names = collect($matches[1] ?? [])
            ->map(fn (string $name) => mb_strtolower($name))
            ->unique()
            ->values();

        if ($names->isEmpty()) {
            return collect();
        }

        return User::query()
            ->whereIn(DB::raw('LOWER(name)'), $names->all())
            ->get();
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
