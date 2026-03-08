<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-slate-900">{{ __('Dashboard') }}</h2>
            <p class="text-sm text-slate-500 mt-0.5">{{ __('A quick pulse of project health and delivery rhythm.') }}
            </p>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- KPI Cards --}}
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="card-strong dashboard-kpi p-5 group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                        style="background: var(--accent-soft);">
                        <svg class="w-5 h-5" style="color: var(--accent);" fill="none" stroke="currentColor"
                            stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('Total') }}</span>
                </div>
                <p class="text-3xl font-bold text-slate-900">{{ $projectsCount }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ __('Total projects') }}</p>
            </div>

            <div class="card-strong dashboard-kpi p-5 group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-50">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.75"
                            viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-emerald-500 uppercase tracking-wider">●
                        {{ __('Live') }}</span>
                </div>
                <p class="text-3xl font-bold text-slate-900">{{ $activeProjectsCount }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ __('Active projects') }}</p>
            </div>

            <div class="card-strong dashboard-kpi p-5 group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-sky-50">
                        <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" stroke-width="1.75"
                            viewBox="0 0 24 24">
                            <path d="M9 11 12 14 22 4" />
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('Tasks') }}</span>
                </div>
                <p class="text-3xl font-bold text-slate-900">{{ $tasksCount }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ __('Tasks in view') }}</p>
            </div>

            <div class="card-strong dashboard-kpi p-5 group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-amber-50">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.75"
                            viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-amber-500 uppercase tracking-wider">{{ __('Open') }}</span>
                </div>
                <p class="text-3xl font-bold text-slate-900">{{ $openTasksCount }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ __('Open tasks') }}</p>
            </div>

            <div class="card-strong dashboard-kpi p-5 group"
                style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.04) 0%, rgba(139, 92, 246, 0.06) 100%);">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                        style="background: var(--accent-soft);">
                        <svg class="w-5 h-5" style="color: var(--accent);" fill="none" stroke="currentColor"
                            stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold" style="color: var(--accent);">{{ $completionRate }}%</span>
                </div>
                <div class="flex items-center gap-3">
                    <div id="completion-ring" data-rate="{{ $completionRate }}"
                        class="h-12 w-12 rounded-full grid place-items-center text-sm font-bold"
                        style="background: conic-gradient(var(--accent) {{ $completionRate }}%, #e2e8f0 {{ $completionRate }}% 100%);">
                        <span
                            class="h-8 w-8 rounded-full bg-white grid place-items-center text-xs font-bold text-slate-700">{{ $doneTasksCount }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ __('Completion') }}</p>
                        <p class="text-xs text-slate-500">{{ __('completed tasks') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- AI Smart Search --}}
        <div class="card-strong p-5" x-data="aiSmartSearch()">
            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="color: var(--accent);"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M12 2a4 4 0 0 0-4 4c0 2 2 3 2 6H8a2 2 0 0 0-2 2v1h12v-1a2 2 0 0 0-2-2h-2c0-3 2-4 2-6a4 4 0 0 0-4-4z" />
                            <path d="M10 18h4" />
                            <path d="M10 22h4" />
                        </svg>
                    </div>
                    <input type="text" x-model="query" @keydown.enter="search()"
                        class="w-full pl-12 pr-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 placeholder-slate-400"
                        placeholder="{{ __('AI Smart Search — e.g. "overdue tasks in Project X" or "high priority assigned to John"') }}">
                </div>
                <button @click="search()" :disabled="loading"
                    class="shrink-0 btn-primary !rounded-xl !px-6 !py-3 disabled:opacity-50">
                    <template x-if="loading">
                        <span
                            class="animate-spin inline-block h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                    </template>
                    <template x-if="!loading">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </template>
                    {{ __('Search') }}
                </button>
            </div>

            {{-- Parsed filters --}}
            <div x-show="filters" x-cloak class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                <span class="text-slate-500">{{ __('AI parsed:') }}</span>
                <template x-for="(v, k) in filters" :key="k">
                    <template x-if="v && v !== false && v !== 'null'">
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-indigo-700 border border-indigo-200"
                            style="background: var(--accent-soft);" x-text="k + ': ' + v"></span>
                    </template>
                </template>
            </div>

            {{-- Results --}}
            <div x-show="results.length > 0" x-cloak class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-slate-700"
                        x-text="'{{ __('Found') }} ' + results.length + ' {{ __('tasks') }}'"></span>
                    <button @click="results = []; filters = null"
                        class="text-xs text-slate-400 hover:text-slate-600">{{ __('Clear') }}</button>
                </div>
                <div
                    class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden max-h-64 overflow-y-auto">
                    <template x-for="t in results" :key="t.id">
                        <a :href="t.url"
                            class="flex items-center justify-between gap-4 px-4 py-3 hover:bg-slate-50 transition-colors">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-slate-800 truncate" x-text="t.title"></span>
                                    <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-md border" :class="{
                                            'bg-rose-50 text-rose-600 border-rose-200': t.priority === 'high',
                                            'bg-amber-50 text-amber-600 border-amber-200': t.priority === 'medium',
                                            'bg-slate-50 text-slate-500 border-slate-200': t.priority === 'low'
                                        }" x-text="t.priority"></span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-slate-500 mt-0.5">
                                    <span x-text="t.project"></span>
                                    <span x-text="t.assignee || '{{ __('Unassigned') }}'"></span>
                                    <span x-text="t.due_date || '—'"></span>
                                </div>
                            </div>
                            <span class="shrink-0 text-[10px] font-bold px-2.5 py-1 rounded-md border" :class="{
                                    'bg-slate-100 text-slate-600 border-slate-200': t.status === 'todo',
                                    'bg-sky-100 text-sky-600 border-sky-200': t.status === 'in_progress',
                                    'bg-emerald-100 text-emerald-600 border-emerald-200': t.status === 'done'
                                }" x-text="t.status.replace('_', ' ')"></span>
                        </a>
                    </template>
                </div>
            </div>

            {{-- Error --}}
            <div x-show="error" x-cloak class="mt-3 text-sm text-rose-600 text-center py-2" x-text="error"></div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('aiSmartSearch', () => ({
                    query: '',
                    loading: false,
                    results: [],
                    filters: null,
                    error: '',

                    search() {
                        if (!this.query.trim() || this.loading) return;
                        this.loading = true;
                        this.error = '';
                        this.results = [];
                        this.filters = null;

                        fetch("{{ route('ai.smart-search') }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            body: JSON.stringify({ query: this.query })
                        })
                            .then(r => { if (!r.ok && r.status === 419) throw new Error('CSRF'); return r.json(); })
                            .then(data => {
                                this.loading = false;
                                if (data.ok) {
                                    this.results = data.results;
                                    this.filters = data.filters;
                                    if (!data.results.length) this.error = '{{ __("No tasks found matching your search.") }}';
                                } else {
                                    this.error = data.message || '{{ __("Search failed.") }}';
                                }
                            })
                            .catch(() => {
                                this.loading = false;
                                this.error = '{{ __("AI service is temporarily unavailable. Please try again.") }}';
                            });
                    }
                }));
            });
        </script>

        {{-- Charts --}}
        <div class="grid gap-4 xl:grid-cols-5">
            <div class="card-strong p-5 xl:col-span-3">
                <div class="mb-4 flex items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900">{{ __('Workload trend (6 months)') }}</h3>
                    <span class="text-xs text-slate-400">{{ __('Created vs Completed') }}</span>
                </div>
                <div class="dashboard-chart-card rounded-xl p-3">
                    <canvas id="tasks-trend-chart" height="100" class="dashboard-chart-canvas"></canvas>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-4 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-2"><span
                            class="h-2.5 w-2.5 rounded-full bg-sky-500"></span>{{ __('Created') }}</span>
                    <span class="inline-flex items-center gap-2"><span
                            class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>{{ __('Completed') }}</span>
                </div>
            </div>

            <div class="card-strong p-5 xl:col-span-2">
                <h3 class="text-sm font-bold text-slate-900 mb-3">{{ __('Task status') }}</h3>
                <div class="dashboard-chart-card rounded-xl p-3">
                    <canvas id="task-status-chart" height="140" class="dashboard-chart-canvas"></canvas>
                </div>
            </div>
        </div>

        {{-- Projects & Upcoming --}}
        <div class="grid gap-4 lg:grid-cols-5">
            <div class="card-strong p-5 lg:col-span-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900">{{ __('Recent projects') }}</h3>
                    <a href="{{ route('projects.index') }}" class="text-xs font-medium hover:underline"
                        style="color: var(--accent);">{{ __('View all') }} →</a>
                </div>
                <div class="space-y-2">
                    @forelse ($recentProjects as $project)
                        <a href="{{ route('projects.show', $project) }}"
                            class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold text-white shrink-0"
                                    style="background: var(--gradient);">
                                    {{ strtoupper(substr($project->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p
                                        class="font-semibold text-sm text-slate-900 group-hover:text-indigo-600 transition-colors">
                                        {{ $project->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $project->owner?->name }} ·
                                        {{ __($project->status) }}</p>
                                </div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-slate-300 group-hover:text-indigo-500 transition-colors"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500 py-4 text-center">{{ __('No projects yet.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="card-strong p-5 lg:col-span-2">
                <h3 class="text-sm font-bold text-slate-900 mb-3">{{ __('Project status') }}</h3>
                <div class="dashboard-chart-card rounded-xl p-3">
                    <canvas id="project-status-chart" height="160" class="dashboard-chart-canvas"></canvas>
                </div>
                <div class="mt-4 h-px bg-slate-100"></div>
                <h4 class="text-sm font-bold text-slate-900 mt-4 mb-3">{{ __('Upcoming tasks') }}</h4>
                <div class="space-y-2">
                    @forelse ($upcomingTasks as $task)
                        <div
                            class="rounded-xl border border-slate-200 px-4 py-3 bg-slate-50/70 hover:border-indigo-200 transition-colors">
                            <p class="font-semibold text-sm text-slate-900">{{ $task->title }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $task->project?->name }} ·
                                {{ $task->due_date?->format('d/m/Y H:i') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">{{ __('No upcoming tasks.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Overdue --}}
        <div class="card-strong p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                    <h3 class="text-sm font-bold text-slate-900">{{ __('Overdue tasks') }}</h3>
                </div>
                <a href="{{ route('tasks.index') }}" class="text-xs font-medium hover:underline"
                    style="color: var(--accent);">{{ __('Manage tasks') }} →</a>
            </div>
            <div class="grid gap-3 md:grid-cols-2">
                @forelse ($overdueTasks as $task)
                    <div class="card p-4 border-red-100 bg-red-50/30 hover:border-red-200 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-slate-900">{{ $task->title }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $task->project?->name }} ·
                                    {{ $task->due_date?->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 col-span-2 text-center py-4">{{ __('No overdue tasks.') }}</p>
                @endforelse
            </div>
        </div>

        {{-- Export --}}
        <div class="flex justify-end">
            <a href="{{ route('exports.reports') }}" class="btn-secondary text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="7 10 12 15 17 10" />
                    <line x1="12" x2="12" y1="15" y2="3" />
                </svg>
                {{ __('Export report') }}
            </a>
        </div>
    </div>

    <div id="dashboard-chart-data" class="hidden" data-project-status='@json($projectStatusData)'
        data-task-status='@json($taskStatusData)' data-task-trend='@json($taskTrendData)'
        data-items-label="{{ __('items') }}" data-created-label="{{ __('Created') }}"
        data-completed-label="{{ __('Completed') }}"></div>

    <div id="dashboard-chart-tooltip" class="dashboard-chart-tooltip hidden"></div>

    <script>
        (function () {
            const dataNode = document.getElementById('dashboard-chart-data');
            if (!dataNode) return;

            const projectStatus = JSON.parse(dataNode.dataset.projectStatus || '[]');
            const taskStatus = JSON.parse(dataNode.dataset.taskStatus || '[]');
            const taskTrend = JSON.parse(dataNode.dataset.taskTrend || '[]');
            const itemsLabel = dataNode.dataset.itemsLabel || 'items';
            const createdLabel = dataNode.dataset.createdLabel || 'Created';
            const completedLabel = dataNode.dataset.completedLabel || 'Completed';
            const tooltipNode = document.getElementById('dashboard-chart-tooltip');
            const chartState = { trendPoints: [] };

            const palette = {
                sky: '#6366f1',
                emerald: '#10b981',
                amber: '#f59e0b',
                slate: '#64748b',
                violet: '#8b5cf6',
                rose: '#fb7185',
            };

            const hexToRgba = (hex, alpha) => {
                const safeHex = String(hex || '').replace('#', '');
                if (safeHex.length !== 6) return `rgba(100, 116, 139, ${alpha})`;
                const red = parseInt(safeHex.slice(0, 2), 16);
                const green = parseInt(safeHex.slice(2, 4), 16);
                const blue = parseInt(safeHex.slice(4, 6), 16);
                return `rgba(${red}, ${green}, ${blue}, ${alpha})`;
            };

            const drawDonut = (canvas, rows) => {
                if (!canvas || !rows.length) return;
                const ctx = canvas.getContext('2d');
                const width = canvas.width = canvas.offsetWidth * window.devicePixelRatio;
                const height = canvas.height = canvas.offsetHeight * window.devicePixelRatio;
                const cx = width / 2;
                const cy = height / 2;
                const radius = Math.min(width, height) * 0.3;
                const lineWidth = radius * 0.45;
                const total = rows.reduce((sum, row) => sum + Number(row.value || 0), 0) || 1;
                const colors = [palette.sky, palette.emerald, palette.amber, palette.violet, palette.rose, palette.slate];

                ctx.clearRect(0, 0, width, height);
                ctx.lineCap = 'round';

                const ringGradient = ctx.createLinearGradient(0, 0, width, height);
                ringGradient.addColorStop(0, 'rgba(99, 102, 241, 0.06)');
                ringGradient.addColorStop(1, 'rgba(139, 92, 246, 0.04)');
                ctx.fillStyle = ringGradient;
                ctx.beginPath();
                ctx.arc(cx, cy, radius + lineWidth * 0.7, 0, Math.PI * 2);
                ctx.fill();

                let angle = -Math.PI / 2;
                rows.forEach((row, index) => {
                    const value = Number(row.value || 0);
                    if (value < 1) return;
                    const next = angle + ((value / total) * Math.PI * 2);
                    ctx.beginPath();
                    ctx.strokeStyle = colors[index % colors.length];
                    ctx.lineWidth = lineWidth;
                    ctx.arc(cx, cy, radius, angle, next);
                    ctx.stroke();
                    angle = next;
                });

                ctx.fillStyle = '#0f172a';
                ctx.textAlign = 'center';
                ctx.font = `700 ${Math.round(20 * window.devicePixelRatio)}px Inter, Space Grotesk`;
                ctx.fillText(String(total), cx, cy + (5 * window.devicePixelRatio));
                ctx.fillStyle = '#64748b';
                ctx.font = `${Math.round(11 * window.devicePixelRatio)}px Inter, Space Grotesk`;
                ctx.fillText(itemsLabel, cx, cy + (24 * window.devicePixelRatio));
            };

            const drawBars = (canvas, rows) => {
                if (!canvas || !rows.length) return;
                const ctx = canvas.getContext('2d');
                const width = canvas.width = canvas.offsetWidth * window.devicePixelRatio;
                const height = canvas.height = canvas.offsetHeight * window.devicePixelRatio;
                const padding = 22 * window.devicePixelRatio;
                const chartWidth = width - padding * 2;
                const chartHeight = height - padding * 2;
                const max = Math.max(1, ...rows.map((row) => Number(row.value || 0)));
                const barWidth = (chartWidth / rows.length) * 0.5;
                const gap = (chartWidth / rows.length) * 0.5;
                const colors = [palette.slate, palette.sky, palette.emerald, palette.amber];

                ctx.clearRect(0, 0, width, height);
                rows.forEach((row, index) => {
                    const value = Number(row.value || 0);
                    const x = padding + index * (barWidth + gap) + (gap / 2);
                    const h = (value / max) * (chartHeight - (22 * window.devicePixelRatio));
                    const y = height - padding - h - (16 * window.devicePixelRatio);
                    const r = 6 * window.devicePixelRatio;

                    const barGradient = ctx.createLinearGradient(0, y, 0, y + h);
                    barGradient.addColorStop(0, colors[index % colors.length]);
                    barGradient.addColorStop(1, hexToRgba(colors[index % colors.length], 0.3));

                    ctx.beginPath();
                    ctx.moveTo(x + r, y);
                    ctx.lineTo(x + barWidth - r, y);
                    ctx.quadraticCurveTo(x + barWidth, y, x + barWidth, y + r);
                    ctx.lineTo(x + barWidth, y + h);
                    ctx.lineTo(x, y + h);
                    ctx.lineTo(x, y + r);
                    ctx.quadraticCurveTo(x, y, x + r, y);
                    ctx.fillStyle = barGradient;
                    ctx.fill();

                    ctx.fillStyle = '#0f172a';
                    ctx.textAlign = 'center';
                    ctx.font = `600 ${Math.round(11 * window.devicePixelRatio)}px Inter, Space Grotesk`;
                    ctx.fillText(String(value), x + barWidth / 2, y - (8 * window.devicePixelRatio));

                    ctx.fillStyle = '#64748b';
                    ctx.font = `${Math.round(10 * window.devicePixelRatio)}px Inter, Space Grotesk`;
                    ctx.fillText(String(row.label || ''), x + barWidth / 2, height - (4 * window.devicePixelRatio));
                });
            };

            const drawTrend = (canvas, rows) => {
                if (!canvas || !rows.length) return;
                const ctx = canvas.getContext('2d');
                const width = canvas.width = canvas.offsetWidth * window.devicePixelRatio;
                const height = canvas.height = canvas.offsetHeight * window.devicePixelRatio;
                const padding = 24 * window.devicePixelRatio;
                const chartWidth = width - padding * 2;
                const chartHeight = height - padding * 2;
                const max = Math.max(1, ...rows.map((row) => Math.max(Number(row.created || 0), Number(row.completed || 0))));

                const point = (idx, value) => ({
                    x: padding + (chartWidth * idx / Math.max(1, rows.length - 1)),
                    y: padding + (chartHeight - (Number(value || 0) / max) * chartHeight),
                });

                ctx.clearRect(0, 0, width, height);
                chartState.trendPoints = [];

                ctx.strokeStyle = 'rgba(148,163,184,0.12)';
                ctx.lineWidth = 1;
                for (let i = 0; i <= 4; i += 1) {
                    const y = padding + (chartHeight / 4) * i;
                    ctx.beginPath();
                    ctx.moveTo(padding, y);
                    ctx.lineTo(width - padding, y);
                    ctx.stroke();
                }

                const drawLine = (key, color) => {
                    const area = [];
                    ctx.strokeStyle = color;
                    ctx.lineWidth = 2.5 * window.devicePixelRatio;
                    ctx.lineJoin = 'round';
                    ctx.lineCap = 'round';
                    ctx.beginPath();
                    rows.forEach((row, idx) => {
                        const p = point(idx, row[key]);
                        area.push(p);
                        if (idx === 0) ctx.moveTo(p.x, p.y);
                        else ctx.lineTo(p.x, p.y);
                    });
                    ctx.stroke();

                    const areaGradient = ctx.createLinearGradient(0, padding, 0, height - padding);
                    areaGradient.addColorStop(0, hexToRgba(color, 0.15));
                    areaGradient.addColorStop(1, hexToRgba(color, 0.01));

                    ctx.beginPath();
                    area.forEach((p, idx) => {
                        if (idx === 0) ctx.moveTo(p.x, p.y);
                        else ctx.lineTo(p.x, p.y);
                    });
                    ctx.lineTo(area[area.length - 1].x, height - padding);
                    ctx.lineTo(area[0].x, height - padding);
                    ctx.closePath();
                    ctx.fillStyle = areaGradient;
                    ctx.fill();

                    rows.forEach((row, idx) => {
                        const p = point(idx, row[key]);
                        ctx.beginPath();
                        ctx.fillStyle = '#ffffff';
                        ctx.arc(p.x, p.y, 4 * window.devicePixelRatio, 0, Math.PI * 2);
                        ctx.fill();
                        ctx.beginPath();
                        ctx.fillStyle = color;
                        ctx.arc(p.x, p.y, 2.5 * window.devicePixelRatio, 0, Math.PI * 2);
                        ctx.fill();

                        chartState.trendPoints.push({
                            x: p.x / window.devicePixelRatio,
                            y: p.y / window.devicePixelRatio,
                            month: String(row.label || ''),
                            label: key === 'created' ? createdLabel : completedLabel,
                            value: Number(row[key] || 0),
                            color,
                        });
                    });
                };

                drawLine('created', palette.sky);
                drawLine('completed', palette.emerald);

                ctx.fillStyle = '#94a3b8';
                ctx.textAlign = 'center';
                ctx.font = `${Math.round(10 * window.devicePixelRatio)}px Inter, Space Grotesk`;
                rows.forEach((row, idx) => {
                    const x = padding + (chartWidth * idx / Math.max(1, rows.length - 1));
                    ctx.fillText(String(row.label || ''), x, height - (5 * window.devicePixelRatio));
                });
            };

            const hideTooltip = () => {
                if (!tooltipNode) return;
                tooltipNode.classList.add('hidden');
                tooltipNode.innerHTML = '';
            };

            const showTooltip = (event, payload) => {
                if (!tooltipNode || !payload) return;
                tooltipNode.innerHTML = `
                    <div style="font-weight:600; margin-bottom:2px;">${payload.month}</div>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <span style="width:8px; height:8px; border-radius:9999px; background:${payload.color}; display:inline-block;"></span>
                        <span>${payload.label}: <strong>${payload.value}</strong></span>
                    </div>
                `;
                tooltipNode.classList.remove('hidden');
                tooltipNode.style.left = `${event.clientX + 14}px`;
                tooltipNode.style.top = `${event.clientY + 14}px`;
            };

            const bindTrendTooltip = () => {
                const canvas = document.getElementById('tasks-trend-chart');
                if (!canvas || !tooltipNode) return;

                canvas.addEventListener('mousemove', (event) => {
                    const rect = canvas.getBoundingClientRect();
                    const x = event.clientX - rect.left;
                    const y = event.clientY - rect.top;

                    let nearest = null;
                    let nearestDistance = Number.POSITIVE_INFINITY;

                    chartState.trendPoints.forEach((point) => {
                        const dx = point.x - x;
                        const dy = point.y - y;
                        const distance = Math.sqrt((dx * dx) + (dy * dy));
                        if (distance < nearestDistance) {
                            nearestDistance = distance;
                            nearest = point;
                        }
                    });

                    if (!nearest || nearestDistance > 18) {
                        hideTooltip();
                        return;
                    }
                    showTooltip(event, nearest);
                });

                canvas.addEventListener('mouseleave', hideTooltip);
            };

            const renderCharts = () => {
                drawTrend(document.getElementById('tasks-trend-chart'), taskTrend);
                drawBars(document.getElementById('task-status-chart'), taskStatus);
                drawDonut(document.getElementById('project-status-chart'), projectStatus);
            };

            renderCharts();
            bindTrendTooltip();
            window.addEventListener('resize', renderCharts);
        })();
    </script>
</x-app-layout>