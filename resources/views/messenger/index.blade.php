<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm text-slate-500">{{ __('Messenger') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('Chats & Groups') }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="document.dispatchEvent(new CustomEvent('open-groups-modal'))" class="btn-secondary text-xs">{{ __('Manage Groups') }}</button>
                <a href="{{ route('ai.chat.index', ['popup' => request()->query('popup')]) }}" class="btn-secondary text-xs">{{ __('AI Assistant') }}</a>
                @if ($activeType === 'direct')
                    <button type="button" id="start-call-header" class="btn-primary text-xs">{{ __('Call') }}</button>
                @endif
            </div>
        </div>
    </x-slot>

    <div x-data="{ showGroupsSettings: false }" @open-groups-modal.window="showGroupsSettings = true">
        <!-- Groups Management Modal -->
        <div
            x-show="showGroupsSettings"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4"
            style="display: none;"
        >
            <div
                @click.outside="showGroupsSettings = false"
                class="w-full max-w-sm h-fit max-h-[85vh] bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col relative"
            >
                <div class="h-fit" x-data="{
                            groupName: '',
                            search: '',
                            selected: [],
                            users: {{ Js::from($contacts->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])) }},
                            toggle(id) {
                                if (this.selected.includes(id)) {
                                    this.selected = this.selected.filter(i => i !== id);
                                } else {
                                    this.selected.push(id);
                                }
                            },
                            get filteredUsers() {
                                if (this.search === '') return this.users;
                                const lower = this.search.toLowerCase();
                                return this.users.filter(u => 
                                    u.name.toLowerCase().includes(lower) || 
                                    u.email.toLowerCase().includes(lower)
                                );
                            },
                            get selectedUsers() {
                                return this.users.filter(u => this.selected.includes(u.id));
                            }
                        }">
                    <form method="POST" action="{{ route('chat-groups.store', ['popup' => request()->query('popup')]) }}" class="flex flex-col h-full">
                        @csrf
                        <input type="hidden" name="from_messenger" value="1">
                        
                        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50">
                            <div>
                                <h3 class="font-semibold text-sm text-slate-900">{{ __('Create Group') }}</h3>
                            </div>
                            <button type="button" @click="showGroupsSettings = false" class="text-slate-400 hover:text-slate-600 transition p-1 hover:bg-slate-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="flex-1 overflow-y-auto p-4 bg-white space-y-3">
                            <div>
                                <label class="block text-[10px] font-medium text-slate-500 mb-1 uppercase tracking-wider">{{ __('Name') }}</label>
                                <div class="flex gap-2">
                                    <input type="text" name="name" x-model="groupName" class="flex-1 rounded-md border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-xs py-1.5" placeholder="{{ __('Group Name') }}" required>
                                    <button type="submit" class="btn-primary text-xs !py-1.5 !px-3 shadow-sm shrink-0" :disabled="!groupName">
                                        {{ __('Create') }}
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-medium text-slate-500 mb-1 uppercase tracking-wider">{{ __('Members') }}</label>
                                <div class="flex rounded-md shadow-sm">
                                    <input type="text" x-model="search" class="flex-1 min-w-0 block w-full px-3 py-1.5 rounded-l-md border border-slate-200 text-xs focus:ring-indigo-500 focus:border-indigo-500" placeholder="{{ __('Search...') }}">
                                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-slate-200 bg-slate-50 text-slate-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </span>
                                </div>
                                
                                <!-- Selected Tags -->
                                <div class="flex flex-wrap gap-1 mt-2 min-h-[1.25rem] bg-slate-50/50 p-1 rounded-md border border-dashed border-slate-200" x-show="selected.length > 0">
                                    <template x-for="user in selectedUsers" :key="user.id">
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-white text-indigo-700 border border-indigo-100 shadow-sm">
                                            <span x-text="user.name"></span>
                                            <button type="button" @click="toggle(user.id)" class="text-indigo-400 hover:text-indigo-600 focus:outline-none ml-1">
                                                <svg class="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            </button>
                                            <input type="hidden" name="member_ids[]" :value="user.id">
                                        </span>
                                    </template>
                                    <button type="button" @click="selected = []" class="text-[10px] text-slate-400 hover:text-slate-600 underline ml-1 self-center" x-show="selected.length > 1">{{ __('Clear all') }}</button>
                                </div>

                                <!-- User List -->
                                <div class="mt-2 h-32 overflow-y-auto border border-slate-200 rounded-md divide-y divide-slate-50 bg-white shadow-sm scroll-smooth text-xs">
                                    <template x-for="user in filteredUsers" :key="user.id">
                                        <div @click="toggle(user.id)" class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50 cursor-pointer transition group select-none">
                                            <div class="flex-shrink-0 relative">
                                                <div class="w-4 h-4 rounded border flex items-center justify-center transition-colors duration-200" 
                                                        :class="selected.includes(user.id) ? 'bg-indigo-500 border-indigo-500' : 'border-slate-300 bg-white'">
                                                    <svg x-show="selected.includes(user.id)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-slate-700 truncate group-hover:text-indigo-700 transition-colors" x-text="user.name"></p>
                                                <p class="text-[10px] text-slate-400 truncate" x-text="user.email"></p>
                                            </div>
                                        </div>
                                    </template>
                                    <div x-show="filteredUsers.length === 0" class="p-4 text-center">
                                        <p class="text-xs text-slate-500 italic mb-1">{{ __('No members found') }}</p>
                                        <button type="button" @click="search = ''" class="text-[10px] text-indigo-500 hover:underline">{{ __('Show all') }}</button>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1 text-right" x-show="selected.length > 0">
                                    <span x-text="selected.length"></span> {{ __('selected') }}
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-strong p-0 overflow-hidden bg-gradient-to-br from-white to-slate-50/70 {{ request()->query('popup') ? 'h-screen rounded-none border-0' : '' }}">
        <div class="grid md:grid-cols-12 {{ request()->query('popup') ? 'h-full' : 'h-[calc(100vh-220px)]' }}">
            <aside class="md:col-span-4 lg:col-span-3 border-b md:border-b-0 md:border-r border-slate-100 p-3 overflow-y-auto min-h-0 max-h-64 md:max-h-none bg-white/70 backdrop-blur">
                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-500 mb-2">{{ __('Direct') }}</p>
                <div class="space-y-1 mb-4">
                    @foreach ($contacts as $contact)
                        @php($meta = $directMap->get($contact->id))
                        <a href="{{ route('messenger.direct', [$contact, 'popup' => request()->query('popup')]) }}" class="block rounded-xl px-3 py-2 border transition duration-200 hover:-translate-y-0.5 {{ $activeType === 'direct' && $activeTarget?->id === $contact->id ? 'border-slate-900 bg-slate-50 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-sm' }}">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-semibold text-sm text-slate-900">{{ $contact->name }}</p>
                                <span class="inline-flex h-2.5 w-2.5 rounded-full {{ $contact->isOnline() ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            </div>
                            <p class="text-[11px] text-slate-500">{{ \Illuminate\Support\Str::limit($meta['last_message'] ?? '', 34) }}</p>
                            <p class="text-[10px] text-slate-400">{{ $contact->activityStatusLabel() }}</p>
                        </a>
                    @endforeach
                </div>

                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-500">{{ __('Groups') }}</p>
                    <button type="button" @click="showGroupsSettings = true" class="text-[10px] font-medium text-accent hover:underline">{{ __('Create') }}</button>
                </div>
                <div class="space-y-1">
                    @foreach ($groups as $group)
                        <a href="{{ route('messenger.group', [$group, 'popup' => request()->query('popup')]) }}" class="block rounded-xl px-3 py-2 border transition duration-200 hover:-translate-y-0.5 {{ $activeType === 'group' && $activeTarget?->id === $group->id ? 'border-slate-900 bg-slate-50 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-sm' }}">
                            <p class="font-semibold text-sm text-slate-900">{{ $group->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ $group->messages_count }} {{ __('messages') }}</p>
                        </a>
                    @endforeach
                </div>
            </aside>

            <section class="md:col-span-8 lg:col-span-9 p-3 flex flex-col min-h-[45vh] md:min-h-0 relative bg-white/40" x-data="{ showGroupSettings: false }">
                @if ($activeType)
                    <div class="relative flex items-center justify-between pb-2 border-b border-slate-100">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $activeTarget->name }}</p>
                            @if ($activeType === 'direct')
                                <p class="text-[11px] text-slate-500">{{ $activeTarget->activityStatusLabel() }}</p>
                            @elseif ($activeType === 'group')
                                <p class="text-[11px] text-slate-500">{{ $groupMembers->count() }} {{ __('members') }}</p>
                            @endif
                            <p class="text-xs text-slate-500" id="typing-indicator">
                                {{ $activeType === 'direct' && $typing ? __('Typing...') : '' }}
                            </p>
                        </div>
                        @if ($activeType === 'direct')
                            <button type="button" id="start-call-inline" class="btn-secondary text-xs py-1.5 px-3">{{ __('Start Call') }}</button>
                        @elseif ($activeType === 'group')
                            <button
                                type="button"
                                @click="showGroupSettings = true"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 hover:border-slate-300 transition"
                                title="{{ __('Group options') }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="19" cy="12" r="1"></circle>
                                    <circle cx="5" cy="12" r="1"></circle>
                                </svg>
                            </button>
                        @endif
                    </div>

                    @if ($activeType === 'group')
                        @php($groupMemberIds = $groupMembers->pluck('id')->map(fn ($id) => (int) $id)->all())
                        
                        <!-- Group Settings Modal -->
                        <div x-show="showGroupSettings" 
                             style="display: none;"
                             class="fixed inset-0 z-50 overflow-y-auto" 
                             aria-labelledby="modal-title" role="dialog" aria-modal="true">
                             
                            <!-- Backdrop -->
                            <div x-show="showGroupSettings" 
                                 x-transition:enter="ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="fixed inset-0 bg-slate-500/75 transition-opacity backdrop-blur-sm" 
                                 @click="showGroupSettings = false"></div>

                            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                <div x-show="showGroupSettings" 
                                     x-transition:enter="ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave="ease-in duration-200"
                                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all w-full sm:my-8 sm:w-full sm:max-w-lg">
                                    
                                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-2">
                                            <h3 class="text-base font-semibold leading-6 text-slate-900" id="modal-title">{{ __('Group Settings') }}</h3>
                                            <button @click="showGroupSettings = false" class="text-slate-400 hover:text-slate-500">
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-1 custom-scrollbar">
                                            <!-- Rename Group -->
                                            <form method="POST" action="{{ route('messenger.group.rename', $activeTarget) }}">
                                                @csrf
                                                @method('PATCH')
                                                <label for="group-name" class="block text-sm font-medium leading-6 text-slate-900">{{ __('Group Name') }}</label>
                                                <div class="mt-1 flex rounded-md shadow-sm">
                                                    <input type="text" name="name" id="group-name" class="block w-full rounded-none rounded-l-md border-0 py-1.5 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" value="{{ old('name', $activeTarget->name) }}" required>
                                                    <button type="submit" class="relative -ml-px inline-flex items-center gap-x-1.5 rounded-r-md px-3 py-2 text-sm font-semibold text-slate-900 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
                                                        {{ __('Rename') }}
                                                    </button>
                                                </div>
                                            </form>

                                            <!-- Members Management -->
                                            <div class="border-t border-slate-100 pt-4">
                                                <h4 class="text-sm font-medium leading-6 text-slate-900 mb-2">{{ __('Manage Members') }}</h4>
                                                <form method="POST" action="{{ route('messenger.group.members', $activeTarget) }}" 
                                                    class="space-y-4"
                                                    x-data="{
                                                        search: '',
                                                        selected: {{ Js::from($groupMemberIds) }},
                                                        users: {{ Js::from($contacts->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])->merge($groupMembers->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email]))->unique('id')->values()) }},
                                                        toggle(id) {
                                                            if (this.selected.includes(id)) {
                                                                this.selected = this.selected.filter(i => i !== id);
                                                            } else {
                                                                this.selected.push(id);
                                                            }
                                                        },
                                                        get filteredUsers() {
                                                            if (this.search === '') return this.users;
                                                            const lower = this.search.toLowerCase();
                                                            return this.users.filter(u => 
                                                                u.name.toLowerCase().includes(lower) || 
                                                                u.email.toLowerCase().includes(lower)
                                                            );
                                                        },
                                                        get selectedUsers() {
                                                            return this.users.filter(u => this.selected.includes(u.id));
                                                        }
                                                    }">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="member_ids[]" value="{{ auth()->id() }}">
                                                    
                                                    <div class="relative">
                                                        <input type="text" x-model="search" class="block w-full rounded-md border-0 py-1.5 pl-10 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="{{ __('Search by name or email...') }}">
                                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    </div>

                                                    <!-- Selected Tags -->
                                                    <div class="flex flex-wrap gap-2" x-show="selected.length > 0">
                                                        <template x-for="user in selectedUsers" :key="user.id">
                                                            <span class="inline-flex items-center gap-x-0.5 rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                                                                <span x-text="user.name"></span>
                                                                <button type="button" @click="toggle(user.id)" class="group relative -mr-1 h-3.5 w-3.5 rounded-sm hover:bg-indigo-600/20">
                                                                    <span class="sr-only">Remove</span>
                                                                    <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-indigo-700/50 group-hover:stroke-indigo-700/75">
                                                                        <path d="M4 4l6 6m0-6l-6 6" />
                                                                    </svg>
                                                                </button>
                                                                <input type="hidden" name="member_ids[]" :value="user.id">
                                                            </span>
                                                        </template>
                                                        <span class="text-xs text-slate-500 self-center" x-show="selected.length > 0">
                                                            <span x-text="selected.length"></span> {{ __('selected') }}
                                                        </span>
                                                    </div>

                                                    <!-- User List -->
                                                    <div class="max-h-60 overflow-y-auto rounded-md border border-slate-200 bg-white shadow-sm custom-scrollbar">
                                                        <template x-for="user in filteredUsers" :key="user.id">
                                                            <div @click="toggle(user.id)" class="relative flex cursor-pointer select-none items-center px-4 py-3 hover:bg-slate-50 transition border-b border-slate-50 last:border-0">
                                                                <div class="flex h-5 items-center">
                                                                    <input type="checkbox" :checked="selected.includes(user.id)" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600">
                                                                </div>
                                                                <div class="ml-3 text-sm leading-6">
                                                                    <label class="font-medium text-slate-900" x-text="user.name"></label>
                                                                    <p class="text-slate-500" x-text="user.email"></p>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <div x-show="filteredUsers.length === 0" class="p-8 text-center">
                                                            <svg class="mx-auto h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                                            </svg>
                                                            <p class="mt-2 text-sm text-slate-500">{{ __('No members found.') }}</p>
                                                            <button type="button" @click="search = ''" class="mt-1 text-sm text-indigo-600 hover:text-indigo-500">{{ __('View all members') }}</button>
                                                        </div>
                                                    </div>

                                                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-lg -mx-6 -mb-6 mt-4 border-t border-slate-100">
                                                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">{{ __('Save Changes') }}</button>
                                                        <button type="button" @click="showGroupSettings = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">{{ __('Cancel') }}</button>
                                                    </div>
                                                </form>
                                            </div>

                                            @if ($canDeleteGroup)
                                                <div class="border-t border-slate-100 pt-6 mt-6">
                                                    <div class="rounded-md bg-red-50 p-4">
                                                        <div class="flex">
                                                            <div class="flex-shrink-0">
                                                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                                                </svg>
                                                            </div>
                                                            <div class="ml-3">
                                                                <h3 class="text-sm font-medium text-red-800">{{ __('Delete Group') }}</h3>
                                                                <div class="mt-2 text-sm text-red-700">
                                                                    <p>{{ __('Once you delete a group, there is no going back. Please be certain.') }}</p>
                                                                </div>
                                                                <div class="mt-4">
                                                                    <form method="POST" action="{{ route('chat-groups.destroy', $activeTarget) }}">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="rounded-md bg-red-50 px-2.5 py-1.5 text-sm font-semibold text-red-800 shadow-sm hover:bg-red-100 ring-1 ring-inset ring-red-300">{{ __('Delete this group') }}</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="messenger-feed" class="flex-1 overflow-y-auto py-3 pr-1 space-y-2 scroll-smooth">
                        @if ($activeType === 'direct')
                            @include('messenger.partials.direct-feed', ['messages' => $messages, 'authUserId' => auth()->id()])
                        @else
                            @include('messenger.partials.group-feed', ['messages' => $messages, 'authUserId' => auth()->id(), 'nicknames' => $groupNicknames])
                        @endif
                    </div>

                    <form id="composer-form" method="POST" enctype="multipart/form-data" action="{{ $activeType === 'direct' ? route('messenger.send-direct', $activeTarget) : route('messenger.send-group', $activeTarget) }}" class="pt-2 border-t border-slate-100 bg-white/70 backdrop-blur rounded-2xl px-2 pb-2 space-y-2">
                        @csrf
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-2">
                            <textarea id="composer" name="body" rows="2" class="w-full rounded-xl border-slate-200 text-sm" placeholder="{{ __('Type a message...') }}">{{ old('body') }}</textarea>
                            <button id="composer-submit" type="submit" class="btn-primary sm:shrink-0 text-xs py-1.5 px-4 h-fit">{{ __('Send') }}</button>
                        </div>
                        <div class="flex items-center gap-2">
                            <input id="composer-attachment" type="file" name="attachment" class="w-full text-xs text-slate-500 file:mr-2 file:rounded-full file:border-0 file:bg-slate-900 file:px-2.5 file:py-1 file:text-[10px] file:font-semibold file:text-white hover:file:bg-slate-700">
                        </div>
                        @error('body')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                        @error('attachment')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                        <p id="composer-error" class="text-sm text-red-600 mt-2 hidden"></p>
                        <p class="text-[11px] text-slate-400 mt-1">{{ __('Enter to send · Ctrl+Enter for new line') }}</p>
                    </form>
                @else
                    <div class="h-full flex items-center justify-center text-sm text-slate-500">
                        {{ __('Choose a chat or group to start messaging.') }}
                    </div>
                @endif
            </section>
        </div>
    </div>

    @if ($activeType === 'direct')
           <div id="call-data"
               class="hidden"
               data-contact-id="{{ $activeTarget->id }}"
               data-auth-id="{{ auth()->id() }}"
               data-ice='@json(config("webrtc.ice_servers"))'></div>

        <div id="call-modal" class="fixed inset-0 z-50 hidden bg-slate-900/75 p-4 backdrop-blur-sm">
            <div class="mx-auto h-full max-w-6xl rounded-2xl bg-white shadow-xl flex flex-col overflow-hidden border border-slate-200">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <div>
                        <h3 id="call-title" class="font-semibold text-slate-900">{{ __('Call') }}</h3>
                        <p id="call-status-text" class="text-xs text-slate-500 mt-1"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="accept-call" class="btn-primary text-xs py-1.5 px-3 hidden">{{ __('Accept') }}</button>
                        <button type="button" id="reject-call" class="btn-secondary text-xs py-1.5 px-3 hidden">{{ __('Reject') }}</button>
                        <button type="button" id="end-call" class="btn-secondary text-xs py-1.5 px-3 hidden">{{ __('End') }}</button>
                        <button type="button" id="close-call" class="btn-secondary text-xs py-1.5 px-3">{{ __('Close') }}</button>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-3 p-4 bg-slate-50">
                    <div class="card p-3">
                        <p class="text-xs text-slate-500 mb-2">{{ __('Remote') }}</p>
                        <video id="remote-video" class="w-full rounded-xl bg-black" autoplay playsinline></video>
                    </div>
                    <div class="card p-3">
                        <p class="text-xs text-slate-500 mb-2">{{ __('You') }}</p>
                        <video id="local-video" class="w-full rounded-xl bg-black" autoplay playsinline muted></video>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($activeType)
        <div
            id="messenger-data"
            class="hidden"
            data-ws-url="{{ $wsUrl }}"
            data-ws-channels='@json(array_values(array_filter(array_merge([$wsUserChannel], $wsGroupChannels->all()))))'
            data-has-typing="{{ $typing ? 1 : 0 }}"
            data-active-target-id="{{ $activeType === 'direct' ? $activeTarget->id : 0 }}"
        ></div>

        <script>
            (function () {
                const feed = document.getElementById('messenger-feed');
                const composer = document.getElementById('composer');
                const form = document.getElementById('composer-form');
                const submitBtn = document.getElementById('composer-submit');
                const attachmentInput = document.getElementById('composer-attachment');
                const composerError = document.getElementById('composer-error');
                const typingEl = document.getElementById('typing-indicator');
                const groupSettingsToggle = document.getElementById('group-settings-toggle');
                const groupSettingsPanel = document.getElementById('group-settings-panel');
                const groupSettingsBackdrop = document.getElementById('group-settings-backdrop');
                const runtime = document.getElementById('messenger-data');
                const type = "{{ $activeType }}";
                const feedEndpoint = type === 'direct'
                    ? "{{ route('messenger.direct-feed', $activeTarget) }}"
                    : "{{ route('messenger.group-feed', $activeTarget) }}";
                const typingEndpoint = type === 'direct'
                    ? "{{ route('messenger.typing', $activeTarget) }}"
                    : '';
                const wsUrl = runtime?.dataset.wsUrl || '';
                const wsChannels = JSON.parse(runtime?.dataset.wsChannels || '[]');
                const hasInitialTyping = runtime?.dataset.hasTyping === '1';
                const activeTargetId = Number(runtime?.dataset.activeTargetId || 0);
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                let loading = false;
                let queuedRefresh = false;
                let typingTick = 0;
                let typingTimeout = null;
                let submitLocked = false;
                let lastSubmitAt = 0;
                let unlockGuard = null;
                let lastFeedHtml = feed ? feed.innerHTML : '';

                if (groupSettingsToggle && groupSettingsPanel) {
                    const openGroupSettings = () => {
                        groupSettingsPanel.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                        groupSettingsPanel.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');
                        groupSettingsBackdrop?.classList.remove('hidden');
                        groupSettingsToggle.setAttribute('aria-expanded', 'true');
                    };

                    const closeGroupSettings = () => {
                        groupSettingsPanel.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                        groupSettingsPanel.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                        groupSettingsBackdrop?.classList.add('hidden');
                        groupSettingsToggle.setAttribute('aria-expanded', 'false');
                    };

                    groupSettingsToggle.addEventListener('click', function () {
                        const expanded = groupSettingsToggle.getAttribute('aria-expanded') === 'true';
                        if (expanded) {
                            closeGroupSettings();
                            return;
                        }

                        openGroupSettings();
                    });

                    groupSettingsBackdrop?.addEventListener('click', closeGroupSettings);

                    document.addEventListener('keydown', function (event) {
                        if (event.key === 'Escape') {
                            closeGroupSettings();
                        }
                    });
                }

                const setSubmitState = (busy) => {
                    submitLocked = busy;

                    if (submitBtn) {
                        submitBtn.disabled = busy;
                        submitBtn.classList.toggle('opacity-70', busy);
                        submitBtn.textContent = busy ? "{{ __('Sending...') }}" : "{{ __('Send') }}";
                    }

                    if (composer) {
                        composer.readOnly = busy;
                    }
                };

                const showComposerError = (message) => {
                    if (!composerError) {
                        return;
                    }

                    if (!message) {
                        composerError.classList.add('hidden');
                        composerError.textContent = '';
                        return;
                    }

                    composerError.classList.remove('hidden');
                    composerError.textContent = message;
                };

                const scheduleRefresh = () => {
                    if (queuedRefresh) {
                        return;
                    }

                    queuedRefresh = true;
                    setTimeout(() => {
                        queuedRefresh = false;
                        refresh();
                    }, 120);
                };

                const fetchWithTimeout = async (url, options = {}, timeoutMs = 9000) => {
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), timeoutMs);

                    try {
                        return await fetch(url, {
                            ...options,
                            signal: controller.signal,
                        });
                    } finally {
                        clearTimeout(timeoutId);
                    }
                };

                const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

                const refresh = async () => {
                    if (loading || !feed) {
                        return;
                    }

                    loading = true;
                    const nearBottom = feed.scrollHeight - feed.scrollTop - feed.clientHeight < 120;

                    try {
                        const response = await fetch(feedEndpoint, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                        });

                        if (!response.ok) {
                            return;
                        }

                        const payload = await response.json();
                        if (payload.html !== lastFeedHtml) {
                            feed.innerHTML = payload.html;
                            lastFeedHtml = payload.html;
                        }

                        if (type === 'direct' && typingEl) {
                            typingEl.textContent = payload.typing ? "{{ __('Typing...') }}" : '';
                        }

                        if (nearBottom) {
                            feed.scrollTop = feed.scrollHeight;
                        }
                    } finally {
                        loading = false;
                    }
                };

                if (composer) {
                    composer.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter' && !event.ctrlKey) {
                            event.preventDefault();

                            if (submitLocked) {
                                return;
                            }

                            form?.requestSubmit();
                        }
                    });

                    composer.addEventListener('input', function () {
                        if (type !== 'direct') {
                            return;
                        }

                        const now = Date.now();
                        if (now - typingTick < 2000) {
                            return;
                        }

                        typingTick = now;

                        fetch(typingEndpoint, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrf,
                            },
                            credentials: 'same-origin',
                        });
                    });
                }

                if (form && composer) {
                    form.addEventListener('submit', async function (event) {
                        event.preventDefault();

                        if (submitLocked) {
                            return;
                        }

                        const now = Date.now();
                        if (now - lastSubmitAt < 700) {
                            return;
                        }
                        lastSubmitAt = now;

                        const body = (composer.value || '').trim();
                        const hasAttachment = !!attachmentInput?.files?.length;

                        if (!body && !hasAttachment) {
                            showComposerError("{{ __('Message cannot be empty.') }}");
                            return;
                        }

                        showComposerError('');
                        setSubmitState(true);

                        clearTimeout(unlockGuard);
                        unlockGuard = setTimeout(() => {
                            setSubmitState(false);
                            showComposerError("{{ __('Send request timed out. Please try again.') }}");
                        }, 15000);

                        try {
                            let response = null;

                            for (let attempt = 0; attempt < 2; attempt += 1) {
                                try {
                                    const formData = new FormData(form);
                                    formData.set('body', body);

                                    response = await fetchWithTimeout(form.action, {
                                        method: 'POST',
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': csrf,
                                        },
                                        credentials: 'same-origin',
                                        body: formData,
                                    }, 9000);
                                    break;
                                } catch (error) {
                                    if (attempt === 0) {
                                        await wait(300);
                                        continue;
                                    }

                                    throw error;
                                }
                            }

                            if (!response) {
                                throw new Error('send-no-response');
                            }

                            if (response.status === 422) {
                                const payload = await response.json();
                                const message = payload?.errors?.body?.[0] || "{{ __('Unable to send message.') }}";
                                showComposerError(message);
                                return;
                            }

                            if (!response.ok) {
                                throw new Error('send-failed');
                            }

                            composer.value = '';
                            if (attachmentInput) {
                                attachmentInput.value = '';
                            }
                            composer.focus();
                            scheduleRefresh();
                        } catch (_) {
                            showComposerError("{{ __('Network is unstable. Please try sending again.') }}");
                        } finally {
                            clearTimeout(unlockGuard);
                            setSubmitState(false);
                        }
                    });
                }

                if (feed) {
                    feed.scrollTop = feed.scrollHeight;
                    setInterval(scheduleRefresh, 12000);
                }

                const triggerTyping = () => {
                    if (!typingEl || type !== 'direct') {
                        return;
                    }

                    typingEl.textContent = "{{ __('Typing...') }}";
                    if (typingTimeout) {
                        clearTimeout(typingTimeout);
                    }
                    typingTimeout = setTimeout(() => {
                        typingEl.textContent = '';
                    }, 3000);
                };

                const bindSocket = () => {
                    if (!wsUrl || !Array.isArray(wsChannels) || wsChannels.length === 0) {
                        return;
                    }

                    let socket;

                    const connect = () => {
                        try {
                            const query = wsChannels
                                .map((channel) => 'channel=' + encodeURIComponent(channel))
                                .join('&');
                            const separator = wsUrl.includes('?') ? '&' : '?';
                            socket = new WebSocket(wsUrl + separator + query);
                        } catch (_) {
                            return;
                        }

                        socket.addEventListener('open', () => {
                            try {
                                socket.send(JSON.stringify({ action: 'subscribe', channels: wsChannels }));
                            } catch (_) {
                            }
                        });

                        socket.addEventListener('message', (event) => {
                            try {
                                const message = JSON.parse(event.data || '{}');
                                const eventName = message?.event || '';

                                if (eventName === 'typing.direct' && type === 'direct') {
                                    const fromId = Number(message?.payload?.from_id || 0);
                                    if (fromId === activeTargetId) {
                                        triggerTyping();
                                    }
                                    return;
                                }

                                if (eventName === 'message.direct' || eventName === 'message.group') {
                                    scheduleRefresh();
                                }
                            } catch (_) {
                            }
                        });

                        socket.addEventListener('close', () => {
                            setTimeout(connect, 1800);
                        });

                        socket.addEventListener('error', () => {
                            try {
                                socket.close();
                            } catch (_) {
                            }
                        });
                    };

                    connect();
                };

                bindSocket();
                if (type === 'direct' && hasInitialTyping) {
                    triggerTyping();
                }
            })();
        </script>
    @endif

    @if ($activeType === 'direct')
        <script>
            (function () {
                const modal = document.getElementById('call-modal');
                const startHeader = document.getElementById('start-call-header');
                const startInline = document.getElementById('start-call-inline');
                const acceptBtn = document.getElementById('accept-call');
                const rejectBtn = document.getElementById('reject-call');
                const endBtn = document.getElementById('end-call');
                const closeBtn = document.getElementById('close-call');
                const titleNode = document.getElementById('call-title');
                const statusNode = document.getElementById('call-status-text');
                const remoteVideo = document.getElementById('remote-video');
                const localVideo = document.getElementById('local-video');
                const callData = document.getElementById('call-data');

                const contactId = Number(callData?.dataset.contactId || 0);
                const authId = Number(callData?.dataset.authId || 0);
                const iceServers = JSON.parse(callData?.dataset.ice || '[]');
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                let currentCall = null;
                let pc = null;
                let localStream = null;
                let ringingCtx = null;
                let ringingOsc = null;
                let locked = false;
                let callActionLocked = false;

                const setCallStatus = (message = '', tone = 'normal') => {
                    if (!statusNode) {
                        return;
                    }

                    statusNode.textContent = message;
                    statusNode.classList.remove('text-slate-500', 'text-red-600', 'text-emerald-600');
                    statusNode.classList.add(tone === 'error' ? 'text-red-600' : tone === 'success' ? 'text-emerald-600' : 'text-slate-500');
                };

                const setCallActionBusy = (busy) => {
                    callActionLocked = busy;
                    [startHeader, startInline, acceptBtn, rejectBtn, endBtn].forEach((button) => {
                        if (!button) {
                            return;
                        }

                        button.disabled = busy;
                        button.classList.toggle('opacity-70', busy);
                    });
                };

                const api = async (url, options = {}) => {
                    const headers = Object.assign({
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                    }, options.headers || {});

                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 10000);

                    let response;
                    try {
                        response = await fetch(url, Object.assign({
                            credentials: 'same-origin',
                            headers,
                            signal: controller.signal,
                        }, options));
                    } finally {
                        clearTimeout(timeoutId);
                    }

                    if (!response.ok) {
                        let message = 'Request failed';
                        try {
                            const payload = await response.json();
                            message = payload?.message || message;
                        } catch (_) {
                        }

                        throw new Error(message);
                    }

                    try {
                        return await response.json();
                    } catch (_) {
                        throw new Error('Request failed');
                    }
                };

                const closeModal = () => {
                    modal?.classList.add('hidden');
                    setCallStatus('');
                };

                const stopRingtone = () => {
                    try { ringingOsc?.stop(); } catch (_) {}
                    ringingOsc = null;
                    if (ringingCtx) {
                        ringingCtx.close();
                        ringingCtx = null;
                    }
                };

                const startRingtone = () => {
                    if (ringingCtx) return;
                    try {
                        ringingCtx = new (window.AudioContext || window.webkitAudioContext)();
                        ringingOsc = ringingCtx.createOscillator();
                        const gain = ringingCtx.createGain();
                        ringingOsc.frequency.value = 660;
                        gain.gain.value = 0.05;
                        ringingOsc.connect(gain);
                        gain.connect(ringingCtx.destination);
                        ringingOsc.start();
                    } catch (_) {}
                };

                const resetPeer = () => {
                    if (pc) {
                        try { pc.close(); } catch (_) {}
                    }
                    pc = null;

                    if (localStream) {
                        localStream.getTracks().forEach((track) => track.stop());
                    }
                    localStream = null;

                    if (localVideo) localVideo.srcObject = null;
                    if (remoteVideo) remoteVideo.srcObject = null;
                };

                const ensurePeer = async () => {
                    if (pc) return pc;

                    if (!window.isSecureContext) {
                        throw new Error("{{ __('Calling requires HTTPS (secure context).') }}");
                    }

                    if (!navigator.mediaDevices?.getUserMedia) {
                        throw new Error("{{ __('Camera/Microphone is not supported on this browser.') }}");
                    }

                    localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: true });
                    if (localVideo) localVideo.srcObject = localStream;

                    pc = new RTCPeerConnection({ iceServers });
                    localStream.getTracks().forEach((track) => pc.addTrack(track, localStream));

                    pc.ontrack = (event) => {
                        if (remoteVideo) remoteVideo.srcObject = event.streams[0];
                    };

                    pc.onconnectionstatechange = () => {
                        if (!pc) {
                            return;
                        }

                        if (['failed', 'disconnected'].includes(pc.connectionState) && currentCall?.id) {
                            end();
                        }
                    };

                    return pc;
                };

                const waitIceComplete = (peer) => new Promise((resolve) => {
                    if (peer.iceGatheringState === 'complete') {
                        resolve();
                        return;
                    }

                    const handler = () => {
                        if (peer.iceGatheringState === 'complete') {
                            peer.removeEventListener('icegatheringstatechange', handler);
                            resolve();
                        }
                    };

                    peer.addEventListener('icegatheringstatechange', handler);
                });

                const showIncoming = () => {
                    modal?.classList.remove('hidden');
                    if (titleNode) titleNode.textContent = "{{ __('Incoming call') }}";
                    setCallStatus("{{ __('Someone is calling you.') }}");
                    acceptBtn?.classList.remove('hidden');
                    rejectBtn?.classList.remove('hidden');
                    endBtn?.classList.add('hidden');
                };

                const showActive = (titleText) => {
                    modal?.classList.remove('hidden');
                    if (titleNode) titleNode.textContent = titleText;
                    acceptBtn?.classList.add('hidden');
                    rejectBtn?.classList.add('hidden');
                    endBtn?.classList.remove('hidden');
                };

                const startCall = async () => {
                    if (callActionLocked) {
                        return;
                    }

                    setCallActionBusy(true);
                    setCallStatus("{{ __('Starting call...') }}");

                    try {
                        const payload = await api("{{ route('calls.start', $activeTarget) }}", { method: 'POST' });
                        currentCall = payload.call;
                        showActive("{{ __('Calling...') }}");
                        setCallStatus("{{ __('Waiting for answer...') }}");
                    } catch (error) {
                        setCallStatus(error.message || "{{ __('Unable to start call.') }}", 'error');
                    } finally {
                        setCallActionBusy(false);
                    }
                };

                const sync = async () => {
                    if (locked) return;
                    locked = true;

                    try {
                        const payload = await api("{{ route('calls.poll') }}");
                        const call = payload.call;

                        if (!call) {
                            if (currentCall) {
                                stopRingtone();
                                resetPeer();
                                closeModal();
                            }
                            stopRingtone();
                            currentCall = null;
                            return;
                        }

                        if (![call.caller_id, call.callee_id].includes(contactId)) {
                            return;
                        }

                        currentCall = call;

                        if (call.status === 'ringing' && call.callee_id === authId) {
                            startRingtone();
                            showIncoming();
                            return;
                        }

                        if (call.status === 'ringing' && call.caller_id === authId) {
                            modal?.classList.remove('hidden');
                            if (titleNode) titleNode.textContent = "{{ __('Calling...') }}";
                            setCallStatus("{{ __('Waiting for answer...') }}");
                            return;
                        }

                        if (call.status === 'active') {
                            stopRingtone();
                            showActive("{{ __('In call') }}");
                            setCallStatus("{{ __('Connected') }}", 'success');

                            if (call.caller_id === authId && !call.offer_sdp) {
                                const peer = await ensurePeer();
                                const offer = await peer.createOffer();
                                await peer.setLocalDescription(offer);
                                await waitIceComplete(peer);

                                await api("{{ url('/calls') }}/" + call.id + "/signal", {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ type: 'offer', sdp: peer.localDescription.sdp }),
                                });
                            }

                            if (call.offer_sdp && call.callee_id === authId) {
                                const peer = await ensurePeer();
                                if (!peer.currentRemoteDescription) {
                                    await peer.setRemoteDescription({ type: 'offer', sdp: call.offer_sdp });
                                    const answer = await peer.createAnswer();
                                    await peer.setLocalDescription(answer);
                                    await waitIceComplete(peer);

                                    await api("{{ url('/calls') }}/" + call.id + "/signal", {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({ type: 'answer', sdp: peer.localDescription.sdp }),
                                    });
                                }
                            }

                            if (call.answer_sdp && call.caller_id === authId) {
                                const peer = await ensurePeer();
                                if (!peer.currentRemoteDescription) {
                                    await peer.setRemoteDescription({ type: 'answer', sdp: call.answer_sdp });
                                }
                            }
                        }

                        if (['ended', 'rejected', 'missed'].includes(call.status)) {
                            stopRingtone();
                            resetPeer();
                            if (call.status === 'rejected') {
                                setCallStatus("{{ __('Call was rejected.') }}", 'error');
                            } else if (call.status === 'missed') {
                                setCallStatus("{{ __('Call was missed.') }}", 'error');
                            } else {
                                setCallStatus("{{ __('Call ended.') }}");
                            }
                            closeModal();
                            currentCall = null;
                        }
                    } catch (error) {
                        setCallStatus(error.message || "{{ __('Call sync failed.') }}", 'error');
                    } finally {
                        locked = false;
                    }
                };

                const accept = async () => {
                    if (!currentCall || callActionLocked) return;
                    setCallActionBusy(true);
                    try {
                        await api("{{ url('/calls') }}/" + currentCall.id + "/accept", { method: 'POST' });
                        stopRingtone();
                        showActive("{{ __('Connecting...') }}");
                        setCallStatus("{{ __('Connecting media...') }}");
                    } catch (error) {
                        setCallStatus(error.message || "{{ __('Unable to accept call.') }}", 'error');
                    } finally {
                        setCallActionBusy(false);
                    }
                };

                const reject = async () => {
                    if (!currentCall || callActionLocked) return;
                    setCallActionBusy(true);
                    try {
                        await api("{{ url('/calls') }}/" + currentCall.id + "/reject", { method: 'POST' });
                    } catch (error) {
                        setCallStatus(error.message || "{{ __('Unable to reject call.') }}", 'error');
                    }
                    stopRingtone();
                    resetPeer();
                    closeModal();
                    setCallActionBusy(false);
                };

                const end = async () => {
                    if (!currentCall || callActionLocked) return;
                    setCallActionBusy(true);
                    try {
                        await api("{{ url('/calls') }}/" + currentCall.id + "/end", { method: 'POST' });
                    } catch (error) {
                        setCallStatus(error.message || "{{ __('Unable to end call.') }}", 'error');
                    }
                    stopRingtone();
                    resetPeer();
                    closeModal();
                    setCallActionBusy(false);
                };

                startHeader?.addEventListener('click', startCall);
                startInline?.addEventListener('click', startCall);
                acceptBtn?.addEventListener('click', accept);
                rejectBtn?.addEventListener('click', reject);
                endBtn?.addEventListener('click', end);
                closeBtn?.addEventListener('click', () => {
                    if (currentCall && ['ringing', 'active'].includes(currentCall.status)) {
                        end();
                        return;
                    }

                    closeModal();
                });

                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) closeModal();
                });

                setInterval(sync, 2000);
            })();
        </script>
    @endif
    </div>
</x-app-layout>
