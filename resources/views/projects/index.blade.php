<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">{{ __('Workspace') }}</p>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('Projects') }}</h2>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('exports.projects') }}" class="btn-secondary inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    {{ __('Export XLSX') }}
                </a>
                <a href="{{ route('projects.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                    {{ __('New project') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">


        <div class="card-strong overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <form method="GET" action="{{ route('projects.index') }}" class="relative w-full sm:max-w-xs">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search projects...') }}" class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent sm:text-sm transition-shadow">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-4 text-left">{{ __('Project') }}</th>
                            <th class="px-6 py-4 text-left">{{ __('Owner') }}</th>
                            <th class="px-6 py-4 text-left">{{ __('Status') }}</th>
                            <th class="px-6 py-4 text-center">{{ __('Tasks') }}</th>
                            <th class="px-6 py-4 text-left">{{ __('Timeline') }}</th>
                            <th class="px-6 py-4 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($projects as $project)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-4 whitespace-normal min-w-[250px]">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-accent/10 to-accent/5 border border-accent/10 flex items-center justify-center text-accent font-bold text-lg shrink-0">
                                            {{ strtoupper(substr($project->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('projects.show', $project) }}" class="font-semibold text-slate-900 hover:text-accent transition-colors">{{ $project->name }}</a>
                                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-1" title="{{ $project->description }}">{{ \Illuminate\Support\Str::limit($project->description, 60) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-medium text-slate-600 shrink-0">
                                            {{ strtoupper(substr($project->owner?->name ?? '?', 0, 2)) }}
                                        </div>
                                        <span class="text-slate-700 font-medium">{{ $project->owner?->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'planning' => 'bg-sky-100 text-sky-700 border-sky-200',
                                            'active' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                            'on_hold' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            'completed' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        ];
                                        $colorClass = $statusColors[$project->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $colorClass }}">
                                        {{ __(ucwords(str_replace('_', ' ', $project->status))) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                                        {{ $project->tasks_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 text-xs">
                                    <span>{{ $project->start_date?->format('d/m/Y') ?? '—' }}</span>
                                    <span class="text-slate-300 mx-1">→</span>
                                    <span>{{ $project->end_date?->format('d/m/Y') ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-accent hover:bg-accent/5 transition-colors" title="{{ __('Open') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-16 w-16 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h18M3 12h18M3 17h12"/></svg>
                                        </div>
                                        <h3 class="text-sm font-medium text-slate-900 mb-1">{{ __('No projects found') }}</h3>
                                        <p class="text-sm text-slate-500 mb-4 max-w-sm">{{ request('search') ? __('We couldn\'t find any projects matching your search.') : __('Get started by creating a new project to organize your team\'s work.') }}</p>
                                        @if(!request('search'))
                                            <a href="{{ route('projects.create') }}" class="btn-primary inline-flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                                                {{ __('New project') }}
                                            </a>
                                        @else
                                            <a href="{{ route('projects.index') }}" class="btn-secondary">{{ __('Clear search') }}</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($projects->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
