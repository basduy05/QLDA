<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="space-y-1">
                <p class="text-sm text-slate-500">{{ __('Overview') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900 lg:text-3xl">{{ __('Project command center') }}</h2>
                <p class="text-sm text-slate-500">{{ __('A quick pulse of project health and delivery rhythm.') }}</p>
            </div>
            <a href="{{ route('exports.reports') }}" class="btn-secondary">{{ __('Export report') }}</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="card-strong dashboard-kpi p-4">
                <p class="text-xs uppercase tracking-widest text-slate-500">{{ __('Total projects') }}</p>
                <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $projectsCount }}</p>
                <p class="mt-0.5 text-xs text-slate-500">{{ __('Across all monitored workspaces') }}</p>
            </div>
            <div class="card-strong dashboard-kpi p-4">
                <p class="text-xs uppercase tracking-widest text-slate-500">{{ __('Active projects') }}</p>
                <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $activeProjectsCount }}</p>
                <p class="mt-0.5 text-xs text-slate-500">{{ __('In current execution phase') }}</p>
            </div>
            <div class="card-strong dashboard-kpi p-4">
                <p class="text-xs uppercase tracking-widest text-slate-500">{{ __('Tasks in view') }}</p>
                <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $tasksCount }}</p>
                <p class="mt-0.5 text-xs text-slate-500">{{ __('All tasks inside your visibility') }}</p>
            </div>
            <div class="card-strong dashboard-kpi p-4">
                <p class="text-xs uppercase tracking-widest text-slate-500">{{ __('Open tasks') }}</p>
                <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $openTasksCount }}</p>
                <p class="mt-0.5 text-xs text-slate-500">{{ __('Need follow-up or action') }}</p>
            </div>
            <div class="card-strong dashboard-kpi p-4 bg-gradient-to-br from-white to-sky-50">
                <p class="text-xs uppercase tracking-widest text-slate-500">{{ __('Completion rate') }}</p>
                <div class="mt-2 flex items-center gap-3">
                    <div id="completion-ring" data-rate="{{ $completionRate }}" class="h-12 w-12 rounded-full border border-slate-200 grid place-items-center text-sm font-semibold text-slate-800 bg-slate-100">
                        <span class="h-8 w-8 rounded-full bg-white grid place-items-center">{{ $completionRate }}%</span>
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-slate-900">{{ $doneTasksCount }}</p>
                        <p class="text-xs text-slate-500">{{ __('completed tasks') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-5">
            <div class="card-strong p-4 xl:col-span-3">
                <div class="mb-4 flex items-center justify-between gap-2">
                    <h3 class="text-base font-semibold">{{ __('Workload trend (6 months)') }}</h3>
                    <span class="text-xs text-slate-500">{{ __('Created vs Completed') }}</span>
                </div>
                <div class="dashboard-chart-card rounded-xl p-2">
                    <canvas id="tasks-trend-chart" height="100" class="dashboard-chart-canvas"></canvas>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-sky-500"></span>{{ __('Created') }}</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>{{ __('Completed') }}</span>
                </div>
            </div>

            <div class="card-strong p-4 xl:col-span-2">
                <h3 class="text-base font-semibold mb-3">{{ __('Task status') }}</h3>
                <div class="dashboard-chart-card rounded-xl p-2">
                    <canvas id="task-status-chart" height="140" class="dashboard-chart-canvas"></canvas>
                </div>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-5">
            <div class="card-strong p-4 lg:col-span-3">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold">{{ __('Recent projects') }}</h3>
                    <a href="{{ route('projects.index') }}" class="text-sm text-slate-500 hover:text-slate-800">{{ __('View all') }}</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($recentProjects as $project)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="font-medium text-slate-900">{{ $project->name }}</p>
                                <p class="text-xs text-slate-500">{{ $project->owner?->name }} · {{ __($project->status) }}</p>
                            </div>
                            <a href="{{ route('projects.show', $project) }}" class="text-xs text-slate-600 hover:text-slate-900">{{ __('Open') }}</a>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 py-4">{{ __('No projects yet.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="card-strong p-4 lg:col-span-2">
                <h3 class="text-base font-semibold mb-3">{{ __('Project status') }}</h3>
                <div class="dashboard-chart-card rounded-xl p-2">
                    <canvas id="project-status-chart" height="160" class="dashboard-chart-canvas"></canvas>
                </div>
                <div class="mt-4 h-px bg-slate-100"></div>
                <h4 class="text-sm font-medium mt-3 mb-2">{{ __('Upcoming tasks') }}</h4>
                <div class="space-y-2">
                    @forelse ($upcomingTasks as $task)
                        <div class="rounded-lg border border-slate-200 px-3 py-2 bg-slate-50/70">
                            <p class="font-medium text-sm text-slate-900">{{ $task->title }}</p>
                            <p class="text-xs text-slate-500">{{ $task->project?->name }} · {{ $task->due_date?->format('d/m/Y') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">{{ __('No upcoming tasks.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card-strong p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold">{{ __('Overdue tasks') }}</h3>
                <a href="{{ route('tasks.index') }}" class="text-sm text-slate-500 hover:text-slate-800">{{ __('Manage tasks') }}</a>
            </div>
            <div class="grid gap-3 md:grid-cols-2">
                @forelse ($overdueTasks as $task)
                    <div class="card p-3 border-red-100 bg-red-50/30">
                        <p class="font-medium text-sm text-slate-900">{{ $task->title }}</p>
                        <p class="text-xs text-slate-500">{{ $task->project?->name }} · {{ $task->due_date?->format('d/m/Y') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">{{ __('No overdue tasks.') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <div
        id="dashboard-chart-data"
        class="hidden"
        data-project-status='@json($projectStatusData)'
        data-task-status='@json($taskStatusData)'
        data-task-trend='@json($taskTrendData)'
        data-items-label="{{ __('items') }}"
        data-created-label="{{ __('Created') }}"
        data-completed-label="{{ __('Completed') }}"
    ></div>

    <div id="dashboard-chart-tooltip" class="dashboard-chart-tooltip hidden"></div>

    <script>
        (function () {
            const dataNode = document.getElementById('dashboard-chart-data');
            if (!dataNode) {
                return;
            }

            const projectStatus = JSON.parse(dataNode.dataset.projectStatus || '[]');
            const taskStatus = JSON.parse(dataNode.dataset.taskStatus || '[]');
            const taskTrend = JSON.parse(dataNode.dataset.taskTrend || '[]');
            const itemsLabel = dataNode.dataset.itemsLabel || 'items';
            const createdLabel = dataNode.dataset.createdLabel || 'Created';
            const completedLabel = dataNode.dataset.completedLabel || 'Completed';
            const tooltipNode = document.getElementById('dashboard-chart-tooltip');
            const chartState = {
                trendPoints: [],
            };

            const palette = {
                sky: '#0ea5e9',
                emerald: '#10b981',
                amber: '#f59e0b',
                slate: '#64748b',
                violet: '#8b5cf6',
                rose: '#fb7185',
            };

            const hexToRgba = (hex, alpha) => {
                const safeHex = String(hex || '').replace('#', '');
                if (safeHex.length !== 6) {
                    return `rgba(100, 116, 139, ${alpha})`;
                }

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
                ringGradient.addColorStop(0, 'rgba(14, 165, 233, 0.09)');
                ringGradient.addColorStop(1, 'rgba(16, 185, 129, 0.06)');
                ctx.fillStyle = ringGradient;
                ctx.beginPath();
                ctx.arc(cx, cy, radius + lineWidth * 0.7, 0, Math.PI * 2);
                ctx.fill();

                let angle = -Math.PI / 2;
                rows.forEach((row, index) => {
                    const value = Number(row.value || 0);
                    if (value < 1) {
                        return;
                    }

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
                ctx.font = `${Math.round(18 * window.devicePixelRatio)}px Space Grotesk`;
                ctx.fillText(String(total), cx, cy + (5 * window.devicePixelRatio));
                ctx.fillStyle = '#64748b';
                ctx.font = `${Math.round(11 * window.devicePixelRatio)}px Space Grotesk`;
                ctx.fillText(itemsLabel, cx, cy + (22 * window.devicePixelRatio));
            };

            const drawBars = (canvas, rows) => {
                if (!canvas || !rows.length) return;
                const ctx = canvas.getContext('2d');
                const width = canvas.width = canvas.offsetWidth * window.devicePixelRatio;
                const height = canvas.height = canvas.offsetHeight * window.devicePixelRatio;
                const padding = 20 * window.devicePixelRatio;
                const chartWidth = width - padding * 2;
                const chartHeight = height - padding * 2;
                const max = Math.max(1, ...rows.map((row) => Number(row.value || 0)));
                const barWidth = (chartWidth / rows.length) * 0.58;
                const gap = (chartWidth / rows.length) * 0.42;
                const colors = [palette.slate, palette.sky, palette.emerald, palette.amber];

                ctx.clearRect(0, 0, width, height);
                rows.forEach((row, index) => {
                    const value = Number(row.value || 0);
                    const x = padding + index * (barWidth + gap) + (gap / 2);
                    const h = (value / max) * (chartHeight - (22 * window.devicePixelRatio));
                    const y = height - padding - h - (16 * window.devicePixelRatio);

                    const barGradient = ctx.createLinearGradient(0, y, 0, y + h);
                    barGradient.addColorStop(0, colors[index % colors.length]);
                    barGradient.addColorStop(1, 'rgba(148, 163, 184, 0.45)');
                    ctx.fillStyle = barGradient;
                    ctx.fillRect(x, y, barWidth, h);

                    ctx.fillStyle = '#0f172a';
                    ctx.textAlign = 'center';
                    ctx.font = `${Math.round(10 * window.devicePixelRatio)}px Space Grotesk`;
                    ctx.fillText(String(value), x + barWidth / 2, y - (6 * window.devicePixelRatio));

                    ctx.fillStyle = '#64748b';
                    ctx.fillText(String(row.label || ''), x + barWidth / 2, height - (4 * window.devicePixelRatio));
                });
            };

            const drawTrend = (canvas, rows) => {
                if (!canvas || !rows.length) return;
                const ctx = canvas.getContext('2d');
                const width = canvas.width = canvas.offsetWidth * window.devicePixelRatio;
                const height = canvas.height = canvas.offsetHeight * window.devicePixelRatio;
                const padding = 22 * window.devicePixelRatio;
                const chartWidth = width - padding * 2;
                const chartHeight = height - padding * 2;
                const max = Math.max(1, ...rows.map((row) => Math.max(Number(row.created || 0), Number(row.completed || 0))));

                const point = (idx, value) => ({
                    x: padding + (chartWidth * idx / Math.max(1, rows.length - 1)),
                    y: padding + (chartHeight - (Number(value || 0) / max) * chartHeight),
                });

                ctx.clearRect(0, 0, width, height);
                chartState.trendPoints = [];

                ctx.strokeStyle = 'rgba(100,116,139,0.2)';
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
                    ctx.lineWidth = 2.4 * window.devicePixelRatio;
                    ctx.beginPath();
                    rows.forEach((row, idx) => {
                        const p = point(idx, row[key]);
                        area.push(p);
                        if (idx === 0) ctx.moveTo(p.x, p.y);
                        else ctx.lineTo(p.x, p.y);
                    });
                    ctx.stroke();

                    const areaGradient = ctx.createLinearGradient(0, padding, 0, height - padding);
                    areaGradient.addColorStop(0, hexToRgba(color, 0.18));
                    areaGradient.addColorStop(1, hexToRgba(color, 0.02));

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
                        ctx.fillStyle = color;
                        ctx.arc(p.x, p.y, 3.1 * window.devicePixelRatio, 0, Math.PI * 2);
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

                ctx.fillStyle = '#64748b';
                ctx.textAlign = 'center';
                ctx.font = `${Math.round(10 * window.devicePixelRatio)}px Space Grotesk`;
                rows.forEach((row, idx) => {
                    const x = padding + (chartWidth * idx / Math.max(1, rows.length - 1));
                    ctx.fillText(String(row.label || ''), x, height - (5 * window.devicePixelRatio));
                });
            };

            const hideTooltip = () => {
                if (!tooltipNode) {
                    return;
                }

                tooltipNode.classList.add('hidden');
                tooltipNode.innerHTML = '';
            };

            const showTooltip = (event, payload) => {
                if (!tooltipNode || !payload) {
                    return;
                }

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
                if (!canvas || !tooltipNode) {
                    return;
                }

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

                const completionRing = document.getElementById('completion-ring');
                const rate = Number(completionRing?.dataset.rate || 0);
                if (completionRing) {
                    completionRing.style.background = `conic-gradient(#0ea5e9 ${rate}%, #e2e8f0 ${rate}% 100%)`;
                }
            };

            renderCharts();
            bindTrendTooltip();
            window.addEventListener('resize', renderCharts);
        })();
    </script>
</x-app-layout>
