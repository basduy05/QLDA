<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectMemberStatsSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected Project $project;
    protected array $stats;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->stats = $project->getMemberStatistics();
    }

    public function collection()
    {
        return collect($this->stats);
    }

    public function headings(): array
    {
        return [
            'Tên thành viên',
            'Email',
            'Tổng số Task',
            'Hoàn thành đúng hạn',
            'Hoàn thành trễ',
            'Đang thực hiện',
            'Quá hạn',
            'Điểm đóng góp',
            'Tỉ lệ đóng góp (%)',
        ];
    }

    public function map($stat): array
    {
        return [
            $stat['user']->name,
            $stat['user']->email,
            $stat['total_tasks'],
            $stat['completed_on_time'],
            $stat['completed_late'],
            $stat['in_progress'],
            $stat['overdue'],
            $stat['score'],
            $stat['contribution_percentage'] . '%',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Thống kê thành viên';
    }
}
