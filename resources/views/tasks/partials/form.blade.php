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
            <input type="datetime-local" name="due_date" value="{{ old('due_date', optional($task->due_date ?? null)->format('Y-m-d\TH:i')) }}" class="mt-2 w-full rounded-xl border-slate-200">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600">{{ __('Assignee') }}</label>
            <div class="flex items-center gap-2 mt-2">
                <select name="assignee_id" id="assignee-select" class="flex-1 rounded-xl border-slate-200">
                    <option value="">{{ __('Unassigned') }}</option>
                    @foreach ($users as $assignee)
                        <option value="{{ $assignee->id }}" @selected(old('assignee_id', $task->assignee_id ?? '') == $assignee->id)>{{ $assignee->name }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="aiSuggestAssignee()" id="ai-assign-btn" class="shrink-0 bg-gradient-to-r from-violet-500 to-purple-600 text-white px-3 py-2.5 rounded-xl hover:from-violet-600 hover:to-purple-700 transition-all flex items-center gap-1 text-xs shadow-sm" title="{{ __('AI suggest assignee') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a4 4 0 0 0-4 4c0 2 2 3 2 6H8a2 2 0 0 0-2 2v1h12v-1a2 2 0 0 0-2-2h-2c0-3 2-4 2-6a4 4 0 0 0-4-4z"/><path d="M10 18h4"/><path d="M10 22h4"/></svg>
                    AI
                </button>
            </div>
            <div id="ai-assign-result" style="display:none" class="mt-2 p-2 bg-violet-50 border border-violet-200 rounded-lg text-xs text-violet-800"></div>
        </div>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('projects.show', $project) }}" class="btn-secondary">{{ __('Cancel') }}</a>
</div>

<script>
function aiSuggestAssignee() {
    const btn = document.getElementById('ai-assign-btn');
    const result = document.getElementById('ai-assign-result');
    const title = document.querySelector('input[name="title"]')?.value || '';
    const description = document.querySelector('textarea[name="description"]')?.value || '';
    const priority = document.querySelector('select[name="priority"]')?.value || 'medium';

    if (!title.trim()) {
        result.style.display = 'block';
        result.innerHTML = '<span class="text-amber-600">{{ __("Please enter a task title first.") }}</span>';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin inline-block h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>';
    result.style.display = 'none';

    fetch("{{ route('ai.suggest-assignee') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ project_id: {{ $project->id }}, title, description, priority })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a4 4 0 0 0-4 4c0 2 2 3 2 6H8a2 2 0 0 0-2 2v1h12v-1a2 2 0 0 0-2-2h-2c0-3 2-4 2-6a4 4 0 0 0-4-4z"/><path d="M10 18h4"/><path d="M10 22h4"/></svg> AI';
        if (data.ok) {
            const select = document.getElementById('assignee-select');
            select.value = data.user_id;
            result.style.display = 'block';
            result.innerHTML = `<strong>${data.user_name}</strong> — ${data.reason.replace(/</g, '&lt;')}`;
        } else {
            result.style.display = 'block';
            result.innerHTML = '<span class="text-rose-600">' + (data.message || '{{ __("AI unavailable") }}') + '</span>';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a4 4 0 0 0-4 4c0 2 2 3 2 6H8a2 2 0 0 0-2 2v1h12v-1a2 2 0 0 0-2-2h-2c0-3 2-4 2-6a4 4 0 0 0-4-4z"/><path d="M10 18h4"/><path d="M10 22h4"/></svg> AI';
        result.style.display = 'block';
        result.innerHTML = '<span class="text-rose-600">{{ __("Connection failed.") }}</span>';
    });
}
</script>
