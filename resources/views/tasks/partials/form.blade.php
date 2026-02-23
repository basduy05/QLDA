@csrf

@if ($errors->any())
    <div class="p-4 mb-6 text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-lg">
        <ul class="list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Task title') }}</label>
        <input type="text" name="title" value="{{ old('title', $task->title ?? '') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="{{ __('Task title') }}" required>
        @error('title')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Description') }}</label>
        <textarea name="description" rows="4" class="w-full px-3 py-2 border border-slate-300 rounded-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="{{ __('Task details...') }}">{{ old('description', $task->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Status') }}</label>
            <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $task->status ?? '') === $status)>{{ __(ucwords(str_replace('_', ' ', $status))) }}</option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Priority') }}</label>
            <select name="priority" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @foreach ($priorities as $priority)
                    <option value="{{ $priority }}" @selected(old('priority', $task->priority ?? '') === $priority)>{{ __(ucwords($priority)) }}</option>
                @endforeach
            </select>
            @error('priority')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Due date') }}</label>
            <input type="date" name="due_date" value="{{ old('due_date', optional($task->due_date ?? null)->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('due_date')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Assignee') }}</label>
            <select name="assignee_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">{{ __('Unassigned') }}</option>
                @foreach ($users as $assignee)
                    <option value="{{ $assignee->id }}" @selected(old('assignee_id', $task->assignee_id ?? '') == $assignee->id)>{{ $assignee->name }}</option>
                @endforeach
            </select>
            @error('assignee_id')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
        {{ $submitLabel }}
    </button>
    <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 text-sm font-medium">
        {{ __('Cancel') }}
    </a>
</div>
