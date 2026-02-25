<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectTasksSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function collection()
    {
        return $this->project->tasks()->with(['assignee', 'creator'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên Task',
            'Mô tả',
            'Trạng thái',
            'Độ ưu tiên',
            'Người được giao',
            'Người tạo',
            'Hạn chót',
            'Ngày tạo',
            'Ngày cập nhật cuối',
            'Đánh giá tiến độ',
        ];
    }

    public function map($task): array
    {
        $statusLabels = [
            'todo' => 'Cần làm',
            'in_progress' => 'Đang thực hiện',
            'review' => 'Chờ duyệt',
            'done' => 'Hoàn thành',
        ];

        $priorityLabels = [
            'low' => 'Thấp',
            'medium' => 'Trung bình',
            'high' => 'Cao',
            'urgent' => 'Khẩn cấp',
        ];

        $progressEvaluation = 'Đang thực hiện';
        $isDone = $task->status === 'done';
        $dueDate = $task->due_date ? $task->due_date->endOfDay() : null;
        $updatedAt = $task->updated_at;

        if ($isDone) {
            if ($dueDate && $updatedAt->gt($dueDate)) {
                $progressEvaluation = 'Hoàn thành trễ';
            } else {
                $progressEvaluation = 'Hoàn thành đúng hạn';
            }
        } else {
            if ($dueDate && now()->gt($dueDate)) {
                $progressEvaluation = 'Quá hạn';
            }
        }

        return [
            $task->id,
            $task->title,
            $task->description,
            $statusLabels[$task->status] ?? $task->status,
            $priorityLabels[$task->priority] ?? $task->priority,
            $task->assignee ? $task->assignee->name : 'Chưa giao',
            $task->creator ? $task->creator->name : '',
            $task->due_date ? $task->due_date->format('d/m/Y') : 'Không có',
            $task->created_at->format('d/m/Y H:i'),
            $task->updated_at->format('d/m/Y H:i'),
            $progressEvaluation,
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
        return 'Chi tiết Task';
    }
}
