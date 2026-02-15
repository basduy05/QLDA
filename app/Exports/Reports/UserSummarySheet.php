<?php

namespace App\Exports\Reports;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserSummarySheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function __construct(private User $user)
    {
    }

    public function title(): string
    {
        return 'Users';
    }

    public function collection(): Collection
    {
        if ($this->user->isAdmin()) {
            return User::orderBy('name')->get();
        }

        return User::where('id', $this->user->id)->get();
    }

    public function headings(): array
    {
        return [
            'User',
            'Assigned Tasks',
            'Completed Tasks',
            'Overdue Tasks',
        ];
    }

    public function map($user): array
    {
        $tasks = Task::query()->where('assignee_id', $user->id)->get();
        $assigned = $tasks->count();
        $completed = $tasks->where('status', 'done')->count();
        $overdue = $tasks
            ->where('status', '!=', 'done')
            ->filter(fn ($task) => $task->due_date && $task->due_date->lt(Carbon::today()))
            ->count();

        return [
            $user->name,
            $assigned,
            $completed,
            $overdue,
        ];
    }
}
