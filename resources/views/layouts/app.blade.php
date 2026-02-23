<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'QhorizonPM') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="flex h-screen bg-white">
            <!-- Sidebar Navigation -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white border-b border-slate-200 px-6 py-4">
                        {{ $header }}
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto px-6 py-6">
                    {{ $slot }}
                </main>
            </div>
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
    </body>
</html>

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
    </body>
</html>
