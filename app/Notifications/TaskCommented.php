<?php

namespace App\Notifications;

use App\Models\TaskComment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskCommented extends Notification
{
    use Queueable;

    public function __construct(private TaskComment $comment, private bool $isMention = false)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->isMention
            ? __('You were mentioned in a task comment')
            : __('New task comment');

        $message = __(':user commented on task :task', [
            'user' => $this->comment->user->name,
            'task' => $this->comment->task->title,
        ]);

        return [
            'task_id' => $this->comment->task_id,
            'task_title' => $this->comment->task->title,
            'project_name' => $this->comment->task->project->name,
            'commenter' => $this->comment->user->name,
            'body' => $this->comment->body,
            'is_mention' => $this->isMention,
            'title' => $title,
            'message' => $message,
            'url' => route('tasks.show', $this->comment->task),
            'type' => 'task-comment',
        ];
    }
}
