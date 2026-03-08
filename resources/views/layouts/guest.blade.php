<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Aperlex') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/logo-full.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Google+Sans:wght@400;500;700&display=swap"
        rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .auth-bg {
            background:
                radial-gradient(ellipse at 20% 50%, rgba(99, 102, 241, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(139, 92, 246, 0.06) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 100%, rgba(99, 102, 241, 0.04) 0%, transparent 50%),
                var(--surface);
        }

        .auth-grid {
            background-image:
                linear-gradient(rgba(148, 163, 184, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, 0.06) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</head>

<body class="antialiased">
    <div class="auth-bg auth-grid min-h-screen flex">
        {{-- Left branding panel (hidden on mobile) --}}
        <div class="hidden lg:flex lg:w-[45%] xl:w-[40%] relative overflow-hidden"
            style="background: var(--sidebar-bg);">
            <div class="absolute inset-0"
                style="background: radial-gradient(ellipse at 30% 40%, rgba(99, 102, 241, 0.2) 0%, transparent 60%), radial-gradient(ellipse at 70% 80%, rgba(139, 92, 246, 0.15) 0%, transparent 60%);">
            </div>
            <div class="relative flex flex-col justify-between p-12 w-full">
                <div>
                    <a href="/" class="flex items-center gap-3">
                        <img src="/images/logo-full.png" alt="Aperlex" class="h-10">
                        <span class="text-white font-bold text-2xl tracking-tight">Aperlex</span>
                    </a>
                </div>
                <div class="space-y-6">
                    <h2 class="text-3xl xl:text-4xl font-bold text-white leading-tight">
                        {{ __('Manage projects') }}
                        <span
                            class="bg-gradient-to-r from-indigo-400 to-violet-400 bg-clip-text text-transparent">{{ __('smarter') }}</span>,<br>
                        {{ __('deliver faster') }}
                    </h2>
                    <p class="text-slate-400 text-lg max-w-md leading-relaxed">
                        {{ __('AI-powered project management for teams that ship.') }}
                    </p>
                    <div class="flex items-center gap-6 pt-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-500/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <span class="text-sm text-slate-300">{{ __('AI Insights') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-violet-500/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>
                            <span class="text-sm text-slate-300">{{ __('Team Collab') }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-slate-600 text-sm">
                    © {{ date('Y') }} Aperlex
                </div>
            </div>
        </div>

        {{-- Right form panel --}}
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-10">
            <div class="lg:hidden text-center mb-8">
                <a href="/" class="inline-flex items-center gap-2.5">
                    <img src="/images/logo-full.png" alt="Aperlex" class="h-10">
                    <span class="font-bold text-xl tracking-tight text-slate-900">Aperlex</span>
                </a>
                <p class="text-sm text-slate-500 mt-2">{{ __('Plan, deliver, and celebrate project wins.') }}</p>
            </div>

            <div class="w-full max-w-md">
                <div class="card-strong px-7 py-8" style="animation: fadeSlideUp 400ms var(--ease-smooth) both;">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>

</html>