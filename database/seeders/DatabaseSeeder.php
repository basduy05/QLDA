<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'locale' => 'vi',
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Nguyen Van A',
                'password' => Hash::make('User@123'),
                'role' => 'user',
                'locale' => 'vi',
            ]
        );

        $project = Project::updateOrCreate(
            ['name' => 'Qhorizon PM System'],
            [
                'description' => 'Internal rollout for project management platform.',
                'status' => 'active',
                'start_date' => now()->subWeek(),
                'end_date' => now()->addMonths(2),
                'owner_id' => $admin->id,
            ]
        );

        Task::updateOrCreate(
            ['title' => 'Design onboarding flow', 'project_id' => $project->id],
            [
                'description' => 'Prepare onboarding screens and user guidance.',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => now()->addWeek(),
                'assignee_id' => $user->id,
                'created_by' => $admin->id,
            ]
        );

        Task::updateOrCreate(
            ['title' => 'Setup deployment pipeline', 'project_id' => $project->id],
            [
                'description' => 'Configure shared hosting deployment steps.',
                'status' => 'todo',
                'priority' => 'medium',
                'due_date' => now()->addWeeks(2),
                'assignee_id' => $admin->id,
                'created_by' => $admin->id,
            ]
        );
    }
}
