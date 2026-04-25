<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 w-full">
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Tasks') }}</h2>
                <p class="text-sm text-slate-500 mt-0.5">{{ __('Track and manage all your tasks') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="btn-primary inline-flex items-center gap-2 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        {{ __('New Task') }}
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 z-10 mt-2 w-60 origin-top-right rounded-xl bg-white ring-1 ring-slate-200 focus:outline-none"
                         style="display: none; box-shadow: var(--shadow-lg);">
                         <div class="py-1.5 max-h-60 overflow-y-auto">
                            @if($manageableProjects->isEmpty())
                                <p class="block px-4 py-3 text-sm text-slate-500">{{ __('No projects available for task creation.') }}</p>
                            @else
                                <p class="px-4 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Select Project') }}</p>
                                @foreach($manageableProjects as $project)
                                    <a href="{{ route('projects.tasks.create', $project) }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors rounded-lg mx-1.5">{{ $project->name }}</a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <a href="{{ route('exports.tasks') }}" class="btn-secondary inline-flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    {{ __('Export') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card-strong overflow-hidden">
        <div class="panel-toolbar">
            <form method="GET" action="{{ route('tasks.index') }}" class="w-full flex flex-col sm:flex-row gap-3 sm:items-center">
                <div class="search-wrap sm:max-w-xs">
                    <div class="search-icon">
                        <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search tasks...') }}" class="search-field">
                </div>

                <select name="project_id" onchange="this.form.submit()" class="filter-select">
                    <option value="">{{ __('All Projects') }}</option>
                    @foreach ($projectsFilter as $projectOption)
                        <option value="{{ $projectOption->id }}" @selected((string) request('project_id') === (string) $projectOption->id)>
                            {{ $projectOption->name }}
                        </option>
                    @endforeach
                </select>

                <select name="assignee_id" class="filter-select">
                    <option value="">{{ __('All Assignees') }}</option>
                    @foreach ($usersFilter as $userOption)
                        <option value="{{ $userOption->id }}" @selected((string) request('assignee_id') === (string) $userOption->id)>
                            {{ $userOption->name }}
                        </option>
                    @endforeach
                </select>

                <div class="flex items-center gap-2">
                    <button type="submit" class="btn-secondary h-10 !px-4 text-sm">{{ __('Filter') }}</button>
                    @if(request()->filled('search') || request()->filled('project_id'))
                        <a href="{{ route('tasks.index') }}" class="btn-ghost h-10 !px-3 text-sm">{{ __('Clear') }}</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-shell">
            <table class="table-clean">
                <thead>
                    <tr>
                        <th>{{ __('Task') }}</th>
                        <th>{{ __('Project') }}</th>
                        <th>{{ __('Assignee') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Due date') }}</th>
                        <th class="text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($tasks as $task)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-5 py-4 whitespace-normal min-w-[250px]">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5">
                                        @if($task->status === 'done')
                                            <div class="w-6 h-6 rounded-lg bg-emerald-100 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                            </div>
                                        @elseif($task->status === 'in_progress')
                                            <div class="w-6 h-6 rounded-lg bg-sky-100 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center">
                                                <div class="w-3 h-3 rounded-full border-2 border-slate-300"></div>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-slate-900 hover:text-indigo-600 transition-colors {{ $task->status === 'done' ? 'line-through text-slate-400' : '' }}">{{ $task->title }}</a>
                                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1" title="{{ $task->description }}">{{ \Illuminate\Support\Str::limit($task->description, 60) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('projects.show', $task->project) }}" class="inline-flex items-center gap-1.5 text-slate-600 hover:text-indigo-600 transition-colors">
                                    <div class="w-5 h-5 rounded bg-slate-100 flex items-center justify-center text-[9px] font-bold text-slate-500 shrink-0">
                                        {{ strtoupper(substr($task->project?->name ?? '', 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-sm">{{ $task->project?->name }}</span>
                                </a>
                            </td>
                            <td class="px-5 py-4">
                                @if($task->assignee)
                                    <div class="flex items-center gap-2">
                                        <div class="h-7 w-7 rounded-lg bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600 shrink-0">
                                            {{ strtoupper(substr($task->assignee->name, 0, 2)) }}
                                        </div>
                                        <span class="text-slate-700 font-medium text-sm">{{ $task->assignee->name }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-slate-400 text-sm italic">
                                        <div class="w-7 h-7 rounded-lg bg-slate-50 flex items-center justify-center border border-dashed border-slate-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                        </div>
                                        {{ __('Unassigned') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $statusColors = [
                                        'todo' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        'in_progress' => 'bg-sky-50 text-sky-700 border-sky-200',
                                        'done' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    ];
                                    $colorClass = $statusColors[$task->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                @endphp
                                <span class="meta-chip {{ $colorClass }}">
                                    {{ __(ucwords(str_replace('_', ' ', $task->status))) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $priorityColors = [
                                        'low' => 'text-slate-500 bg-slate-50 border-slate-200',
                                        'medium' => 'text-amber-600 bg-amber-50 border-amber-200',
                                        'high' => 'text-rose-600 bg-rose-50 border-rose-200',
                                    ];
                                    $pColorClass = $priorityColors[$task->priority] ?? 'text-slate-500 bg-slate-50 border-slate-200';
                                @endphp
                                <span class="meta-chip {{ $pColorClass }}">
                                    @if($task->priority === 'high')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m7 7 5-5 5 5"/><path d="m7 13 5-5 5 5"/></svg>
                                    @elseif($task->priority === 'medium')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m7 17 5 5 5-5"/></svg>
                                    @endif
                                    {{ __(ucwords($task->priority)) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-500 text-xs">
                                @if($task->due_date)
                                    @php
                                        $dateClass = '';
                                        if ($task->status === 'done') {
                                            if ($task->updated_at->gt($task->due_date)) {
                                                $dateClass = 'text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg font-bold';
                                            } else {
                                                $dateClass = 'text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg font-bold';
                                            }
                                        } elseif ($task->due_date->isPast()) {
                                            $dateClass = 'text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg font-bold';
                                        } elseif ($task->due_date->isFuture() && $task->due_date->diffInDays(now()) <= 2) {
                                            $dateClass = 'text-amber-600 font-bold';
                                        }
                                    @endphp
                                    <span class="{{ $dateClass }}">{{ $task->due_date->format('d/m/Y H:i') }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('tasks.show', $task) }}" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="{{ __('Open') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-empty">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="empty-state-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" style="color: var(--accent);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900 mb-1">{{ __('No tasks found') }}</h3>
                                    <p class="text-sm text-slate-500 mb-5 max-w-sm">{{ (request()->filled('search') || request()->filled('project_id')) ? __('We couldn\'t find any tasks matching your filters.') : __('You don\'t have any tasks assigned to you yet.') }}</p>
                                    @if(request()->filled('search') || request()->filled('project_id'))
                                        <a href="{{ route('tasks.index') }}" class="btn-secondary">{{ __('Clear search') }}</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tasks->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
