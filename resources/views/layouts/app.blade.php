<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Aperlex') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" href="/images/logo-full.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="antialiased"
    x-data="{ sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true', mobileOpen: false }"
    x-init="$watch('sidebarCollapsed', v => localStorage.setItem('sidebar_collapsed', v))">

    @if(!request()->query('popup'))
    <div class="page-shell flex">
        {{-- Sidebar --}}
        @include('layouts.navigation')

        {{-- Mobile overlay --}}
        <div x-show="mobileOpen" x-transition.opacity @click="mobileOpen = false" class="sidebar-overlay md:hidden"
            style="display:none;"></div>

        {{-- Main Content --}}
        <div class="main-content flex-1 flex flex-col" :class="{ 'sidebar-collapsed': sidebarCollapsed }">

            {{-- Top Bar --}}
            <header class="topbar">
                <div class="flex items-center gap-3">
                    {{-- Mobile hamburger --}}
                    <button @click="mobileOpen = !mobileOpen" class="md:hidden btn-ghost !p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    @isset($header)
                        {{ $header }}
                    @endisset
                </div>
                <div class="flex items-center gap-2">
                    {{-- Notifications --}}
                    <a wire:navigate href="{{ route('notifications.index') }}" class="nav-action-btn"
                        title="{{ __('Notifications') }}" aria-label="{{ __('Notifications') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                        </svg>
                        @php($unreadCount = Auth::user()?->unreadNotificationsCountSafe() ?? 0)
                                                <span id="nav-unread-count"
                                                    class="absolute -top-1.5 -right-1.5 text-[10px] bg-red-500 text-white min-w-[18px] h-[18px] flex items-center justify-center rounded-full font-bold {{ $unreadCount > 0 ? '' : 'hidden' }}">{{ $unreadCount }}</span>
                                            </a>

                                            {{-- Language --}}
                                            <div class="nav-lang" aria-label="{{ __('Locale') }}">
                                                <a href="{{ route('lang.switch', 'vi') }}"
                                                    class="nav-lang-btn {{ app()->getLocale() === 'vi' ? 'nav-lang-btn-active' : '' }}">VI</a>
                                                <a href="{{ route('lang.switch', 'en') }}"
                                                    class="nav-lang-btn {{ app()->getLocale() === 'en' ? 'nav-lang-btn-active' : '' }}">EN</a>
                                            </div>

                                            {{-- Profile --}}
                                            <div class="flex items-center gap-2">
                                                <a wire:navigate href="{{ route('profile.edit') }}"
                                                    class="nav-profile-chip hover:border-indigo-300 transition-colors">
                                                    <div
                                                        class="w-6 h-6 rounded-md bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-[10px] font-bold text-white mr-2">
                                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                                    </div>
                                                    <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                                                </a>
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button type="submit" class="nav-action-btn" title="{{ __('Log Out') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                                            <polyline points="16 17 21 12 16 7" />
                                                            <line x1="21" x2="9" y1="12" y2="12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </header>

                                    {{-- Flash / Toast --}}
                                    @include('components.toast-notification')

                                    {{-- Page Content --}}
                                    <main class="flex-1">
                                        <div class="mx-auto w-full max-w-[1400px] px-5 py-6 md:px-8">
                                            {{ $slot }}
                                        </div>
                                    </main>
                                </div>
                            </div>
                        @else
    {{-- Popup mode (AI / Messenger) --}}
    <div class="w-full h-screen overflow-hidden p-0">
        {{ $slot }}
    </div>
    @endif

    <script>
        (function () {
            const countNode = document.getElementById('nav-unread-count');
            if (!countNode) return;

            let lastCount = parseInt((countNode.textContent || '0').trim(), 10) || 0;

            const playSound = () => {
                try {
                    const context = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = context.createOscillator();
                    const gain = context.createGain();
                    oscillator.type = 'sine';
                    oscillator.frequency.value = 880;
                    gain.gain.value = 0.04;
                    oscillator.connect(gain);
                    gain.connect(context.destination);
                    oscillator.start();
                    oscillator.stop(context.currentTime + 0.12);
                } catch (_) { }
            };

            const syncNotifications = async () => {
                try {
                    const response = await fetch("{{ route('notifications.pulse') }}", {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) return;
                    const payload = await response.json();
                    const currentCount = Number(payload.unreadCount || 0);

                    countNode.textContent = String(currentCount);
                    countNode.classList.toggle('hidden', currentCount < 1);

                    if (currentCount > lastCount) {
                        playSound();
                        if (window.notify) {
                            window.notify("{{ __('You have a new notification.') }}", 'info');
                        }
                    }
                    lastCount = currentCount;
                } catch (_) { }
            };

            if (window.realtime) {
                const userId = {{ Auth::id() }};
                window.realtime.subscribe(`user.${userId}`);
                window.realtime.on('notification.new', () => {
                    console.log('Realtime notification received');
                    syncNotifications();
                });
            }
        })();
    </script>

    @if(!request()->query('popup'))
        <!-- Floating Chat Bubbles -->
        <div x-data="{ activePopup: null }" class="fixed bottom-6 right-6 flex flex-col gap-3 z-50 items-end">

            <!-- AI Popup -->
            <div x-show="activePopup === 'ai'" x-transition.opacity.duration.300ms
                class="fixed bottom-24 right-6 h-[700px] max-h-[calc(100vh-8rem)] bg-white rounded-2xl overflow-hidden flex flex-col z-50"
                style="width: 1000px; max-width: calc(100vw - 3rem); display: none; box-shadow: var(--shadow-xl); border: 1px solid var(--border);">
                <div class="flex items-center justify-between px-5 py-3.5" style="background: var(--gradient);">
                    <h3 class="font-semibold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v4" />
                            <path d="m16.24 7.76 2.83-2.83" />
                            <path d="M18 12h4" />
                            <path d="m16.24 16.24 2.83 2.83" />
                            <path d="M12 18v4" />
                            <path d="m4.93 19.07 2.83-2.83" />
                            <path d="M2 12h4" />
                            <path d="m4.93 4.93 2.83 2.83" />
                            <circle cx="12" cy="12" r="4" />
                        </svg>
                        {{ __('AI Assistant') }}
                    </h3>
                    <button @click="activePopup = null" class="text-white/80 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
                <iframe :src="activePopup === 'ai' ? '{{ route('ai.chat.index', ['popup' => 1]) }}' : ''"
                    class="w-full flex-1 border-0 bg-slate-50"></iframe>
            </div>

            <!-- Messenger Popup -->
            <div x-show="activePopup === 'messenger'" x-transition.opacity.duration.300ms
                class="fixed bottom-24 right-6 h-[700px] max-h-[calc(100vh-8rem)] bg-white rounded-2xl overflow-hidden flex flex-col z-50"
                style="width: 1000px; max-width: calc(100vw - 3rem); display: none; box-shadow: var(--shadow-xl); border: 1px solid var(--border);">
                <div class="flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-emerald-500 to-green-600">
                    <h3 class="font-semibold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                        {{ __('Messenger') }}
                    </h3>
                    <button @click="activePopup = null" class="text-white/80 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
                <iframe :src="activePopup === 'messenger' ? '{{ route('messenger.index', ['popup' => 1]) }}' : ''"
                    class="w-full flex-1 border-0 bg-slate-50"></iframe>
            </div>

            <!-- AI Bubble -->
            <button @click="activePopup = activePopup === 'ai' ? null : 'ai'"
                class="group relative flex items-center justify-center w-14 h-14 text-white rounded-xl hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                style="background: var(--gradient); box-shadow: var(--shadow-lg), var(--shadow-glow);"
                aria-label="{{ __('AI Assistant') }}">
                <svg x-show="activePopup !== 'ai'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v4" />
                    <path d="m16.24 7.76 2.83-2.83" />
                    <path d="M18 12h4" />
                    <path d="m16.24 16.24 2.83 2.83" />
                    <path d="M12 18v4" />
                    <path d="m4.93 19.07 2.83-2.83" />
                    <path d="M2 12h4" />
                    <path d="m4.93 4.93 2.83 2.83" />
                    <circle cx="12" cy="12" r="4" />
                </svg>
                <svg x-show="activePopup === 'ai'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    style="display: none;">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
                <span
                    class="absolute right-full mr-3 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none shadow-lg">{{ __('AI Assistant') }}</span>
            </button>

            <!-- Messenger Bubble -->
            <button @click="activePopup = activePopup === 'messenger' ? null : 'messenger'"
                class="group relative flex items-center justify-center w-14 h-14 bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                aria-label="{{ __('Messenger') }}">
                <svg x-show="activePopup !== 'messenger'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                </svg>
                <svg x-show="activePopup === 'messenger'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" style="display: none;">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
                <span
                    class="absolute right-full mr-3 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none shadow-lg">{{ __('Messenger') }}</span>
            </button>
        </div>
    @endif

    @livewireScripts
    @stack('scripts')
</body>

</html>