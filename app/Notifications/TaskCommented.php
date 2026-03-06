<?php

namespace App\Notifications;

use App\Mail\TaskNotificationMail;
use App\Models\TaskComment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        $data = [
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

        // Fire-and-forget email
        $this->sendEmail($notifiable, $data);

        return $data;
    }

    private function sendEmail(object $notifiable, array $data): void
    {
        try {
            $body = __('Hello :name,', ['name' => $notifiable->name]) . "\n\n";

            if ($this->isMention) {
                $body .= __(':user mentioned you in a comment on task ":task":', [
                    'user' => $this->comment->user->name,
                    'task' => $this->comment->task->title,
                ]);
            } else {
                $body .= __(':user commented on task ":task":', [
                    'user' => $this->comment->user->name,
                    'task' => $this->comment->task->title,
                ]);
            }

            $body .= "\n\n" . '"' . $this->comment->body . '"';
            $body .= "\n\n" . __('Project') . ': ' . ($this->comment->task->project->name ?? '—');

            Mail::to($notifiable->email)->send(new TaskNotificationMail(
                title: $data['title'] . ': ' . $this->comment->task->title,
                body: $body,
                actionUrl: $data['url'],
                actionLabel: __('View comment'),
            ));
        } catch (\Throwable $e) {
            Log::warning('Task comment email failed', [
                'user_id' => $notifiable->id ?? null,
                'comment_id' => $this->comment->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
