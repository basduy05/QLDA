<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private User $user)
    {
    }

    public function collection(): Collection
    {
        $query = Project::with('owner')->latest();

        if (!$this->user->isAdmin()) {
            $query->where('owner_id', $this->user->id);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Status',
            'Owner',
            'Start Date',
            'End Date',
            'Created At',
        ];
    }

    public function map($project): array
    {
        return [
            $project->id,
            $project->name,
            $project->status,
            $project->owner?->name,
            optional($project->start_date)->format('Y-m-d'),
            optional($project->end_date)->format('Y-m-d'),
            $project->created_at?->format('Y-m-d H:i'),
        ];
    }
}
