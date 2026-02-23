<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900">{{ __('Projects') }}</h1>
            <a href="{{ route('projects.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                {{ __('New project') }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-700 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <!-- Search Bar -->
        <form method="GET" action="{{ route('projects.index') }}" class="mb-6">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="{{ __('Search projects...') }}" 
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </form>

        <!-- Projects Table -->
        <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">{{ __('Project') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">{{ __('Owner') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-slate-700">{{ __('Tasks') }}</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-slate-700">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($projects as $project)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('projects.show', $project) }}" class="font-semibold text-slate-900 hover:text-blue-600">
                                    {{ $project->name }}
                                </a>
                                <p class="text-sm text-slate-500 mt-1">{{ \Illuminate\Support\Str::limit($project->description, 60) ?? '—' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-slate-700">{{ $project->owner?->name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($project->status === 'planning') bg-sky-100 text-sky-700
                                    @elseif($project->status === 'active') bg-emerald-100 text-emerald-700
                                    @elseif($project->status === 'on_hold') bg-amber-100 text-amber-700
                                    @else bg-slate-100 text-slate-700
                                    @endif
                                ">
                                    {{ __(ucwords(str_replace('_', ' ', $project->status))) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-slate-700 font-medium">{{ $project->tasks_count ?? 0 }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                {{ __('No projects found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($projects->hasPages())
            <div class="mt-6">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
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
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        <span>{{ $project->start_date?->format('d/m/Y') ?? '—' }}</span>
                                        <span class="text-slate-300">→</span>
                                        <span>{{ $project->end_date?->format('d/m/Y') ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-accent hover:bg-accent/5 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100" title="{{ __('Open') }}">
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
