<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-slate-500">{{ __('Communication') }}</p>
            <h2 class="text-3xl font-semibold text-slate-900">{{ __('Chat Groups') }}</h2>
            <p class="text-sm text-slate-500 mt-2">{{ __('Messages are automatically removed after 24 hours.') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="card p-4 text-sm text-emerald-700 bg-emerald-50">
                {{ session('status') }}
            </div>
        @endif

        <div class="card-strong p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">{{ __('Create Group') }}</h3>
            <form method="POST" action="{{ route('chat-groups.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-medium text-slate-600">{{ __('Group name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="mt-2 w-full rounded-xl border-slate-200" required>
                    @error('name')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600">{{ __('Members') }}</label>
                    <select name="member_ids[]" multiple class="mt-2 w-full rounded-xl border-slate-200 min-h-32">
                        @foreach ($users as $member)
                            <option value="{{ $member->id }}" @selected(collect(old('member_ids', []))->contains($member->id))>
                                {{ $member->name }} ({{ $member->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-2">{{ __('Hold Ctrl / Cmd to select multiple users.') }}</p>
                </div>

                <div>
                    <button type="submit" class="btn-primary">{{ __('Create Group') }}</button>
                </div>
            </form>
        </div>

        <div class="card-strong p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">{{ __('Available Groups') }}</h3>
            <div class="grid gap-4 md:grid-cols-2">
                @forelse ($groups as $group)
                    <a href="{{ route('chat-groups.show', $group) }}" class="card p-4 hover:border-slate-300 transition">
                        <p class="font-semibold text-slate-900">{{ $group->name }}</p>
                        <p class="text-sm text-slate-500 mt-1">
                            {{ __('Created by') }} {{ $group->creator?->name }}
                        </p>
                        <p class="text-sm text-slate-500 mt-1">
                            {{ $group->members->count() }} {{ __('members') }} · {{ $group->messages_count }} {{ __('messages') }}
                        </p>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">{{ __('No groups yet.') }}</p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $groups->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
