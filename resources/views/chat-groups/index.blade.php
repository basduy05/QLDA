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
                    <div class="flex items-center justify-between gap-3">
                        <label class="text-sm font-medium text-slate-600">{{ __('Members') }}</label>
                        <p class="text-xs text-slate-500">
                            <span id="selected-members-count">0</span> {{ __('selected') }}
                        </p>
                    </div>
                    @php($selectedMembers = collect(old('member_ids', []))->map(fn ($id) => (int) $id)->all())
                    <input
                        id="member-search"
                        type="text"
                        class="mt-2 w-full rounded-xl border-slate-200"
                        placeholder="{{ __('Search by name or email...') }}"
                    >
                    <div id="member-list" class="mt-2 max-h-64 overflow-y-auto rounded-xl border border-slate-200 p-2 grid sm:grid-cols-2 gap-2">
                        @foreach ($users as $member)
                            <label data-member-text="{{ \Illuminate\Support\Str::lower($member->name.' '.$member->email) }}" class="flex items-start gap-3 rounded-lg border border-slate-200 px-3 py-2 cursor-pointer hover:border-slate-300">
                                <input
                                    type="checkbox"
                                    name="member_ids[]"
                                    data-member-checkbox="1"
                                    value="{{ $member->id }}"
                                    class="mt-1 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                                    @checked(in_array($member->id, $selectedMembers, true))
                                >
                                <span>
                                    <span class="block text-sm font-medium text-slate-800">{{ $member->name }}</span>
                                    <span class="block text-xs text-slate-500">{{ $member->email }}</span>
                                    <span class="block text-[11px] text-slate-400">{{ $member->activityStatusLabel() }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-500 mt-2">{{ __('Tick members to include them in this group.') }}</p>
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
                    <div class="card p-4 hover:border-slate-300 transition">
                        <a href="{{ route('messenger.group', $group) }}" class="block">
                            <p class="font-semibold text-slate-900">{{ $group->name }}</p>
                            <p class="text-sm text-slate-500 mt-1">
                                {{ __('Created by') }} {{ $group->creator?->name }}
                            </p>
                            <p class="text-sm text-slate-500 mt-1">
                                {{ $group->members->count() }} {{ __('members') }} · {{ $group->messages_count }} {{ __('messages') }}
                            </p>
                        </a>

                        @if ($isAdmin || (int) $group->created_by === (int) $authUserId)
                            <form method="POST" action="{{ route('chat-groups.destroy', $group) }}" class="mt-3">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:text-red-700">{{ __('Delete group') }}</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">{{ __('No groups yet.') }}</p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $groups->links() }}
            </div>
        </div>
    </div>

    <script>
        (function () {
            const searchInput = document.getElementById('member-search');
            const memberList = document.getElementById('member-list');
            const countNode = document.getElementById('selected-members-count');
            if (!memberList) {
                return;
            }

            const items = Array.from(memberList.querySelectorAll('label[data-member-text]'));

            const refreshCount = () => {
                if (!countNode) {
                    return;
                }

                const checked = memberList.querySelectorAll('input[data-member-checkbox]:checked').length;
                countNode.textContent = String(checked);
            };

            const applyFilter = () => {
                const keyword = (searchInput?.value || '').trim().toLowerCase();

                items.forEach((item) => {
                    const text = item.getAttribute('data-member-text') || '';
                    const visible = keyword === '' || text.includes(keyword);
                    item.classList.toggle('hidden', !visible);
                });
            };

            memberList.addEventListener('change', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLInputElement)) {
                    return;
                }

                if (target.matches('input[data-member-checkbox]')) {
                    refreshCount();
                }
            });

            searchInput?.addEventListener('input', applyFilter);

            refreshCount();
            applyFilter();
        })();
    </script>
</x-app-layout>
