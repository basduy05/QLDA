<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProjectReportExport implements WithMultipleSheets
{
    use Exportable;

    protected Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function sheets(): array
    {
        return [
            new ProjectMemberStatsSheet($this->project),
            new ProjectTasksSheet($this->project),
        ];
    }
}
