<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 w-full">
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ __('Projects') }}</h2>
                <p class="text-sm text-slate-500 mt-0.5">{{ __('Manage and track all your projects') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('exports.projects') }}" class="btn-secondary inline-flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="7 10 12 15 17 10" />
                        <line x1="12" x2="12" y1="15" y2="3" />
                    </svg>
                    {{ __('Export') }}
                </a>
                <a href="{{ route('projects.create') }}" class="btn-primary inline-flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" x2="12" y1="5" y2="19" />
                        <line x1="5" x2="19" y1="12" y2="12" />
                    </svg>
                    {{ __('New project') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="card-strong overflow-hidden">
            <div
                class="p-4 border-b border-slate-100 bg-slate-50/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <form method="GET" action="{{ route('projects.index') }}" class="relative w-full sm:max-w-xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" x2="16.65" y1="21" y2="16.65" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Search projects...') }}"
                        class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-shadow">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50/50">
                        <tr class="text-slate-500 uppercase tracking-wider text-xs font-bold">
                            <th class="px-5 py-3.5 text-left">{{ __('Project') }}</th>
                            <th class="px-5 py-3.5 text-left">{{ __('Owner') }}</th>
                            <th class="px-5 py-3.5 text-left">{{ __('Status') }}</th>
                            <th class="px-5 py-3.5 text-center">{{ __('Tasks') }}</th>
                            <th class="px-5 py-3.5 text-left">{{ __('Timeline') }}</th>
                            <th class="px-5 py-3.5 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($projects as $project)
                            <tr class="hover:bg-indigo-50/30 transition-colors group">
                                <td class="px-5 py-4 whitespace-normal min-w-[250px]">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shrink-0"
                                            style="background: var(--gradient);">
                                            {{ strtoupper(substr($project->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('projects.show', $project) }}"
                                                class="font-semibold text-slate-900 hover:text-indigo-600 transition-colors">{{ $project->name }}</a>
                                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-1"
                                                title="{{ $project->description }}">
                                                {{ \Illuminate\Support\Str::limit($project->description, 60) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="h-7 w-7 rounded-lg bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600 shrink-0">
                                            {{ strtoupper(substr($project->owner?->name ?? '?', 0, 2)) }}
                                        </div>
                                        <span class="text-slate-700 font-medium text-sm">{{ $project->owner?->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $statusColors = [
                                            'planning' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'on_hold' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'completed' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        ];
                                        $colorClass = $statusColors[$project->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $colorClass }}">
                                        {{ __(ucwords(str_replace('_', ' ', $project->status))) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex items-center justify-center h-7 min-w-[1.75rem] px-2 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">
                                        {{ $project->tasks_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-500 text-xs">
                                    <div class="flex flex-col gap-0.5">
                                        <span>{{ $project->start_date?->format('d/m/Y H:i') ?? '—' }}</span>
                                        <span class="text-slate-300">↓</span>
                                        <span>{{ $project->end_date?->format('d/m/Y H:i') ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('projects.show', $project) }}"
                                        class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                        title="{{ __('Open') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="m9 18 6-6-6-6" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="empty-state-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7"
                                                style="color: var(--accent);" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.5">
                                                <path
                                                    d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-900 mb-1">{{ __('No projects found') }}</h3>
                                        <p class="text-sm text-slate-500 mb-5 max-w-sm">
                                            {{ request('search') ? __('We couldn\'t find any projects matching your search.') : __('Get started by creating a new project to organize your team\'s work.') }}
                                        </p>
                                        @if(!request('search'))
                                            <a href="{{ route('projects.create') }}"
                                                class="btn-primary inline-flex items-center gap-2 text-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2">
                                                    <line x1="12" x2="12" y1="5" y2="19" />
                                                    <line x1="5" x2="19" y1="12" y2="12" />
                                                </svg>
                                                {{ __('New project') }}
                                            </a>
                                        @else
                                            <a href="{{ route('projects.index') }}"
                                                class="btn-secondary">{{ __('Clear search') }}</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($projects->hasPages())
                <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/30">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>