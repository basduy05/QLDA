<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public const ROLE_LEAD = 'lead';
    public const ROLE_DEPUTY = 'deputy';
    public const ROLE_MEMBER = 'member';

    protected $fillable = [
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'owner_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public static function roles(): array
    {
        return [self::ROLE_LEAD, self::ROLE_DEPUTY, self::ROLE_MEMBER];
    }

    public function roleForUser(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        if ((int) $this->owner_id === (int) $user->id) {
            return self::ROLE_LEAD;
        }

        $member = $this->members->firstWhere('id', $user->id);

        return $member?->pivot?->role;
    }

    public function userHasRole(?User $user, array $roles): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $role = $this->roleForUser($user);

        return $role !== null && in_array($role, $roles, true);
    }

    public function getMemberStatistics(): array
    {
        $stats = [];
        $tasks = $this->tasks()->get();

        // Initialize stats for all members
        foreach ($this->members as $member) {
            $stats[$member->id] = [
                'user' => $member,
                'total_tasks' => 0,
                'completed_on_time' => 0,
                'completed_late' => 0,
                'in_progress' => 0,
                'overdue' => 0,
                'score' => 0,
                'contribution_percentage' => 0,
            ];
        }

        // Also include owner if not in members
        if (!isset($stats[$this->owner_id]) && $this->owner) {
            $stats[$this->owner_id] = [
                'user' => $this->owner,
                'total_tasks' => 0,
                'completed_on_time' => 0,
                'completed_late' => 0,
                'in_progress' => 0,
                'overdue' => 0,
                'score' => 0,
                'contribution_percentage' => 0,
            ];
        }

        $totalProjectScore = 0;

        foreach ($tasks as $task) {
            if (!$task->assignee_id || !isset($stats[$task->assignee_id])) {
                continue;
            }

            $assigneeId = $task->assignee_id;
            $stats[$assigneeId]['total_tasks']++;

            $isDone = $task->status === 'done';
            $dueDate = $task->due_date ? $task->due_date->endOfDay() : null;
            $updatedAt = $task->updated_at;

            if ($isDone) {
                if ($dueDate && $updatedAt->gt($dueDate)) {
                    $stats[$assigneeId]['completed_late']++;
                    $stats[$assigneeId]['score'] += 5; // 5 points for late completion
                    $totalProjectScore += 5;
                } else {
                    $stats[$assigneeId]['completed_on_time']++;
                    $stats[$assigneeId]['score'] += 10; // 10 points for on-time completion
                    $totalProjectScore += 10;
                }
            } else {
                if ($dueDate && now()->gt($dueDate)) {
                    $stats[$assigneeId]['overdue']++;
                    // 0 points for overdue
                } else {
                    $stats[$assigneeId]['in_progress']++;
                    $stats[$assigneeId]['score'] += 2; // 2 points for in-progress/todo
                    $totalProjectScore += 2;
                }
            }
        }

        // Calculate percentages
        foreach ($stats as &$stat) {
            if ($totalProjectScore > 0) {
                $stat['contribution_percentage'] = round(($stat['score'] / $totalProjectScore) * 100, 1);
            }
        }

        // Sort by contribution percentage descending
        usort($stats, function ($a, $b) {
            return $b['contribution_percentage'] <=> $a['contribution_percentage'];
        });

        return $stats;
    }
}
