<x-app-layout>
    <style>
        .progress-bar {
            width: var(--progress);
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
                <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                    {{ $project->name }}
                    @php
                        $statusColors = [
                            'planning' => 'bg-sky-100 text-sky-700',
                            'active' => 'bg-emerald-100 text-emerald-700',
                            'on_hold' => 'bg-amber-100 text-amber-700',
                            'completed' => 'bg-slate-100 text-slate-700',
                        ];
                        $colorClass = $statusColors[$project->status] ?? 'bg-slate-100 text-slate-700';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                        {{ __(ucwords(str_replace('_', ' ', $project->status))) }}
                    </span>
                </h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if ($canUpdateProject)
                    <a href="{{ route('projects.edit', $project) }}" class="px-3 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                        {{ __('Edit') }}
                    </a>
                @endif
                @if ($canManageMembers)
                    <form method="POST" action="{{ route('projects.destroy', $project) }}" data-confirm="{{ __('Delete this project?') }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2 text-sm border border-slate-300 rounded-lg text-rose-600 hover:bg-rose-50">
                            {{ __('Delete') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="p-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-3">
                {{ session('status') }}
            </div>
        @endif

        <!-- Project Info -->
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-lg p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4">{{ __('Overview') }}</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            {{ $project->description ?? __('No description provided.') }}
                        </p>
                    </div>
                    
                    <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-slate-200">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase mb-1">{{ __('Owner') }}</p>
                            <p class="font-semibold text-slate-900">{{ $project->owner?->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase mb-1">{{ __('Timeline') }}</p>
                            <p class="font-semibold text-slate-900">
                                {{ $project->start_date?->format('d/m/Y') ?? '—' }} 
                                <span class="text-slate-400 mx-1">→</span> 
                                {{ $project->end_date?->format('d/m/Y') ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="bg-white border border-slate-200 rounded-lg p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4">{{ __('Quick info') }}</h3>
                <div class="space-y-4">
                    @if ($canManageTasks)
                        <a href="{{ route('projects.tasks.create', $project) }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            {{ __('Add task') }}
                        </a>
                    @endif
                    
                    <div>
                        @php
                            $completedTasks = $project->tasks()->where('status', 'done')->count();
                            $progressPercentage = $project->tasks_count > 0 ? round(($completedTasks / $project->tasks_count) * 100) : 0;
                        @endphp
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-slate-700">{{ __('Progress') }}</span>
                            <span class="text-sm font-bold text-slate-900">{{ $progressPercentage }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full progress-bar" style="--progress: {{ $progressPercentage }}%"></div>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">{{ $completedTasks }} / {{ $project->tasks_count }} {{ __('tasks completed') }}</p>
                    </div>

                    <a href="{{ route('projects.index') }}" class="block w-full text-center px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 text-sm font-medium">
                        {{ __('Back to projects') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Team Members -->
        <div class="bg-white border border-slate-200 rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-900">{{ __('Team members') }} ({{ $project->members->count() }})</h3>
                @if ($canManageMembers)
                    <span class="text-xs font-medium text-slate-500 bg-slate-50 px-2 py-1 rounded">{{ __('Lead can manage members') }}</span>
                @endif
            </div>

            <div class="space-y-3 border-b border-slate-200 pb-6 mb-6">
                <!-- Project Owner -->
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">
                            {{ strtoupper(substr($project->owner?->name ?? '?', 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $project->owner?->name }}</p>
                            <p class="text-xs text-slate-500">{{ $project->owner?->email }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-medium bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ $roleLabels['lead'] }}</span>
                </div>
            </div>

            @if ($project->members->count() > 1)
                <div class="space-y-2 mb-6">
                    @foreach ($project->members->where('id', '!=', $project->owner_id)->sortBy('name') as $member)
                        <div class="flex items-center justify-between p-3 border border-slate-200 rounded-lg hover:bg-slate-50">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr($member->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $member->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $member->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @if ($canManageMembers)
                                    <form method="POST" action="{{ route('projects.members.update', [$project, $member]) }}" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="text-xs border border-slate-300 rounded px-2 py-1" onchange="this.form.requestSubmit()">
                                            @foreach (['deputy', 'member'] as $role)
                                                <option value="{{ $role }}" @selected(($member->pivot->role ?? 'member') === $role)>{{ $roleLabels[$role] }}</option>
                                            @endforeach
                                        </select>
                                    </form>

                                    <form method="POST" action="{{ route('projects.members.remove', [$project, $member]) }}" data-confirm="{{ __('Remove this member?') }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-rose-600 text-sm">✕</button>
                                    </form>
                                @else
                                    <span class="text-xs font-medium {{ $member->pivot->role === 'deputy' ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-700' }} px-2 py-1 rounded">
                                        {{ $roleLabels[$member->pivot->role] ?? $member->pivot->role }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($canManageMembers)
                <div class="border-t border-slate-200 pt-6">
                    <h4 class="text-sm font-bold text-slate-900 mb-3">{{ __('Add new member') }}</h4>
                    <form method="POST" action="{{ route('projects.members.add', $project) }}" class="flex flex-col sm:flex-row gap-2">
                        @csrf
                        <select name="user_id" required class="flex-1 text-sm border border-slate-300 rounded-lg px-3 py-2">
                            <option value="">{{ __('Select user...') }}</option>
                            @foreach ($availableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <select name="role" required class="text-sm border border-slate-300 rounded-lg px-3 py-2">
                            <option value="member">{{ $roleLabels['member'] }}</option>
                            <option value="deputy">{{ $roleLabels['deputy'] }}</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium whitespace-nowrap">
                            {{ __('Add') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Tasks Table -->
        <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">{{ __('Project tasks') }} ({{ $project->tasks->count() }})</h3>
                @if ($canManageTasks)
                    <a href="{{ route('projects.tasks.create', $project) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        {{ __('New task') }}
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">{{ __('Task') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">{{ __('Assignee') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">{{ __('Priority') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">{{ __('Due date') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($project->tasks as $task)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-slate-900 hover:text-blue-600 {{ $task->status === 'done' ? 'line-through text-slate-500' : '' }}">
                                        {{ $task->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-slate-700">
                                    {{ $task->assignee?->name ?? '—' }}
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
                                    <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                    {{ __('No tasks created yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
