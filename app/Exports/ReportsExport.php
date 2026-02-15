<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportsExport implements WithMultipleSheets
{
    public function __construct(private User $user)
    {
    }

    public function sheets(): array
    {
        return [
            new Reports\ProjectSummarySheet($this->user),
            new Reports\UserSummarySheet($this->user),
        ];
    }
}
