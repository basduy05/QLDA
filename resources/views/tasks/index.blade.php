<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900">{{ __('Tasks') }}</h1>
    </x-slot>

    <div class="space-y-4">
         <!-- Search Bar -->
        <form method="GET" action="{{ route('tasks.index') }}" class="mb-6">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="{{ __('Search tasks...') }}" 
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </form>

        <!-- Tasks Table -->
        <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">{{ __('Task') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">{{ __('Project') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">{{ __('Assignee') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">{{ __('Priority') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">{{ __('Due date') }}</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-slate-700">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($tasks as $task)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-slate-900 hover:text-blue-600
                                    {{ $task->status === 'done' ? 'line-through text-slate-500' : '' }}
                                ">
                                    {{ $task->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('projects.show', $task->project) }}" class="text-blue-600 hover:text-blue-700">
                                    {{ $task->project?->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @if($task->assignee)
                                    <span class="text-slate-700">{{ $task->assignee->name }}</span>
                                @else
                                    <span class="text-slate-500 italic">{{ __('—') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($task->status === 'done') bg-emerald-100 text-emerald-700
                                    @elseif($task->status === 'in_progress') bg-blue-100 text-blue-700
                                    @else bg-slate-100 text-slate-700
                                    @endif
                                ">
                                    {{ __(ucwords(str_replace('_', ' ', $task->status))) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($task->priority === 'high') bg-red-100 text-red-700
                                    @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-700
                                    @else bg-gray-100 text-gray-700
                                    @endif
                                ">
                                    {{ __(ucfirst($task->priority)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $task->due_date?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                {{ __('No tasks found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($tasks->hasPages())
            <div class="mt-6">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
