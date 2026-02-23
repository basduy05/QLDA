<x-app-layout>
    @php
        $roleLabels = [
            'lead' => __('Lead'),
            'deputy' => __('Deputy'),
            'member' => __('Member'),
        ];
    @endphp

    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm text-slate-500">{{ __('Project details') }}</p>
                <h2 class="text-3xl font-semibold text-slate-900">{{ $project->name }}</h2>
                <p class="text-xs text-slate-500 mt-1">{{ __('Your role') }}: <span class="font-semibold text-slate-700">{{ $roleLabels[$viewerRole] ?? __('Guest') }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                @if ($canUpdateProject)
                    <a href="{{ route('projects.edit', $project) }}" class="btn-secondary">{{ __('Edit') }}</a>
                @endif
                @if ($canManageMembers)
                    <form method="POST" action="{{ route('projects.destroy', $project) }}" data-confirm="{{ __('Delete this project?') }}" onsubmit="return confirm(this.dataset.confirm)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-secondary">{{ __('Delete') }}</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="card p-4 text-sm text-emerald-700 bg-emerald-50">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card-strong p-6 lg:col-span-2">
                <h3 class="text-xl font-semibold mb-4">{{ __('Overview') }}</h3>
                <p class="text-slate-600">{{ $project->description ?? __('No description provided.') }}</p>
                <div class="mt-4 grid gap-4 md:grid-cols-3 text-sm">
                    <div>
                        <p class="text-slate-500">{{ __('Owner') }}</p>
                        <p class="font-semibold text-slate-900">{{ $project->owner?->name }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">{{ __('Status') }}</p>
                        <p class="font-semibold text-slate-900">{{ __(ucwords(str_replace('_', ' ', $project->status))) }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">{{ __('Timeline') }}</p>
                        <p class="font-semibold text-slate-900">
                            {{ $project->start_date?->format('d/m/Y') ?? '—' }} → {{ $project->end_date?->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-strong p-6">
                <h3 class="text-xl font-semibold mb-4">{{ __('Quick actions') }}</h3>
                @if ($canManageTasks)
                    <a href="{{ route('projects.tasks.create', $project) }}" class="btn-primary w-full">{{ __('Add task') }}</a>
                @endif
                <a href="{{ route('projects.index') }}" class="btn-secondary w-full mt-3">{{ __('Back to projects') }}</a>
            </div>
        </div>

        <div class="card-strong p-6">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <h3 class="text-xl font-semibold">{{ __('Team members') }}</h3>
                @if ($canManageMembers)
                    <span class="text-xs text-slate-500">{{ __('Lead can assign deputy/member roles.') }}</span>
                @endif
            </div>

            <div class="space-y-2">
                <div class="rounded-xl border border-slate-200 p-3 bg-slate-50">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $project->owner?->name }}</p>
                            <p class="text-xs text-slate-500">{{ $project->owner?->email }}</p>
                        </div>
                        <span class="badge bg-slate-900 text-white">{{ $roleLabels['lead'] }}</span>
                    </div>
                </div>

                @foreach ($project->members->where('id', '!=', $project->owner_id)->sortBy('name') as $member)
                    <div class="rounded-xl border border-slate-200 p-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $member->name }}</p>
                                <p class="text-xs text-slate-500">{{ $member->email }}</p>
                            </div>

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
                                        <button type="submit" class="btn-secondary !px-3 text-xs">{{ __('Remove') }}</button>
                                    </form>
                                </div>
                            @else
                                <span class="badge bg-slate-100 text-slate-700">{{ $roleLabels[$member->pivot->role ?? 'member'] }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($canManageMembers && $availableUsers->isNotEmpty())
                <form method="POST" action="{{ route('projects.members.add', $project) }}" class="mt-4 grid gap-3 md:grid-cols-[1fr_180px_auto]">
                    @csrf
                    <select name="user_id" class="rounded-xl border-slate-200" required>
                        <option value="">{{ __('Select user') }}</option>
                        @foreach ($availableUsers as $memberOption)
                            <option value="{{ $memberOption->id }}">{{ $memberOption->name }} ({{ $memberOption->email }})</option>
                        @endforeach
                    </select>

                    <select name="role" class="rounded-xl border-slate-200" required>
                        @foreach (['deputy', 'member'] as $role)
                            <option value="{{ $role }}">{{ $roleLabels[$role] }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn-primary">{{ __('Add member') }}</button>
                </form>
            @endif
        </div>

        <div class="card-strong p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">{{ __('Tasks') }}</h3>
                @if ($canManageTasks)
                    <a href="{{ route('projects.tasks.create', $project) }}" class="btn-secondary">{{ __('New task') }}</a>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left table-head">
                        <tr>
                            <th class="py-3">{{ __('Task') }}</th>
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
                                    <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($task->description, 80) }}</p>
                                </td>
                                <td class="py-4 text-slate-600">{{ $task->assignee?->name ?? __('Unassigned') }}</td>
                                <td class="py-4">
                                    <span class="badge bg-slate-100 text-slate-700">{{ __(ucwords(str_replace('_', ' ', $task->status))) }}</span>
                                </td>
                                <td class="py-4 text-slate-600">{{ __(ucwords($task->priority)) }}</td>
                                <td class="py-4 text-slate-600">{{ $task->due_date?->format('d/m/Y') ?? '—' }}</td>
                                <td class="py-4 text-right">
                                    @if ($canManageTasks)
                                        <a href="{{ route('tasks.edit', $task) }}" class="text-slate-600">{{ __('Edit') }}</a>
                                    @else
                                        <a href="{{ route('tasks.show', $task) }}" class="text-slate-600">{{ __('View') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-500">{{ __('No tasks yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
