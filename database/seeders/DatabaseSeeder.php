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
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
            'locale' => 'vi',
        ]);

        $user = User::factory()->create([
            'name' => 'Nguyen Van A',
            'email' => 'user@example.com',
            'password' => Hash::make('User@123'),
            'role' => 'user',
            'locale' => 'vi',
        ]);

        $project = Project::create([
            'name' => 'Qhorizon PM System',
            'description' => 'Internal rollout for project management platform.',
            'status' => 'active',
            'start_date' => now()->subWeek(),
            'end_date' => now()->addMonths(2),
            'owner_id' => $admin->id,
        ]);

        Task::create([
            'project_id' => $project->id,
            'title' => 'Design onboarding flow',
            'description' => 'Prepare onboarding screens and user guidance.',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => now()->addWeek(),
            'assignee_id' => $user->id,
            'created_by' => $admin->id,
        ]);

        Task::create([
            'project_id' => $project->id,
            'title' => 'Setup deployment pipeline',
            'description' => 'Configure shared hosting deployment steps.',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => now()->addWeeks(2),
            'assignee_id' => $admin->id,
            'created_by' => $admin->id,
        ]);
    }
}
