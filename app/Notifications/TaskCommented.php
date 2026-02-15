<?php

namespace App\Notifications;

use App\Models\TaskComment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCommented extends Notification
{
    use Queueable;

    public function __construct(private TaskComment $comment, private bool $isMention = false)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $task = $this->comment->task;
        $project = $task->project;
        $subject = $this->isMention
            ? __('You were mentioned in a task comment')
            : __('New task comment');

        return (new MailMessage())
            ->subject($subject)
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line(__(':user commented on task :task', [
                'user' => $this->comment->user->name,
                'task' => $task->title,
            ]))
            ->line(__(':project project', ['project' => $project->name]))
            ->line($this->comment->body)
            ->action(__('View task'), url(route('tasks.show', $task)));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->comment->task_id,
            'task_title' => $this->comment->task->title,
            'project_name' => $this->comment->task->project->name,
            'commenter' => $this->comment->user->name,
            'body' => $this->comment->body,
            'is_mention' => $this->isMention,
        ];
    }
}
