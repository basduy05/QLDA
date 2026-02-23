<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900">{{ __('Dashboard') }}</h1>
    </x-slot>

    <div class="space-y-6">
        <!-- KPI Cards -->
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-5">
            <div class="bg-white border border-slate-200 rounded-lg p-4">
                <p class="text-sm font-medium text-slate-500">{{ __('Total projects') }}</p>
                <p class="mt-1 text-3xl font-bold text-slate-900">{{ $projectsCount }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-4">
                <p class="text-sm font-medium text-slate-500">{{ __('Active projects') }}</p>
                <p class="mt-1 text-3xl font-bold text-slate-900">{{ $activeProjectsCount }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-4">
                <p class="text-sm font-medium text-slate-500">{{ __('Tasks') }}</p>
                <p class="mt-1 text-3xl font-bold text-slate-900">{{ $tasksCount }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-4">
                <p class="text-sm font-medium text-slate-500">{{ __('Open tasks') }}</p>
                <p class="mt-1 text-3xl font-bold text-slate-900">{{ $openTasksCount }}</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4">
                <p class="text-sm font-medium text-slate-500">{{ __('Completion rate') }}</p>
                <p class="mt-1 text-3xl font-bold text-blue-900">{{ $completionRate }}%</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">{{ __('Workload trend (6 months)') }}</h3>
                <canvas id="tasks-trend-chart" height="100"></canvas>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">{{ __('Task status') }}</h3>
                <canvas id="task-status-chart" height="250"></canvas>
            </div>
        </div>

        <!-- Recent Projects & Upcoming Tasks -->
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900">{{ __('Recent projects') }}</h3>
                    <a href="{{ route('projects.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">{{ __('View all') }}</a>
                </div>
                <div class="divide-y divide-slate-200">
                    @forelse ($recentProjects as $project)
                        <a href="{{ route('projects.show', $project) }}" class="py-3 flex items-center justify-between hover:bg-slate-50 group">
                            <div>
                                <p class="font-semibold text-slate-900 group-hover:text-blue-600">{{ $project->name }}</p>
                                <p class="text-sm text-slate-500">{{ $project->owner?->name }}</p>
                            </div>
                            <span class="text-xs font-medium px-2 py-1 rounded bg-slate-100 text-slate-700">{{ __($project->status) }}</span>
                        </a>
                    @empty
                        <p class="py-4 text-center text-slate-500">{{ __('No projects yet.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">{{ __('Upcoming tasks') }}</h3>
                <div class="divide-y divide-slate-200">
                    @forelse ($upcomingTasks as $task)
                        <a href="{{ route('tasks.show', $task) }}" class="py-3 flex items-center justify-between hover:bg-slate-50 group">
                            <div>
                                <p class="font-semibold text-slate-900 group-hover:text-blue-600 truncate">{{ $task->title }}</p>
                                <p class="text-xs text-slate-500">{{ $task->project->name }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="py-4 text-center text-slate-500">{{ __('No upcoming tasks.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        // Store data as window variables
        window.tasksTrendData = <?php echo json_encode($tasksTrend); ?>;
        window.taskStatusData = <?php echo json_encode($taskStatusDistribution); ?>;
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tasks Trend Chart
            const trendCtx = document.getElementById('tasks-trend-chart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: window.tasksTrendData.labels,
                        datasets: [
                            {
                                label: '{{ __("Created") }}',
                                data: window.tasksTrendData.created,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true,
                            },
                            {
                                label: '{{ __("Completed") }}',
                                data: window.tasksTrendData.completed,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            }
                        }
                    }
                });
            }

            // Task Status Chart
            const statusCtx = document.getElementById('task-status-chart');
            if (statusCtx) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: window.taskStatusData.labels,
                        datasets: [{
                            data: window.taskStatusData.values,
                            backgroundColor: ['#94a3b8', '#3b82f6', '#10b981'],
                            borderColor: '#ffffff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
