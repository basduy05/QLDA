<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'QhorizonPM') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|fraunces:400,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="page-shell">
            <div class="max-w-6xl mx-auto px-6 py-10">
                <header class="flex items-center justify-between">
                    <div class="text-lg font-semibold">QhorizonPM</div>
                    <div class="flex items-center gap-3 text-sm">
                        <a href="{{ route('lang.switch', 'vi') }}" class="px-3 py-1 rounded-full {{ app()->getLocale() === 'vi' ? 'bg-slate-900 text-white' : 'border border-slate-200' }}">VI</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1 rounded-full {{ app()->getLocale() === 'en' ? 'bg-slate-900 text-white' : 'border border-slate-200' }}">EN</a>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn-primary">{{ __('Go to dashboard') }}</a>
                            @else
                                <a href="{{ route('login') }}" class="btn-secondary">{{ __('Sign in') }}</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-primary">{{ __('Create account') }}</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </header>

                <section class="mt-16 grid gap-10 lg:grid-cols-2 items-center">
                    <div class="space-y-6">
                        <p class="text-sm uppercase tracking-widest text-slate-500">{{ __('Project management, done right') }}</p>
                        <h1 class="text-4xl lg:text-5xl font-semibold text-slate-900">
                            {{ __('Unify your team, tasks, and timelines in one calm workspace.') }}
                        </h1>
                        <p class="text-lg text-slate-600">
                            {{ __('QhorizonPM helps admins and teams manage projects, plan milestones, and keep delivery on track with clear ownership and bilingual workflows.') }}
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('register') }}" class="btn-primary">{{ __('Start with a demo') }}</a>
                            <a href="{{ route('login') }}" class="btn-secondary">{{ __('Explore dashboard') }}</a>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-slate-500">
                            <span class="badge bg-amber-100 text-amber-700">{{ __('Admin ready') }}</span>
                            <span class="badge bg-emerald-100 text-emerald-700">{{ __('User friendly') }}</span>
                            <span class="badge bg-sky-100 text-sky-700">{{ __('Deployment ready') }}</span>
                        </div>
                    </div>
                    <div class="card-strong p-8">
                        <div class="grid gap-4">
                            <div class="card p-4">
                                <p class="text-sm text-slate-500">{{ __('Active projects') }}</p>
                                <p class="text-2xl font-semibold text-slate-900">12</p>
                            </div>
                            <div class="card p-4">
                                <p class="text-sm text-slate-500">{{ __('Upcoming milestones') }}</p>
                                <p class="text-2xl font-semibold text-slate-900">7</p>
                            </div>
                            <div class="card p-4">
                                <p class="text-sm text-slate-500">{{ __('On-track delivery') }}</p>
                                <p class="text-2xl font-semibold text-slate-900">92%</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mt-16 grid gap-6 md:grid-cols-3">
                    <div class="card-strong p-6">
                        <h3 class="text-xl font-semibold">{{ __('Role-based access') }}</h3>
                        <p class="text-sm text-slate-500 mt-2">{{ __('Admins oversee all projects while users focus on their assigned work.') }}</p>
                    </div>
                    <div class="card-strong p-6">
                        <h3 class="text-xl font-semibold">{{ __('Clear project timelines') }}</h3>
                        <p class="text-sm text-slate-500 mt-2">{{ __('Track status, milestones, and deadlines in a unified view.') }}</p>
                    </div>
                    <div class="card-strong p-6">
                        <h3 class="text-xl font-semibold">{{ __('Bilingual interface') }}</h3>
                        <p class="text-sm text-slate-500 mt-2">{{ __('Switch between Vietnamese and English with one click.') }}</p>
                    </div>
                </section>

                <footer class="mt-16 flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-sm text-slate-500">
                    <span>{{ __('Ready for production deployment on shared hosting.') }}</span>
                    <span>© {{ date('Y') }} QhorizonPM</span>
                </footer>
            </div>
        </div>
    </body>
</html>
