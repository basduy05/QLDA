<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-slate-500">{{ __('Workspace') }}</p>
            <h2 class="text-3xl font-semibold text-slate-900">{{ __('Tasks') }}</h2>
        </div>
    </x-slot>

    <div class="card-strong p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left table-head">
                    <tr>
                        <th class="py-3">{{ __('Task') }}</th>
                        <th class="py-3">{{ __('Project') }}</th>
                        <th class="py-3">{{ __('Assignee') }}</th>
                        <th class="py-3">{{ __('Status') }}</th>
                        <th class="py-3">{{ __('Priority') }}</th>
                        <th class="py-3">{{ __('Due date') }}</th>
                        <th class="py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tasks as $task)
                        <tr>
                            <td class="py-4">
                                <p class="font-semibold text-slate-900">{{ $task->title }}</p>
                                <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($task->description, 70) }}</p>
                            </td>
                            <td class="py-4 text-slate-600">{{ $task->project?->name }}</td>
                            <td class="py-4 text-slate-600">{{ $task->assignee?->name ?? __('Unassigned') }}</td>
                            <td class="py-4"><span class="badge bg-slate-100 text-slate-700">{{ __(ucwords(str_replace('_', ' ', $task->status))) }}</span></td>
                            <td class="py-4 text-slate-600">{{ __(ucwords($task->priority)) }}</td>
                            <td class="py-4 text-slate-600">{{ $task->due_date?->format('d/m/Y') ?? '—' }}</td>
                            <td class="py-4 text-right">
                                <a href="{{ route('tasks.show', $task) }}" class="text-slate-600">{{ __('Open') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-500">{{ __('No tasks found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
    </div>
</x-app-layout>
