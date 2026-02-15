@if ($errors->any())
    <div class="card p-4 mb-6 text-sm text-rose-700 bg-rose-50">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-4">
    <div>
        <label class="text-sm font-medium text-slate-600">{{ __('Task title') }}</label>
        <input type="text" name="title" value="{{ old('title', $task->title ?? '') }}" class="mt-2 w-full rounded-xl border-slate-200" required>
    </div>

    <div>
        <label class="text-sm font-medium text-slate-600">{{ __('Description') }}</label>
        <textarea name="description" rows="4" class="mt-2 w-full rounded-xl border-slate-200">{{ old('description', $task->description ?? '') }}</textarea>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-600">{{ __('Status') }}</label>
            <select name="status" class="mt-2 w-full rounded-xl border-slate-200" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $task->status ?? '') === $status)>{{ __(ucwords(str_replace('_', ' ', $status))) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600">{{ __('Priority') }}</label>
            <select name="priority" class="mt-2 w-full rounded-xl border-slate-200" required>
                @foreach ($priorities as $priority)
                    <option value="{{ $priority }}" @selected(old('priority', $task->priority ?? '') === $priority)>{{ __(ucwords($priority)) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-600">{{ __('Due date') }}</label>
            <input type="date" name="due_date" value="{{ old('due_date', optional($task->due_date ?? null)->format('Y-m-d')) }}" class="mt-2 w-full rounded-xl border-slate-200">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600">{{ __('Assignee') }}</label>
            <select name="assignee_id" class="mt-2 w-full rounded-xl border-slate-200">
                <option value="">{{ __('Unassigned') }}</option>
                @foreach ($users as $assignee)
                    <option value="{{ $assignee->id }}" @selected(old('assignee_id', $task->assignee_id ?? '') == $assignee->id)>{{ $assignee->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('projects.show', $project) }}" class="btn-secondary">{{ __('Cancel') }}</a>
</div>
