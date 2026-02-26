<x-app-layout>
    <style>
        .project-progress-bar {
            width: var(--project-progress);
        }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            animation: marquee 10s linear infinite;
        }
        .group\/text:hover .group-hover\/text\:pause {
            animation-play-state: paused;
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
                    <form method="POST" action="{{ route('projects.destroy', $project) }}" data-confirm="{{ __('Delete this project?') }}">
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

        <div class="grid lg:grid-cols-3 gap-6 items-start">
            <!-- Team Members -->
            <div class="card-strong p-4 flex flex-col h-full max-h-[500px] lg:col-span-1">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4 shrink-0">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                        {{ __('Team members') }}
                        <span class="inline-flex items-center justify-center h-5 min-w-[1.25rem] px-1.5 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                            {{ $project->members->count() }}
                        </span>
                    </h3>
                    @if ($canManageMembers)
                        <span class="text-[10px] font-medium text-slate-500 bg-slate-50 px-2 py-1 rounded-md border border-slate-100 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            {{ __('Lead can assign deputy/member roles.') }}
                        </span>
                    @endif
                </div>

                <div class="overflow-y-auto pr-1 space-y-2 flex-grow custom-scrollbar">
                    <div class="rounded-lg border border-accent/20 p-2 bg-accent/5 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-8 h-8 bg-accent/10 rounded-bl-full -mr-4 -mt-4"></div>
                        <div class="flex items-center justify-between gap-2 relative z-10">
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-full bg-white border border-accent/20 flex items-center justify-center text-accent font-bold shadow-sm shrink-0 text-xs">
                                    {{ strtoupper(substr($project->owner?->name ?? '?', 0, 2)) }}
                                </div>
                                <div class="min-w-0 overflow-hidden w-full relative group/text">
                                    <div class="font-bold text-slate-900 text-xs {{ strlen($project->owner?->name) > 15 ? 'flex w-fit animate-marquee group-hover/text:pause whitespace-nowrap' : 'truncate' }}">
                                        @if(strlen($project->owner?->name) > 15)
                                            <span class="mr-4">{{ $project->owner?->name }}</span>
                                            <span>{{ $project->owner?->name }}</span>
                                        @else
                                            {{ $project->owner?->name }}
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-slate-500 truncate max-w-[100px]">{{ $project->owner?->email }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-accent text-white shadow-sm shrink-0">
                                {{ $roleLabels['lead'] }}
                            </span>
                        </div>
                    </div>

                    @foreach ($project->members->where('id', '!=', $project->owner_id)->sortBy('name') as $member)
                        <div class="rounded-lg border border-slate-200 p-2 bg-white hover:border-slate-300 transition-all group">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="h-8 w-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold shrink-0 text-xs">
                                        {{ strtoupper(substr($member->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0 overflow-hidden w-full relative group/text">
                                        <div class="font-semibold text-slate-900 text-xs group-hover:text-accent transition-colors {{ strlen($member->name) > 15 ? 'flex w-fit animate-marquee group-hover/text:pause whitespace-nowrap' : 'truncate' }}">
                                            @if(strlen($member->name) > 15)
                                                <span class="mr-4">{{ $member->name }}</span>
                                                <span>{{ $member->name }}</span>
                                            @else
                                                {{ $member->name }}
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="inline-flex items-center px-1 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $member->pivot->role === 'deputy' ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-600' }}">
                                                {{ $roleLabels[$member->pivot->role] ?? $member->pivot->role }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-1 shrink-0">
                                    @if ($canManageMembers)
                                        <div class="flex items-center gap-1 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity">
                                            <form method="POST" action="{{ route('projects.members.update', [$project, $member]) }}" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" name="role" value="{{ $member->pivot->role === 'member' ? 'deputy' : 'member' }}" class="p-1 text-slate-400 hover:text-accent hover:bg-accent/5 rounded transition-colors" title="{{ __('Toggle Role') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('projects.members.remove', [$project, $member]) }}" data-confirm="{{ __('Remove this member?') }}" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded transition-colors" title="{{ __('Remove') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
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
                    <div class="mt-3 pt-3 border-t border-slate-100 shrink-0 relative" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="btn-secondary w-full justify-center gap-1.5 text-xs !py-1.5 focus:ring-2 focus:ring-accent focus:ring-offset-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                            {{ __('Add') }}
                        </button>

                         <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            @click.away="open = false"
                            class="absolute left-0 bottom-full mb-2 w-64 bg-white border border-slate-200 shadow-xl rounded-lg p-3 z-50"
                            style="display: none;"
                        >
                            <form method="POST" action="{{ route('projects.members.add', $project) }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">{{ __('Select User') }}</label>
                                    <select name="user_id" required class="block w-full rounded-md border-slate-200 text-xs py-1.5 focus:ring-accent focus:border-accent">
                                        <option value="">{{ __('User...') }}</option>
                                        @foreach ($availableUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">{{ __('Role') }}</label>
                                    <select name="role" required class="block w-full rounded-md border-slate-200 text-xs py-1.5 focus:ring-accent focus:border-accent">
                                        <option value="member">{{ $roleLabels['member'] }}</option>
                                        <option value="deputy">{{ $roleLabels['deputy'] }}</option>
                                    </select>
                                </div>
                                <div class="flex justify-end gap-2 pt-1">
                                    <button type="button" @click="open = false" class="btn-secondary text-xs !py-1">{{ __('Cancel') }}</button>
                                    <button type="submit" class="btn-primary text-xs !py-1">
                                        {{ __('Add Member') }}
                                    </button>
                                </div>
                            </form>
                            <!-- Arrow for tooltip -->
                            <div class="absolute -bottom-1 left-10 w-2 h-2 bg-white border-b border-l border-slate-200 transform -rotate-45"></div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Member Contribution -->
            <div class="card-strong p-4 flex flex-col h-full max-h-[500px] lg:col-span-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4 shrink-0">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                        {{ __('Member Contribution') }}
                    </h3>
                    <a href="{{ route('projects.export', $project) }}" class="btn-secondary text-xs !py-1 !px-2 inline-flex items-center gap-1.5 shink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                        {{ __('Export') }}
                    </a>
                </div>
                <!-- Add scrollable container for table -->
                <div class="overflow-y-auto custom-scrollbar flex-grow -mx-4 px-4 sm:mx-0 sm:px-0">
                    <table class="w-full text-xs relative table-fixed">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-2 py-2 text-left bg-slate-50 w-1/4">{{ __('Member') }}</th>
                                <th class="px-1 py-2 text-center bg-slate-50 w-[10%]">{{ __('Total') }}</th>
                                <th class="px-1 py-2 text-center bg-slate-50 w-[10%]">{{ __('Ok') }}</th>
                                <th class="px-1 py-2 text-center bg-slate-50 w-[10%]">{{ __('Late') }}</th>
                                <th class="px-1 py-2 text-center bg-slate-50 w-[10%]">{{ __('Prog') }}</th>
                                <th class="px-1 py-2 text-center bg-slate-50 w-[10%]">{{ __('Due') }}</th>
                                <th class="px-2 py-2 text-right bg-slate-50 rounded-tr-lg w-1/4">{{ __('Contrib') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($memberStats as $stat)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-2 py-2 tooltip-container relative">
                                        <div class="flex items-center gap-2 overflow-hidden">
                                            <div class="h-6 w-6 rounded-full bg-gradient-to-br from-accent to-blue-600 text-white flex items-center justify-center font-bold text-[10px] shadow-sm shrink-0">
                                                {{ strtoupper(substr($stat['user']->name, 0, 1)) }}
                                            </div>
                                            <div class="overflow-hidden w-full relative group/text">
                                                <div class="font-medium text-slate-900 {{ strlen($stat['user']->name) > 15 ? 'flex w-fit animate-marquee group-hover/text:pause whitespace-nowrap' : 'truncate' }}">
                                                    @if(strlen($stat['user']->name) > 15)
                                                        <span class="mr-4">{{ $stat['user']->name }}</span>
                                                        <span>{{ $stat['user']->name }}</span>
                                                    @else
                                                        {{ $stat['user']->name }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-1 py-2 text-center font-medium">{{ $stat['total_tasks'] }}</td>
                                    <td class="px-1 py-2 text-center text-emerald-600 font-medium">{{ $stat['completed_on_time'] }}</td>
                                    <td class="px-1 py-2 text-center text-amber-600 font-medium">{{ $stat['completed_late'] }}</td>
                                    <td class="px-1 py-2 text-center text-sky-600 font-medium">{{ $stat['in_progress'] }}</td>
                                    <td class="px-1 py-2 text-center text-rose-600 font-medium">{{ $stat['overdue'] }}</td>
                                    <td class="px-2 py-2 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <div class="w-16 bg-slate-200 rounded-full h-1.5 overflow-hidden hidden sm:block">
                                                <div class="bg-accent h-1.5 rounded-full" style="width: {{ $stat['contribution_percentage'] }}%"></div>
                                            </div>
                                            <span class="font-bold text-slate-900 w-8 text-right">{{ $stat['contribution_percentage'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                    {{ __('Kanban Board') }}
                </h3>
                @if ($canManageTasks)
                    <a href="{{ route('projects.tasks.create', $project) }}" class="btn-primary py-2 px-4 rounded-full text-sm shadow-md shadow-accent/20">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                        {{ __('New Task') }}
                    </a>
                @endif
            </div>

            <div class="flex gap-6 overflow-x-auto pb-6" style="min-height: 500px;">
                @php
                    $columns = [
                        'todo' => ['label' => 'To Do', 'color' => 'bg-slate-100 border-slate-200', 'text' => 'text-slate-700', 'dot' => 'bg-slate-400'],
                        'in_progress' => ['label' => 'In Progress', 'color' => 'bg-sky-50 border-sky-100', 'text' => 'text-sky-700', 'dot' => 'bg-sky-400'],
                        'done' => ['label' => 'Done', 'color' => 'bg-emerald-50 border-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-400'],
                    ];
                @endphp

                @foreach ($columns as $status => $style)
                    <div class="flex-1 min-w-[300px] flex flex-col h-full rounded-2xl bg-slate-50/50 border border-slate-200/60 p-2">
                        {{-- Column Header --}}
                        <div class="p-3 mb-2 flex items-center justify-between sticky top-0 bg-slate-50/50 backdrop-blur-sm z-10 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full {{ $style['dot'] }}"></div>
                                <h4 class="font-bold text-sm text-slate-800 uppercase tracking-wide">{{ __($style['label']) }}</h4>
                                <span class="bg-white px-2 py-0.5 rounded-md text-xs font-bold text-slate-500 border border-slate-100 shadow-sm">
                                    {{ $project->tasks->where('status', $status)->count() }}
                                </span>
                            </div>
                        </div>
                        
                        {{-- Task Cards --}}
                        <div class="flex-1 space-y-3 p-1 overflow-y-auto custom-scrollbar" 
                             ondrop="drop(event, '{{ $status }}')" 
                             ondragover="allowDrop(event)">
                            @forelse ($project->tasks->where('status', $status)->sortByDesc('created_at') as $task)
                                <div class="group bg-white p-4 rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-accent/30 transition-all duration-200 cursor-pointer relative task-card" 
                                     draggable="{{ $canManageTasks ? 'true' : 'false' }}" 
                                     ondragstart="drag(event, '{{ $task->id }}')"
                                     onclick="if(!event.target.closest('a')) window.location='{{ route('tasks.show', $task) }}'">
                                    
                                    {{-- Priority Badge --}}
                                    <div class="flex justify-between items-start mb-2">
                                        @php
                                            $priorityColors = [
                                                'low' => 'text-slate-500 bg-slate-50 border-slate-100',
                                                'medium' => 'text-amber-600 bg-amber-50 border-amber-100',
                                                'high' => 'text-rose-600 bg-rose-50 border-rose-100',
                                            ];
                                        @endphp
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded border {{ $priorityColors[$task->priority] ?? 'text-slate-500' }}">
                                            {{ $task->priority }}
                                        </span>
                                        
                                        @if($canManageTasks)
                                            <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute top-2 right-2 flex gap-1 bg-white p-1 rounded-lg shadow-sm">
                                                <a href="{{ route('tasks.edit', $task) }}" class="p-1.5 text-slate-400 hover:text-accent hover:bg-slate-50 rounded-md" onclick="event.stopPropagation()">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    <h5 class="text-sm font-bold text-slate-800 mb-2 leading-snug group-hover:text-accent transition-colors">
                                        {{ $task->title }}
                                    </h5>
                                    
                                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-50">
                                        {{-- Assignee --}}
                                        @if($task->assignee)
                                            <div class="flex items-center gap-2" title="{{ __('Assigned to') }} {{ $task->assignee->name }}">
                                                <div class="h-6 w-6 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center text-[10px] font-bold">
                                                    {{ strtoupper(substr($task->assignee->name, 0, 2)) }}
                                                </div>
                                                 <span class="text-xs text-slate-500 font-medium truncate max-w-[80px]">{{ $task->assignee->name }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-1.5 text-slate-400">
                                                 <div class="h-6 w-6 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/></svg>
                                                 </div>
                                                <span class="text-[10px] italic">{{ __('Unassigned') }}</span>
                                            </div>
                                        @endif

                                        {{-- Due Date --}}
                                        @if($task->due_date)
                                             <div class="flex items-center gap-1.5 {{ $task->due_date->isPast() && $task->status !== 'done' ? 'text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded' : 'text-slate-400' }}" title="{{ __('Due date') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                <span class="text-[10px] font-semibold">{{ $task->due_date->format('M d') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Stats --}}
                                    <div class="mt-2 flex items-center gap-3 text-slate-400">
                                        @if($task->subtasks_count > 0)
                                            <div class="flex items-center gap-1 text-[10px]" title="{{ __('Subtasks') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                                <span>{{ $task->subtasks->where('is_completed', true)->count() }}/{{ $task->subtasks_count }}</span>
                                            </div>
                                        @endif
                                        @if($task->comments_count > 0)
                                            <div class="flex items-center gap-1 text-[10px]" title="{{ __('Comments') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                                <span>{{ $task->comments_count }}</span>
                                            </div>
                                        @endif
                                         @if($task->attachments_count > 0)
                                            <div class="flex items-center gap-1 text-[10px]" title="{{ __('Attachments') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                                                <span>{{ $task->attachments_count }}</span>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            @empty
                                <div class="text-center py-8 px-4 border-2 border-dashed border-slate-200 rounded-xl">
                                    <p class="text-xs text-slate-400 font-medium">{{ __('No tasks') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <script>
        function drag(ev, taskId) {
            ev.dataTransfer.setData("taskId", taskId);
            ev.dataTransfer.effectAllowed = "move";
        }

        function allowDrop(ev) {
            ev.preventDefault();
            ev.dataTransfer.dropEffect = "move";
        }

        function drop(ev, newStatus) {
            ev.preventDefault();
            const taskId = ev.dataTransfer.getData("taskId");
            const draggedElement = document.querySelector(`[ondragstart="drag(event, '${taskId}')"]`);
            
            // Optimistic UI update
            if (draggedElement) {
                const targetColumn = ev.currentTarget;
                if (targetColumn === draggedElement.parentElement) return;
                
                targetColumn.appendChild(draggedElement);
                
                // Update counter logic (simple visual update)
                const oldColumn = draggedElement.parentElement;
                // Note: updating counters properly requires finding specific elements which is complex without a framework like Vue/React/Alpine
                // For now, we rely on page reload on error or just let it be until refresh
            }

            // AJAX request to update status
            fetch(`/tasks/${taskId}`, {
                method: 'POST', // Method spoofing for PUT
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'PATCH',
                    status: newStatus
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Task updated:', data);
                // Optionally show a toast notification
            })
            .catch(error => {
                console.error('Error updating task:', error);
                alert('{{ __("Failed to update task status.") }}');
                window.location.reload(); // Revert changes on error
            });
        }
    </script>
</x-app-layout>
