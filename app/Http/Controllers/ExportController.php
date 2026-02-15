<?php

namespace App\Http\Controllers;

use App\Exports\ProjectsExport;
use App\Exports\ReportsExport;
use App\Exports\TasksExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function projects()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return Excel::download(new ProjectsExport($user), 'projects.xlsx');
    }

    public function tasks()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return Excel::download(new TasksExport($user), 'tasks.xlsx');
    }

    public function reports()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return Excel::download(new ReportsExport($user), 'reports.xlsx');
    }
}
