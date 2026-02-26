<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('projects.show', $task->project) }}" class="text-sm font-medium text-slate-500 hover:text-accent transition-colors uppercase tracking-wider">{{ $task->project->name }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    <span class="text-sm font-medium text-slate-400 uppercase tracking-wider">{{ __('Task Details') }}</span>
                </div>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                    {{ $task->title }}
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('tasks.edit', $task) }}" class="btn-secondary inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('tasks.destroy', $task) }}" data-confirm="{{ __('Are you sure you want to delete this task?') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-secondary text-rose-600 hover:bg-rose-50 hover:border-rose-200 hover:text-rose-700 inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-full">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Task Overview (Description + Details) --}}
            <div class="card-strong p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        {{ __('Overview') }}
                    </h3>
                    <div class="text-xs text-slate-400">
                        {{ __('Created') }} {{ $task->created_at->format('d/m/Y') }} • {{ __('by') }} {{ $task->creator->name }}
                    </div>
                </div>

                {{-- Metadata Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6 p-4 bg-slate-50 rounded-lg border border-slate-100">
                     {{-- Status --}}
                    <div>
                        <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block mb-1">{{ __('Status') }}</span>
                        @php
                            $statusColors = [
                                'todo' => 'bg-slate-100 text-slate-700 border-slate-200',
                                'in_progress' => 'bg-sky-100 text-sky-700 border-sky-200',
                                'done' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            ];
                            $colorClass = $statusColors[$task->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold border {{ $colorClass }}">
                            {{ __(ucwords(str_replace('_', ' ', $task->status))) }}
                        </span>
                    </div>

                    {{-- Priority --}}
                    <div>
                        <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block mb-1">{{ __('Priority') }}</span>
                         @php
                            $priorityColors = [
                                'low' => 'text-slate-500 bg-slate-50 border-slate-200',
                                'medium' => 'text-amber-600 bg-amber-50 border-amber-200',
                                'high' => 'text-rose-600 bg-rose-50 border-rose-200',
                            ];
                            $pColorClass = $priorityColors[$task->priority] ?? 'text-slate-500 bg-slate-50 border-slate-200';
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold border {{ $pColorClass }}">
                            {{ __(ucwords($task->priority)) }}
                        </span>
                    </div>

                    {{-- Assignee --}}
                    <div>
                        <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block mb-1">{{ __('Assignee') }}</span>
                         @if($task->assignee)
                            <div class="flex items-center gap-2">
                                <div class="h-5 w-5 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600 shrink-0">
                                    {{ strtoupper(substr($task->assignee->name, 0, 2)) }}
                                </div>
                                <span class="text-sm font-semibold text-slate-900 truncate" title="{{ $task->assignee->name }}">{{ $task->assignee->name }}</span>
                            </div>
                        @else
                            <span class="text-slate-400 italic text-sm">{{ __('Unassigned') }}</span>
                        @endif
                    </div>

                    {{-- Due Date --}}
                    <div>
                        <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block mb-1">{{ __('Due Date') }}</span>
                        <div class="flex items-center gap-1.5 text-sm font-semibold {{ $task->due_date && $task->due_date->isPast() && $task->status !== 'done' ? 'text-rose-600' : 'text-slate-900' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $task->due_date ? $task->due_date->format('d/m/Y') : '—' }}
                        </div>
                    </div>
                </div>

                <div class="prose prose-sm prose-slate max-w-none border-t border-slate-100 pt-4">
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-2 block">{{ __('Description') }}</span>
                    <div class="max-h-[300px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-slate-200 scrollbar-track-transparent">
                        @if($task->description)
                            <p class="text-slate-600 leading-relaxed whitespace-pre-line">{!! nl2br(e($task->description)) !!}</p>
                        @else
                            <p class="text-slate-400 italic">{{ __('No description provided.') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Comments Section (Moved to be more visible) --}}
            <div class="card-strong p-6 flex flex-col h-[500px]">
                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    {{ __('Comments') }}
                </h3>
                <div class="flex-1 overflow-y-auto space-y-4 mb-4 pr-2 scrollbar-thin scrollbar-thumb-slate-200 scrollbar-track-transparent">
                    @forelse ($task->comments as $comment)
                        <div class="flex items-start gap-3">
                            <div class="h-8 w-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold shrink-0 text-xs">
                                {{ strtoupper(substr($comment->user->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 bg-slate-50 rounded-2xl rounded-tl-none p-3 border border-slate-100 group hover:bg-slate-100 transition-colors">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-semibold text-slate-900">{{ $comment->user->name }}</span>
                                    <span class="text-slate-500" title="{{ $comment->created_at->format('d/m/Y H:i:s') }}">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-sm text-slate-700 whitespace-pre-line">
                                    {!! $comment->body !!}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="9" y1="10" x2="15" y2="10"/><line x1="12" y1="7" x2="12" y2="13"/></svg>
                            <p class="text-sm">{{ __('No comments yet.') }}</p>
                        </div>
                    @endforelse
                </div>
                <div class="pt-4 border-t border-slate-100 flex-shrink-0">
                    <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="space-y-3">
                        @csrf
                        <div class="flex items-start gap-3" x-data="commentMentions(@js($mentionableUsers))">
                            <div class="h-8 w-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold shrink-0 text-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 relative">
                                <textarea 
                                    x-ref="textarea"
                                    name="body" 
                                    rows="3" 
                                    class="w-full rounded-xl border-slate-200 text-sm focus:ring-accent focus:border-accent resize-none placeholder-slate-400" 
                                    placeholder="{{ __('Write a comment... (Use @ to mention)') }}" 
                                    required
                                    @input="checkMentions($event)"
                                    @keydown="handleKeydown($event)"
                                >{{ old('body') }}</textarea>
                                
                                {{-- Mentions Dropdown --}}
                                <div x-show="showMentions" 
                                     x-transition
                                     style="display: none;"
                                     class="absolute z-10 w-64 bg-white rounded-lg shadow-xl border border-slate-100 overflow-hidden"
                                     :style="`top: ${coords.top}px; left: ${coords.left}px`">
                                    <div class="max-h-48 overflow-y-auto">
                                        <template x-for="(user, index) in filteredUsers" :key="user.id">
                                            <button 
                                                type="button"
                                                class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 flex items-center gap-2 transition-colors"
                                                :class="{'bg-slate-100': selectedIndex === index}"
                                                @click="selectUser(user)"
                                            >
                                                <div class="h-6 w-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600 shrink-0" x-text="user.initials"></div>
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-slate-900" x-text="user.name"></span>
                                                    <span class="text-[10px] text-slate-500" x-text="user.email"></span>
                                                </div>
                                            </button>
                                        </template>
                                        <div x-show="filteredUsers.length === 0" class="px-3 py-2 text-xs text-slate-400 italic">
                                            {{ __('No users found') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="text-xs text-slate-400 mt-1 flex justify-between items-center">
                                    <span>{{ __('Press Shift + Enter for new line') }}</span>
                                </div>
                                <x-input-error :messages="$errors->get('body')" class="mt-2" />
                            </div>
                        </div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('commentMentions', (users) => ({
            users: users.map(u => ({
                ...u, 
                initials: u.name.substring(0, 2).toUpperCase()
            })),
            showMentions: false,
            search: '',
            selectedIndex: 0,
            coords: { top: 0, left: 0 },
            
            get filteredUsers() {
                if (!this.search) return this.users;
                const lower = this.search.toLowerCase();
                return this.users.filter(u => 
                    u.name.toLowerCase().includes(lower) || 
                    u.email.toLowerCase().includes(lower)
                );
            },

            checkMentions(e) {
                const el = e.target;
                const val = el.value;
                const cursorPos = el.selectionStart;
                
                // Find the last '@' before cursor
                const lastAt = val.lastIndexOf('@', cursorPos - 1);
                
                if (lastAt !== -1) {
                    const textAfterAt = val.substring(lastAt + 1, cursorPos);
                    
                    // Allow spaces in names, but maybe limit to a reasonable length to avoid false positives
                    // Also check if there's a space BEFORE the @ (or it's start of line)
                    const charBeforeAt = lastAt > 0 ? val[lastAt - 1] : ' ';
                    
                    if ((charBeforeAt === ' ' || charBeforeAt === '\n') && textAfterAt.length < 20 && !textAfterAt.includes('\n')) {
                        this.search = textAfterAt;
                        this.showMentions = true;
                        this.selectedIndex = 0;
                        
                        // Simple positioning (approximate) - could be better with a library but this is lightweight
                        // We'll just position it below the textarea for now, relative to the bottom
                        // Actually, let's just make it float bottom-left of the textarea wrapper
                        this.coords = {
                            top: el.offsetHeight, 
                            left: 0 
                        };
                        return;
                    }
                }
                
                this.showMentions = false;
            },

            handleKeydown(e) {
                if (!this.showMentions) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    this.selectedIndex = (this.selectedIndex + 1) % this.filteredUsers.length;
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    this.selectedIndex = (this.selectedIndex - 1 + this.filteredUsers.length) % this.filteredUsers.length;
                } else if (e.key === 'Enter' && this.filteredUsers.length > 0) {
                    e.preventDefault(); // Don't submit form
                    this.selectUser(this.filteredUsers[this.selectedIndex]);
                } else if (e.key === 'Escape') {
                    this.showMentions = false;
                }
            },

            selectUser(user) {
                const el = this.$refs.textarea;
                const val = el.value;
                const cursorPos = el.selectionStart;
                const lastAt = val.lastIndexOf('@', cursorPos - 1);
                
                const before = val.substring(0, lastAt);
                const after = val.substring(cursorPos);
                
                // Insert the mention
                const newValue = `${before}@${user.name} ${after}`;
                
                el.value = newValue;
                this.showMentions = false;
                
                // Focus back and set cursor
                el.focus();
                const newCursorPos = lastAt + user.name.length + 2; // +2 for @ and space
                el.setSelectionRange(newCursorPos, newCursorPos);
            }
        }));
    });
</script>
                        <div class="flex justify-end">
                            <button type="submit" class="btn-primary text-xs py-2 px-5 shadow-sm">{{ __('Post comment') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar (Subtasks & Attachments) --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Subtasks --}}
            <div class="card-strong p-5 flex flex-col h-[400px]">
                <h3 class="text-md font-bold text-slate-900 mb-3 flex items-center gap-2 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    {{ __('Subtasks') }}
                    <span class="text-xs font-normal text-slate-400 ml-auto">{{ $task->subtasks->where('is_completed', true)->count() }}/{{ $task->subtasks->count() }}</span>
                </h3>
                
                <div class="space-y-2 mb-3 overflow-y-auto pr-1 scrollbar-thin scrollbar-thumb-slate-200 scrollbar-track-transparent flex-1" id="subtasks-list">
                    @foreach($task->subtasks as $subtask)
                        <div class="flex items-start gap-2 p-2 hover:bg-slate-50 rounded-lg group transition-colors" x-data="{ editing: false, title: '{{ $subtask->title }}' }">
                             <form method="POST" action="{{ route('subtasks.update', $subtask) }}" class="flex items-center pt-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_completed" value="0">
                                <input type="checkbox" 
                                       name="is_completed" 
                                       value="1" 
                                       onchange="this.form.submit()" 
                                       class="rounded border-slate-300 text-accent focus:ring-accent w-4 h-4 cursor-pointer"
                                       {{ $subtask->is_completed ? 'checked' : '' }}>
                             </form>
                             
                             <div class="flex-grow min-w-0">
                                 <span x-show="!editing" 
                                       @dblclick="editing = true"
                                       class="text-sm text-slate-700 block break-words cursor-pointer {{ $subtask->is_completed ? 'line-through text-slate-400' : '' }}">
                                     {{ $subtask->title }}
                                 </span>
                                 <form x-show="editing" 
                                       method="POST" 
                                       action="{{ route('subtasks.update', $subtask) }}" 
                                       @click.away="editing = false"
                                       class="flex-grow"
                                       style="display: none;">
                                     @csrf
                                     @method('PATCH')
                                     <textarea 
                                            name="title" 
                                            x-model="title" 
                                            class="w-full text-sm border-slate-200 rounded px-2 py-1 focus:ring-accent focus:border-accent resize-none overflow-hidden"
                                            rows="1"
                                            @keydown.enter.prevent="$el.form.submit()"
                                            autofocus></textarea>
                                 </form>
                             </div>

                             <form method="POST" action="{{ route('subtasks.destroy', $subtask) }}" class="opacity-0 group-hover:opacity-100 transition-opacity pt-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-300 hover:text-rose-500 p-0.5" title="{{ __('Delete') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                             </form>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('tasks.subtasks.store', $task) }}" class="flex items-center gap-2 mt-auto pt-2 border-t border-slate-100">
                    @csrf
                    <input type="text" name="title" placeholder="{{ __('Add subtask...') }}" class="flex-grow text-xs border-slate-200 rounded-lg focus:ring-accent focus:border-accent placeholder-slate-400" required>
                    <button type="submit" class="p-1.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 hover:text-slate-900 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                </form>
            </div>

            {{-- Attachments --}}
            <div class="card-strong p-5 flex flex-col h-[400px]">
                <h3 class="text-md font-bold text-slate-900 mb-3 flex items-center gap-2 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/><path d="M22 22 2 2" class="hidden"/></svg>
                    {{ __('Attachments') }}
                    <span class="text-xs font-normal text-slate-400 ml-auto">{{ $task->attachments->count() }}</span>
                </h3>

                <div class="space-y-2 mb-3 overflow-y-auto pr-1 scrollbar-thin scrollbar-thumb-slate-200 scrollbar-track-transparent flex-1">
                    @forelse($task->attachments as $attachment)
                        <div class="flex items-center justify-between p-2 bg-slate-50 border border-slate-200 rounded-lg group hover:border-accent/30 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-8 w-8 shrink-0 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400 group-hover:text-accent transition-colors">
                                    @if($attachment->file_type === 'image')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    @elseif($attachment->file_type === 'pdf')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="block text-xs font-medium text-slate-700 hover:text-accent truncate" title="{{ $attachment->file_name }}">
                                        {{ $attachment->file_name }}
                                    </a>
                                    <p class="text-[10px] text-slate-400">
                                        @php
                                            $size = $attachment->file_size;
                                            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                                            $power = $size > 0 ? floor(log($size, 1024)) : 0;
                                            $formattedSize = number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
                                        @endphp
                                        {{ $formattedSize }}
                                    </p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('attachments.destroy', $attachment) }}" class="opacity-0 group-hover:opacity-100 transition-opacity pl-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-300 hover:text-rose-500 p-1" onclick="return confirm('{{ __('Delete file?') }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-slate-400 italic bg-slate-50 rounded-lg border border-slate-100 border-dashed">
                            {{ __('No files attached') }}
                        </div>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('tasks.attachments.store', $task) }}" enctype="multipart/form-data" class="mt-auto" x-data="{ isDragging: false }">
                    @csrf
                    <div class="relative group">
                        <label 
                            class="flex flex-col items-center justify-center w-full h-16 border-2 border-dashed rounded-lg cursor-pointer transition-colors"
                            :class="isDragging ? 'border-accent bg-accent/5' : 'border-slate-300 hover:bg-slate-50'"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.form.submit()"
                        >
                            <div class="flex flex-col items-center justify-center">
                                <p class="text-xs text-slate-500 flex items-center gap-1">
                                    <svg class="w-4 h-4 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <span class="font-semibold text-accent">{{ __('Upload') }}</span>
                                </p>
                            </div>
                            <input x-ref="fileInput" type="file" name="files[]" multiple required class="hidden" onchange="this.form.submit()">
                        </label>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
