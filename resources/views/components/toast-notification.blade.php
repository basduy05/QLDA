<div x-data="{
        notifications: [],
        add(message, type = 'success') {
            const id = Date.now();
            this.notifications.push({ id, message, type, progress: 100 });
            const interval = setInterval(() => {
                const n = this.notifications.find(n => n.id === id);
                if (n) { n.progress -= 2; if (n.progress <= 0) { clearInterval(interval); this.remove(id); } }
                else { clearInterval(interval); }
            }, 100);
        },
        remove(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    }" @notify.window="add($event.detail.message, $event.detail.type)"
    class="fixed top-4 right-4 z-[100] flex flex-col gap-2.5 w-full max-w-sm pointer-events-none">
    @if (session('status'))
        <div x-init="add('{{ session('status') }}', 'success')"></div>
    @endif
    @if (session('success'))
        <div x-init="add('{{ session('success') }}', 'success')"></div>
    @endif
    @if (session('error'))
        <div x-init="add('{{ session('error') }}', 'error')"></div>
    @endif

    <template x-for="notification in notifications" :key="notification.id">
        <div x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95 translate-x-4"
            class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-xl bg-white border border-slate-200"
            style="box-shadow: var(--shadow-lg);">
            <div class="p-4">
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        <div x-show="notification.type === 'success'"
                            class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div>
                        <div x-show="notification.type === 'error'"
                            class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div x-show="notification.type === 'info'"
                            class="w-8 h-8 rounded-lg flex items-center justify-center"
                            style="background: var(--accent-soft);">
                            <svg class="h-4 w-4" style="color: var(--accent);" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                        </div>
                        <div x-show="notification.type === 'warning'"
                            class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="text-sm font-bold text-slate-900"
                            x-text="notification.type === 'error' ? 'Error' : (notification.type === 'success' ? 'Success' : (notification.type === 'warning' ? 'Warning' : 'Info'))">
                        </p>
                        <p class="mt-0.5 text-sm text-slate-500" x-text="notification.message"></p>
                    </div>
                    <button type="button" @click="remove(notification.id)"
                        class="shrink-0 inline-flex rounded-lg p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </div>
            </div>
            <!-- Progress bar -->
            <div class="h-1 bg-slate-100">
                <div class="h-full transition-all duration-100 ease-linear rounded-full"
                    :style="'width: ' + notification.progress + '%'" :class="{
                        'bg-emerald-500': notification.type === 'success',
                        'bg-red-500': notification.type === 'error',
                        'bg-indigo-500': notification.type === 'info',
                        'bg-amber-500': notification.type === 'warning'
                    }">
                </div>
            </div>
        </div>
    </template>
</div>