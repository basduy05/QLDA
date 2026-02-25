<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'QhorizonPM') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|fraunces:400,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="page-shell {{ request()->query('popup') ? '!min-h-0 !bg-none bg-white' : '' }}">
            @if(!request()->query('popup'))
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white/80 backdrop-blur border-b border-slate-100">
                        <div class="mx-auto w-full max-w-[1440px] px-4 py-3 md:px-6 md:py-4 xl:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset
            @endif

            <!-- Page Content -->
            <main>
                <div class="{{ request()->query('popup') ? 'w-full h-screen overflow-hidden p-0' : 'mx-auto w-full max-w-[1440px] px-4 py-4 md:px-6 md:py-5 xl:px-8' }}">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <script>
            (function () {
                const countNode = document.getElementById('nav-unread-count');
                if (!countNode) {
                    return;
                }

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
                    } catch (_) {
                    }
                };

                const syncNotifications = async () => {
                    try {
                        const response = await fetch("{{ route('notifications.pulse') }}", {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                        });

                        if (!response.ok) {
                            return;
                        }

                        const payload = await response.json();
                        const currentCount = Number(payload.unreadCount || 0);

                        countNode.textContent = String(currentCount);
                        countNode.classList.toggle('hidden', currentCount < 1);

                        if (currentCount > lastCount) {
                            playSound();
                        }

                        lastCount = currentCount;
                    } catch (_) {
                    }
                };

                setInterval(syncNotifications, 7000);
            })();
        </script>

        <script>
            document.addEventListener('submit', function (event) {
                const form = event.target;
                const message = form?.dataset?.confirm;
                if (!message) {
                    return;
                }

                if (!confirm(message)) {
                    event.preventDefault();
                }
            });
        </script>

        @if(!request()->query('popup'))
        <!-- Floating Chat Bubbles -->
        <div x-data="{ activePopup: null }" class="fixed bottom-6 right-6 flex flex-col gap-4 z-50 items-end">
            
            <!-- AI Popup -->
            <div x-show="activePopup === 'ai'" x-transition.opacity.duration.300ms class="fixed bottom-24 right-6 w-[600px] h-[700px] max-w-[calc(100vw-3rem)] max-h-[calc(100vh-8rem)] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col z-50" style="display: none;">
                <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                    <h3 class="font-semibold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"/><path d="m16.24 7.76 2.83-2.83"/><path d="M18 12h4"/><path d="m16.24 16.24 2.83 2.83"/><path d="M12 18v4"/><path d="m4.93 19.07 2.83-2.83"/><path d="M2 12h4"/><path d="m4.93 4.93 2.83 2.83"/><circle cx="12" cy="12" r="4"/></svg>
                        {{ __('AI Assistant') }}
                    </h3>
                    <button @click="activePopup = null" class="text-white/80 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <iframe :src="activePopup === 'ai' ? '{{ route('ai.chat.index', ['popup' => 1]) }}' : ''" class="w-full flex-1 border-0 bg-slate-50"></iframe>
            </div>

            <!-- Messenger Popup -->
            <div x-show="activePopup === 'messenger'" x-transition.opacity.duration.300ms class="fixed bottom-24 right-6 w-[600px] h-[700px] max-w-[calc(100vw-3rem)] max-h-[calc(100vh-8rem)] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col z-50" style="display: none;">
                <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-accent to-blue-600 text-white">
                    <h3 class="font-semibold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        {{ __('Messenger') }}
                    </h3>
                    <button @click="activePopup = null" class="text-white/80 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <iframe :src="activePopup === 'messenger' ? '{{ route('messenger.index', ['popup' => 1]) }}' : ''" class="w-full flex-1 border-0 bg-slate-50"></iframe>
            </div>

            <!-- AI Chat Bubble -->
            <button @click="activePopup = activePopup === 'ai' ? null : 'ai'" class="group relative flex items-center justify-center w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-full shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" aria-label="{{ __('AI Assistant') }}">
                <svg x-show="activePopup !== 'ai'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"/><path d="m16.24 7.76 2.83-2.83"/><path d="M18 12h4"/><path d="m16.24 16.24 2.83 2.83"/><path d="M12 18v4"/><path d="m4.93 19.07 2.83-2.83"/><path d="M2 12h4"/><path d="m4.93 4.93 2.83 2.83"/><circle cx="12" cy="12" r="4"/></svg>
                <svg x-show="activePopup === 'ai'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                <span class="absolute right-full mr-4 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap pointer-events-none shadow-md">
                    {{ __('AI Assistant') }}
                </span>
            </button>

            <!-- Messenger Bubble -->
            <button @click="activePopup = activePopup === 'messenger' ? null : 'messenger'" class="group relative flex items-center justify-center w-14 h-14 bg-gradient-to-br from-accent to-blue-600 text-white rounded-full shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" aria-label="{{ __('Messenger') }}">
                <svg x-show="activePopup !== 'messenger'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <svg x-show="activePopup === 'messenger'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                <span class="absolute right-full mr-4 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap pointer-events-none shadow-md">
                    {{ __('Messenger') }}
                </span>
            </button>
        </div>
        @endif

        @stack('scripts')
    </body>
</html>
