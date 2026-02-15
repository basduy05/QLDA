<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">{{ __('Workspace') }}</p>
                <h2 class="text-3xl font-semibold text-slate-900">{{ __('Projects') }}</h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('exports.projects') }}" class="btn-secondary">{{ __('Export XLSX') }}</a>
                <a href="{{ route('projects.create') }}" class="btn-primary">{{ __('New project') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="card p-4 text-sm text-emerald-700 bg-emerald-50">
                {{ session('status') }}
            </div>
        @endif

        <div class="card-strong p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left table-head">
                        <tr>
                            <th class="py-3">{{ __('Project') }}</th>
                            <th class="py-3">{{ __('Owner') }}</th>
                            <th class="py-3">{{ __('Status') }}</th>
                            <th class="py-3">{{ __('Tasks') }}</th>
                            <th class="py-3">{{ __('Timeline') }}</th>
                            <th class="py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($projects as $project)
                            <tr>
                                <td class="py-4">
                                    <p class="font-semibold text-slate-900">{{ $project->name }}</p>
                                    <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($project->description, 80) }}</p>
                                </td>
                                <td class="py-4 text-slate-600">{{ $project->owner?->name }}</td>
                                <td class="py-4">
                                    <span class="badge bg-slate-100 text-slate-700">{{ __(ucwords(str_replace('_', ' ', $project->status))) }}</span>
                                </td>
                                <td class="py-4 text-slate-600">{{ $project->tasks_count }}</td>
                                <td class="py-4 text-slate-600">
                                    {{ $project->start_date?->format('d/m/Y') ?? '—' }}
                                    <span class="text-slate-400">→</span>
                                    {{ $project->end_date?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="py-4 text-right">
                                    <a href="{{ route('projects.show', $project) }}" class="text-slate-600">{{ __('Open') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-500">{{ __('No projects created yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
