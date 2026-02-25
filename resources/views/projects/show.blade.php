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

        <div class="card-strong p-6 mt-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    {{ __('Project tasks') }}
                    <span class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full bg-slate-100 text-slate-600 text-xs font-medium ml-1">
                        {{ $project->tasks->count() }}
                    </span>
                </h3>
                @if ($canManageTasks)
                    <a href="{{ route('projects.tasks.create', $project) }}" class="btn-primary bg-emerald-700 hover:bg-emerald-800 border-emerald-700 text-sm py-2 px-4 rounded-full inline-flex items-center gap-1.5 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                        {{ __('New task') }}
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto -mx-6 px-6 sm:mx-0 sm:px-0 custom-scrollbar pb-2">
                <table class="min-w-full text-sm whitespace-nowrap">
                    <thead class="bg-slate-50/50 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-4 text-left pl-6">{{ __('Task') }}</th>
                            <th class="px-4 py-4 text-left">{{ __('Assignee') }}</th>
                            <th class="px-4 py-4 text-left">{{ __('Status') }}</th>
                            <th class="px-4 py-4 text-left">{{ __('Priority') }}</th>
                            <th class="px-4 py-4 text-center">{{ __('Due date') }}</th>
                            <th class="px-4 py-4 text-right pr-6"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 bg-white">
                        @forelse ($project->tasks as $task)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-4 py-4 whitespace-normal min-w-[250px] pl-6">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 shrink-0 text-emerald-500">
                                            @if($task->status == 'completed')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-emerald-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300 hover:text-emerald-500 transition-colors cursor-pointer" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('tasks.show', $task) }}" class="text-sm font-bold text-slate-800 hover:text-accent transition-colors block mb-0.5 {{ $task->status == 'completed' ? 'line-through text-slate-400' : '' }}">
                                                {{ $task->title }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($task->assignee)
                                        <div class="flex items-center gap-2">
                                            <div class="h-6 w-6 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600 shrink-0">
                                                {{ strtoupper(substr($task->assignee->name, 0, 2)) }}
                                            </div>
                                            <span class="text-slate-700 font-medium text-xs">{{ $task->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs italic">{{ __('Unassigned') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $statusColors = [
                                            'todo' => 'bg-slate-100 text-slate-700 border-slate-200',
                                            'in_progress' => 'bg-sky-100 text-sky-700 border-sky-200',
                                            'done' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        ];
                                        $colorClass = $statusColors[$task->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $colorClass }}">
                                        {{ __(ucwords(str_replace('_', ' ', $task->status))) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-1.5">
                                        @if($task->priority == 'high')
                                            <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                                            <span class="text-rose-700 font-medium text-xs">{{ __('High') }}</span>
                                        @elseif($task->priority == 'medium')
                                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                            <span class="text-amber-700 font-medium text-xs">{{ __('Medium') }}</span>
                                        @else
                                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                            <span class="text-emerald-700 font-medium text-xs">{{ __('Low') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($task->due_date)
                                        <span class="text-xs font-medium {{ $task->due_date < now() && $task->status != 'completed' ? 'text-rose-600 bg-rose-50 px-2 py-1 rounded' : 'text-slate-600' }}">
                                            {{ $task->due_date->format('M d') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right pr-6">
                                    <a href="{{ route('tasks.show', $task) }}" class="text-slate-400 hover:text-accent p-1 rounded-full hover:bg-slate-50 transition-colors inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                        <p>{{ __('No tasks found') }}</p>
                                        @if ($canManageTasks)
                                            <a href="{{ route('projects.tasks.create', $project) }}" class="text-accent hover:underline text-sm font-medium mt-1">{{ __('Create your first task') }}</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
