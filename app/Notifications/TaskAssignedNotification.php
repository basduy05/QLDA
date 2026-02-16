<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Task $task)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('New task assigned'),
            'message' => __('You were assigned to task: :title', ['title' => $this->task->title]),
            'url' => route('tasks.show', $this->task),
            'task_id' => $this->task->id,
            'type' => 'task',
        ];
    }
}
