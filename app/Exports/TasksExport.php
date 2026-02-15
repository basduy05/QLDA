<?php

namespace App\Exports;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TasksExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private User $user)
    {
    }

    public function collection(): Collection
    {
        $query = Task::with(['project', 'assignee'])->latest();

        if (!$this->user->isAdmin()) {
            $query->where(function ($taskQuery) {
                $taskQuery->where('assignee_id', $this->user->id)
                    ->orWhereHas('project', function ($projectQuery) {
                        $projectQuery->where('owner_id', $this->user->id);
                    });
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Project',
            'Assignee',
            'Status',
            'Priority',
            'Due Date',
            'Created At',
        ];
    }

    public function map($task): array
    {
        return [
            $task->id,
            $task->title,
            $task->project?->name,
            $task->assignee?->name,
            $task->status,
            $task->priority,
            optional($task->due_date)->format('Y-m-d'),
            $task->created_at?->format('Y-m-d H:i'),
        ];
    }
}
