<x-app-layout>
    <style>
        .project-progress-bar {
            width: var(--project-progress);
        }
    </style>

    @php
        $roleLabels = [
            'lead' => __('Lead'),
            'deputy' => __('Deputy'),
            'member' => __('Member'),
        ];
    @endphp

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('projects.index') }}" class="text-sm font-medium text-slate-500 hover:text-accent transition-colors uppercase tracking-wider">{{ __('Projects') }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    <span class="text-sm font-medium text-slate-400 uppercase tracking-wider">{{ __('Details') }}</span>
                </div>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                    {{ $project->name }}
                    @php
                        $statusColors = [
                            'planning' => 'bg-sky-100 text-sky-700 border-sky-200',
                            'active' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'on_hold' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'completed' => 'bg-slate-100 text-slate-700 border-slate-200',
                        ];
                        $colorClass = $statusColors[$project->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $colorClass }} align-middle">
                        {{ __(ucwords(str_replace('_', ' ', $project->status))) }}
                    </span>
                </h2>
                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-slate-100 text-xs font-medium text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                        {{ __('Your role') }}: <span class="text-slate-900">{{ $roleLabels[$viewerRole] ?? __('Guest') }}</span>
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if ($canUpdateProject)
                    <a href="{{ route('projects.edit', $project) }}" class="btn-secondary inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                        {{ __('Edit') }}
                    </a>
                @endif
                @if ($canManageMembers)
                    <form method="POST" action="{{ route('projects.destroy', $project) }}" data-confirm="{{ __('Delete this project?') }}" onsubmit="return confirm(this.dataset.confirm)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-secondary text-rose-600 hover:bg-rose-50 hover:border-rose-200 hover:text-rose-700 inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                            {{ __('Delete') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="card p-4 text-sm text-emerald-700 bg-emerald-50 border-emerald-200 flex items-center gap-3 animate-[softFadeUp_0.3s_ease-out]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card-strong p-6 lg:col-span-2 flex flex-col">
                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    {{ __('Overview') }}
                </h3>
                <div class="prose prose-sm prose-slate max-w-none flex-grow">
                    @if($project->description)
                        <p class="text-slate-600 leading-relaxed">{{ $project->description }}</p>
                    @else
                        <p class="text-slate-400 italic">{{ __('No description provided.') }}</p>
                    @endif
                </div>
                
                <div class="mt-6 pt-6 border-t border-slate-100 grid gap-6 md:grid-cols-2 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-0.5">{{ __('Owner') }}</p>
                            <p class="font-semibold text-slate-900">{{ $project->owner?->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-0.5">{{ __('Timeline') }}</p>
                            <p class="font-semibold text-slate-900">
                                {{ $project->start_date?->format('d/m/Y') ?? '—' }} 
                                <span class="text-slate-400 mx-1">→</span> 
                                {{ $project->end_date?->format('d/m/Y') ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-strong p-6 bg-gradient-to-br from-white to-slate-50/50">
                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m13 13.5 2-2.5-2-2.5"/><path d="m21 21-4.3-4.3"/><circle cx="11" cy="11" r="8"/></svg>
                    {{ __('Quick actions') }}
                </h3>
                <div class="space-y-3">
                    @if ($canManageTasks)
                        <a href="{{ route('projects.tasks.create', $project) }}" class="btn-primary w-full justify-center gap-2 shadow-md shadow-accent/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                            {{ __('Add task') }}
                        </a>
                    @endif
                    <a href="{{ route('projects.index') }}" class="btn-secondary w-full justify-center gap-2 bg-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        {{ __('Back to projects') }}
                    </a>
                </div>
                
                <div class="mt-6 pt-6 border-t border-slate-200/60">
                    @php
                        $completedTasks = $project->tasks()->where('status', 'done')->count();
                        $progressPercentage = $project->tasks_count > 0
                            ? round(($completedTasks / $project->tasks_count) * 100)
                            : 0;
                    @endphp
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-slate-700">{{ __('Progress') }}</span>
                        <span class="text-sm font-bold text-slate-900">{{ $progressPercentage }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                        <div class="project-progress-bar bg-accent h-2.5 rounded-full transition-all duration-500" style="--project-progress: {{ $progressPercentage }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2 text-center">{{ $completedTasks }} / {{ $project->tasks_count }} {{ __('tasks completed') }}</p>
                </div>
            </div>
        </div>

        <div class="card-strong p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    {{ __('Team members') }}
                    <span class="inline-flex items-center justify-center h-5 min-w-[1.25rem] px-1.5 rounded-full bg-slate-100 text-slate-600 text-xs font-medium ml-1">
                        {{ $project->members->count() }}
                    </span>
                </h3>
                @if ($canManageMembers)
                    <span class="text-xs font-medium text-slate-500 bg-slate-50 px-3 py-1.5 rounded-md border border-slate-100 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        {{ __('Lead can assign deputy/member roles.') }}
                    </span>
                @endif
            </div>

            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-xl border border-accent/20 p-4 bg-accent/5 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-accent/10 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div class="flex items-start justify-between gap-3 relative z-10">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-white border border-accent/20 flex items-center justify-center text-accent font-bold shadow-sm">
                                {{ strtoupper(substr($project->owner?->name ?? '?', 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-900">{{ $project->owner?->name }}</p>
                                <p class="text-xs text-slate-500">{{ $project->owner?->email }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-accent text-white shadow-sm">
                            {{ $roleLabels['lead'] }}
                        </span>
                    </div>
                </div>

                @foreach ($project->members->where('id', '!=', $project->owner_id)->sortBy('name') as $member)
                    <div class="rounded-xl border border-slate-200 p-4 bg-white hover:border-slate-300 hover:shadow-sm transition-all group">
                        <div class="flex flex-col h-full justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <div class="h-10 w-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold">
                                    {{ strtoupper(substr($member->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900 group-hover:text-accent transition-colors">{{ $member->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $member->email }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-auto pt-3 border-t border-slate-50">
                                <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider {{ $member->pivot->role === 'deputy' ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $roleLabels[$member->pivot->role] ?? $member->pivot->role }}
                                </span>

                            @if ($canManageMembers)
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('projects.members.update', [$project, $member]) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="rounded-xl border-slate-200 text-sm" onchange="this.form.requestSubmit()">
                                            @foreach (['deputy', 'member'] as $role)
                                                <option value="{{ $role }}" @selected(($member->pivot->role ?? 'member') === $role)>{{ $roleLabels[$role] }}</option>
                                            @endforeach
                                        </select>
                                    </form>

                                    <form method="POST" action="{{ route('projects.members.remove', [$project, $member]) }}" data-confirm="{{ __('Remove this member?') }}" onsubmit="return confirm(this.dataset.confirm)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors" title="{{ __('Remove') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($canManageMembers)
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <h4 class="text-sm font-bold text-slate-900 mb-3">{{ __('Add new member') }}</h4>
                    <form method="POST" action="{{ route('projects.members.store', $project) }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        @csrf
                        <div class="w-full sm:max-w-xs">
                            <select name="user_id" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-accent focus:border-accent">
                                <option value="">{{ __('Select user...') }}</option>
                                @foreach ($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-auto">
                            <select name="role" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-accent focus:border-accent">
                                <option value="member">{{ $roleLabels['member'] }}</option>
                                <option value="deputy">{{ $roleLabels['deputy'] }}</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-secondary w-full sm:w-auto whitespace-nowrap inline-flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                            {{ __('Add member') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="card-strong p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    {{ __('Project tasks') }}
                    <span class="inline-flex items-center justify-center h-5 min-w-[1.25rem] px-1.5 rounded-full bg-slate-100 text-slate-600 text-xs font-medium ml-1">
                        {{ $project->tasks->count() }}
                    </span>
                </h3>
                @if ($canManageTasks)
                    <a href="{{ route('projects.tasks.create', $project) }}" class="btn-primary text-sm py-1.5 px-3 inline-flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                        {{ __('New task') }}
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto -mx-6 px-6 sm:mx-0 sm:px-0">
                <table class="min-w-full text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left rounded-tl-lg">{{ __('Task') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Assignee') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Priority') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Due date') }}</th>
                            <th class="px-4 py-3 text-right rounded-tr-lg"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($project->tasks as $task)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-4 py-3 whitespace-normal min-w-[200px]">
                                    <div class="flex items-start gap-2.5">
                                        <div class="mt-0.5">
                                            @if($task->status === 'done')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            @elseif($task->status === 'in_progress')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-sky-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-slate-900 hover:text-accent transition-colors {{ $task->status === 'done' ? 'line-through text-slate-500' : '' }}">{{ $task->title }}</a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($task->assignee)
                                        <div class="flex items-center gap-2">
                                            <div class="h-5 w-5 rounded-full bg-slate-200 flex items-center justify-center text-[9px] font-medium text-slate-600 shrink-0">
                                                {{ strtoupper(substr($task->assignee->name, 0, 2)) }}
                                            </div>
                                            <span class="text-slate-700 text-xs font-medium">{{ $task->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs italic">{{ __('Unassigned') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'todo' => 'bg-slate-100 text-slate-700 border-slate-200',
                                            'in_progress' => 'bg-sky-100 text-sky-700 border-sky-200',
                                            'done' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        ];
                                        $colorClass = $statusColors[$task->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $colorClass }}">
                                        {{ __(ucwords(str_replace('_', ' ', $task->status))) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $priorityColors = [
                                            'low' => 'text-slate-500 bg-slate-50 border-slate-200',
                                            'medium' => 'text-amber-600 bg-amber-50 border-amber-200',
                                            'high' => 'text-rose-600 bg-rose-50 border-rose-200',
                                        ];
                                        $pColorClass = $priorityColors[$task->priority] ?? 'text-slate-500 bg-slate-50 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium border {{ $pColorClass }}">
                                        {{ __(ucwords($task->priority)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-xs">
                                    {{ $task->due_date?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('tasks.show', $task) }}" class="inline-flex items-center justify-center p-1.5 rounded-md text-slate-400 hover:text-accent hover:bg-accent/5 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100" title="{{ __('Open') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center">
                                    <p class="text-sm text-slate-500">{{ __('No tasks created yet.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
