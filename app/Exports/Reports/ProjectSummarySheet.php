<?php

namespace App\Exports\Reports;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProjectSummarySheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function __construct(private User $user)
    {
    }

    public function title(): string
    {
        return 'Projects';
    }

    public function collection(): Collection
    {
        $query = Project::with('owner', 'tasks');

        if (!$this->user->isAdmin()) {
            $query->where('owner_id', $this->user->id);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Project',
            'Owner',
            'Status',
            'Total Tasks',
            'Completed Tasks',
            'Overdue Tasks',
        ];
    }

    public function map($project): array
    {
        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'done')->count();
        $overdueTasks = $project->tasks
            ->where('status', '!=', 'done')
            ->filter(fn ($task) => $task->due_date && $task->due_date->lt(Carbon::today()))
            ->count();

        return [
            $project->name,
            $project->owner?->name,
            $project->status,
            $totalTasks,
            $completedTasks,
            $overdueTasks,
        ];
    }
}
