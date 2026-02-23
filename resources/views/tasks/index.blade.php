<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">{{ __('Workspace') }}</p>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">{{ __('Tasks') }}</h2>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('exports.tasks') }}" class="btn-secondary inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    {{ __('Export XLSX') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card-strong overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('tasks.index') }}" class="relative w-full sm:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search tasks...') }}" class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent sm:text-sm transition-shadow">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-4 text-left">{{ __('Task') }}</th>
                        <th class="px-6 py-4 text-left">{{ __('Project') }}</th>
                        <th class="px-6 py-4 text-left">{{ __('Assignee') }}</th>
                        <th class="px-6 py-4 text-left">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-left">{{ __('Priority') }}</th>
                        <th class="px-6 py-4 text-left">{{ __('Due date') }}</th>
                        <th class="px-6 py-4 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($tasks as $task)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-4 whitespace-normal min-w-[250px]">
                                <div class="flex items-start gap-3">
                                    <div class="mt-1">
                                        @if($task->status === 'done')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        @elseif($task->status === 'in_progress')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-sky-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-slate-900 hover:text-accent transition-colors {{ $task->status === 'done' ? 'line-through text-slate-500' : '' }}">{{ $task->title }}</a>
                                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1" title="{{ $task->description }}">{{ \Illuminate\Support\Str::limit($task->description, 60) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('projects.show', $task->project) }}" class="inline-flex items-center gap-1.5 text-slate-600 hover:text-accent transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h18M3 12h18M3 17h12"/></svg>
                                    <span class="font-medium">{{ $task->project?->name }}</span>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @if($task->assignee)
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-medium text-slate-600 shrink-0">
                                            {{ strtoupper(substr($task->assignee->name, 0, 2)) }}
                                        </div>
                                        <span class="text-slate-700 font-medium">{{ $task->assignee->name }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-slate-400 italic">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                                        {{ __('Unassigned') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'todo' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'in_progress' => 'bg-sky-100 text-sky-700 border-sky-200',
                                        'done' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    ];
                                    $colorClass = $statusColors[$task->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $colorClass }}">
                                    {{ __(ucwords(str_replace('_', ' ', $task->status))) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $priorityColors = [
                                        'low' => 'text-slate-500 bg-slate-50 border-slate-200',
                                        'medium' => 'text-amber-600 bg-amber-50 border-amber-200',
                                        'high' => 'text-rose-600 bg-rose-50 border-rose-200',
                                    ];
                                    $pColorClass = $priorityColors[$task->priority] ?? 'text-slate-500 bg-slate-50 border-slate-200';
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $pColorClass }}">
                                    @if($task->priority === 'high')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>
                                    @elseif($task->priority === 'medium')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="m18 14-6-6-6 6"/></svg>
                                    @endif
                                    {{ __(ucwords($task->priority)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs">
                                @if($task->due_date)
                                    @php
                                        $isOverdue = $task->due_date->isPast() && $task->status !== 'done';
                                        $isDueSoon = $task->due_date->isFuture() && $task->due_date->diffInDays(now()) <= 2 && $task->status !== 'done';
                                    @endphp
                                    <div class="flex items-center gap-1.5 {{ $isOverdue ? 'text-rose-600 font-medium' : ($isDueSoon ? 'text-amber-600 font-medium' : '') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        <span>{{ $task->due_date->format('d/m/Y') }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('tasks.show', $task) }}" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-accent hover:bg-accent/5 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100" title="{{ __('Open') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                    </div>
                                    <h3 class="text-sm font-medium text-slate-900 mb-1">{{ __('No tasks found') }}</h3>
                                    <p class="text-sm text-slate-500 mb-4 max-w-sm">{{ request('search') ? __('We couldn\'t find any tasks matching your search.') : __('You don\'t have any tasks assigned to you yet.') }}</p>
                                    @if(request('search'))
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
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
