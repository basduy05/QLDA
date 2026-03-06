<?php

namespace App\Notifications;

use App\Mail\TaskNotificationMail;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        $data = [
            'title' => __('New task assigned'),
            'message' => __('You were assigned to task: :title', ['title' => $this->task->title]),
            'url' => route('tasks.show', $this->task),
            'task_id' => $this->task->id,
            'type' => 'task',
        ];

        // Fire-and-forget email
        $this->sendEmail($notifiable, $data);

        return $data;
    }

    private function sendEmail(object $notifiable, array $data): void
    {
        try {
            $project = $this->task->project;
            $priority = match ($this->task->priority) {
                'high' => '🔴 ' . __('High'),
                'medium' => '🟡 ' . __('Medium'),
                default => '🟢 ' . __('Low'),
            };
            $due = $this->task->due_date
                ? $this->task->due_date->format('d/m/Y')
                : __('No due date');

            $body = __('Hello :name,', ['name' => $notifiable->name]) . "\n\n"
                . __('You have been assigned a new task:') . "\n\n"
                . __('Project') . ': ' . ($project->name ?? '—') . "\n"
                . __('Task') . ': ' . $this->task->title . "\n"
                . __('Priority') . ': ' . $priority . "\n"
                . __('Due date') . ': ' . $due;

            if ($this->task->description) {
                $body .= "\n" . __('Description') . ': ' . $this->task->description;
            }

            Mail::to($notifiable->email)->send(new TaskNotificationMail(
                title: __('New task assigned: :title', ['title' => $this->task->title]),
                body: $body,
                actionUrl: $data['url'],
                actionLabel: __('View task'),
            ));
        } catch (\Throwable $e) {
            Log::warning('Task assigned email failed', [
                'user_id' => $notifiable->id ?? null,
                'task_id' => $this->task->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
