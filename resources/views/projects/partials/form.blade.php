@csrf

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
        <label class="text-sm font-medium text-slate-600">{{ __('Project name') }}</label>
        <input type="text" name="name" value="{{ old('name', $project->name ?? '') }}" class="mt-2 w-full rounded-xl border-slate-200" placeholder="{{ __('E.g. Qhorizon rollout') }}" required>
    </div>

    <div>
        <label class="text-sm font-medium text-slate-600">{{ __('Description') }}</label>
        <textarea name="description" rows="4" class="mt-2 w-full rounded-xl border-slate-200" placeholder="{{ __('Project goals, milestones, and scope') }}">{{ old('description', $project->description ?? '') }}</textarea>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-600">{{ __('Status') }}</label>
            <select name="status" class="mt-2 w-full rounded-xl border-slate-200" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $project->status ?? '') === $status)>{{ __(ucwords(str_replace('_', ' ', $status))) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600">{{ __('Owner') }}</label>
            <select name="owner_id" class="mt-2 w-full rounded-xl border-slate-200" @disabled(!$isAdmin)>
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}" @selected(old('owner_id', $project->owner_id ?? auth()->id()) == $owner->id)>{{ $owner->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-600">{{ __('Start date') }}</label>
            <input type="date" name="start_date" value="{{ old('start_date', optional($project->start_date ?? null)->format('Y-m-d')) }}" class="mt-2 w-full rounded-xl border-slate-200">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600">{{ __('End date') }}</label>
            <input type="date" name="end_date" value="{{ old('end_date', optional($project->end_date ?? null)->format('Y-m-d')) }}" class="mt-2 w-full rounded-xl border-slate-200">
        </div>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('projects.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
</div>
