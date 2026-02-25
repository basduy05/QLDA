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
        <div class="page-shell">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/80 backdrop-blur border-b border-slate-100">
                    <div class="w-full py-6 px-4 md:px-6 xl:px-10">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="w-full px-4 md:px-6 xl:px-10 py-8 md:py-10">
                {{ $slot }}
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

        @stack('scripts')
    </body>
</html>
