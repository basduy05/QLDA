<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Aperlex') }} — {{ __('Smart Project Management') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/logo-full.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Google+Sans:wght@400;500;700&display=swap"
        rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .hero-grid {
            background-image:
                linear-gradient(rgba(99, 102, 241, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 102, 241, 0.04) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .hero-glow-1 {
            position: absolute;
            top: -120px;
            left: -80px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
        }

        .hero-glow-2 {
            position: absolute;
            bottom: -150px;
            right: -100px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.10) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }

        .feature-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-grad {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .cta-glow {
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.3), 0 0 80px rgba(139, 92, 246, 0.15);
        }
    </style>
</head>

<body class="antialiased bg-white">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/85 backdrop-blur-xl border-b border-slate-100/80">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5">
                <img src="/images/logo-full.png" alt="Aperlex" class="h-8">
                <span class="font-bold text-lg tracking-tight text-slate-900">Aperlex</span>
            </a>
            <div class="flex items-center gap-3 text-sm">
                <div class="nav-lang">
                    <a href="{{ route('lang.switch', 'vi') }}"
                        class="nav-lang-btn {{ app()->getLocale() === 'vi' ? 'nav-lang-btn-active' : '' }}">VI</a>
                    <a href="{{ route('lang.switch', 'en') }}"
                        class="nav-lang-btn {{ app()->getLocale() === 'en' ? 'nav-lang-btn-active' : '' }}">EN</a>
                </div>
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
        </div>
    </nav>

    <!-- Hero -->
    <section class="relative overflow-hidden hero-grid pt-32 pb-20 md:pt-40 md:pb-28">
        <div class="hero-glow-1"></div>
        <div class="hero-glow-2"></div>
        <div class="relative max-w-6xl mx-auto px-6">
            <div class="grid gap-14 lg:grid-cols-2 items-center">
                <div class="space-y-8" style="animation: fadeSlideUp 600ms var(--ease-smooth) both;">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border text-sm font-medium"
                        style="background: var(--accent-soft); border-color: rgba(99, 102, 241, 0.2); color: var(--accent);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        {{ __('AI-Powered Project Management') }}
                    </div>
                    <h1
                        class="text-4xl md:text-5xl lg:text-[3.5rem] font-extrabold text-slate-900 leading-[1.1] tracking-tight">
                        {{ __('Manage projects') }}
                        <span
                            class="bg-gradient-to-r from-indigo-500 via-violet-500 to-purple-500 bg-clip-text text-transparent">{{ __('smarter') }}</span>,
                        {{ __('deliver faster') }}
                    </h1>
                    <p class="text-lg text-slate-600 max-w-xl leading-relaxed">
                        {{ __('Aperlex combines intelligent task management, real-time collaboration, and AI insights to help your team stay aligned and ship on time.') }}
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('register') }}" class="btn-primary !px-8 !py-3.5 !text-base cta-glow">
                            {{ __('Get started free') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ route('login') }}"
                            class="btn-secondary !px-8 !py-3.5 !text-base">{{ __('Sign in') }}</a>
                    </div>
                    <div class="flex items-center gap-8 pt-2">
                        <div class="text-center">
                            <p class="text-2xl font-extrabold stat-grad">99%</p>
                            <p class="text-xs text-slate-500 mt-1">{{ __('Uptime') }}</p>
                        </div>
                        <div class="w-px h-10 bg-slate-200"></div>
                        <div class="text-center">
                            <p class="text-2xl font-extrabold stat-grad">2x</p>
                            <p class="text-xs text-slate-500 mt-1">{{ __('Faster delivery') }}</p>
                        </div>
                        <div class="w-px h-10 bg-slate-200"></div>
                        <div class="text-center">
                            <p class="text-2xl font-extrabold stat-grad">AI</p>
                            <p class="text-xs text-slate-500 mt-1">{{ __('Powered') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Hero Card -->
                <div class="relative" style="animation: fadeSlideUp 700ms var(--ease-smooth) 150ms both;">
                    <div class="card-strong p-6 space-y-4">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                style="background: var(--gradient);">
                                <img src="/images/logo-full.png" alt="Aperlex"
                                    class="h-5 w-5 object-contain brightness-0 invert">
                            </div>
                            <span class="font-bold text-slate-800">{{ __('Project Overview') }}</span>
                        </div>
                        <div class="grid gap-3">
                            <div class="flex items-center justify-between p-4 rounded-xl border"
                                style="background: var(--accent-soft); border-color: rgba(99, 102, 241, 0.12);">
                                <div>
                                    <p class="text-xs font-medium" style="color: var(--accent);">
                                        {{ __('Active projects') }}
                                    </p>
                                    <p class="text-2xl font-bold text-indigo-700">12</p>
                                </div>
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                    style="background: rgba(99, 102, 241, 0.1);">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path
                                            d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between p-4 rounded-xl bg-emerald-50/60 border border-emerald-100/60">
                                <div>
                                    <p class="text-xs text-emerald-600 font-medium">{{ __('Tasks completed') }}</p>
                                    <p class="text-2xl font-bold text-emerald-700">156</p>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 11l3 3L22 4" />
                                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                                    </svg>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between p-4 rounded-xl bg-violet-50/60 border border-violet-100/60">
                                <div>
                                    <p class="text-xs text-violet-600 font-medium">{{ __('On-track delivery') }}</p>
                                    <p class="text-2xl font-bold text-violet-700">92%</p>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-24 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="text-sm uppercase tracking-widest font-bold mb-3" style="color: var(--accent);">
                    {{ __('Features') }}
                </p>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    {{ __('Everything your team needs') }}
                </h2>
                <p class="text-slate-500 mt-4 max-w-2xl mx-auto text-lg">
                    {{ __('From task management to AI-powered insights, Aperlex brings all your tools together.') }}
                </p>
            </div>
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @php
                    $features = [
                        ['icon' => '<path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/>', 'color' => 'indigo', 'title' => __('Role-based access'), 'desc' => __('Admins oversee all projects while users focus on their assigned work.')],
                        ['icon' => '<path d="M12 2v4m4.24 1.76l2.83-2.83M18 12h4m-4.24 4.24l2.83 2.83M12 18v4m-7.07-2.83l2.83-2.83M2 12h4m-.93-7.07l2.83 2.83"/><circle cx="12" cy="12" r="4"/>', 'color' => 'violet', 'title' => __('AI-Powered insights'), 'desc' => __('Get smart task suggestions, risk detection, and automated reports powered by AI.')],
                        ['icon' => '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>', 'color' => 'emerald', 'title' => __('Real-time collaboration'), 'desc' => __('Chat, comment, and collaborate with your team in real time.')],
                        ['icon' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="4" rx="1"/><rect x="14" y="10" width="7" height="11" rx="1"/><rect x="3" y="13" width="7" height="8" rx="1"/>', 'color' => 'sky', 'title' => __('Visual dashboards'), 'desc' => __('Track status, milestones, and deadlines in a unified view with charts.')],
                        ['icon' => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>', 'color' => 'amber', 'title' => __('Email notifications'), 'desc' => __('Stay updated with task assignments, comments, and project changes.')],
                        ['icon' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>', 'color' => 'rose', 'title' => __('Bilingual interface'), 'desc' => __('Switch between Vietnamese and English with one click.')],
                    ];
                    $colorMap = [
                        'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'border' => 'border-indigo-100'],
                        'violet' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'border' => 'border-violet-100'],
                        'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-100'],
                        'sky' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-600', 'border' => 'border-sky-100'],
                        'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-100'],
                        'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-100'],
                    ];
                @endphp
                @foreach($features as $i => $f)
                    <div class="card-strong p-6 group hover:border-indigo-200 transition-all"
                        style="animation: fadeSlideUp 400ms var(--ease-smooth) {{ ($i * 80) }}ms both;">
                        <div class="feature-icon-wrap {{ $colorMap[$f['color']]['bg'] }} mb-4">
                            <svg class="w-6 h-6 {{ $colorMap[$f['color']]['text'] }}" fill="none" stroke="currentColor"
                                stroke-width="1.75" viewBox="0 0 24 24">{!! $f['icon'] !!}</svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-2">{{ $f['title'] }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Author -->
    <section class="py-24 bg-slate-50/50">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-12">
                <p class="text-sm uppercase tracking-widest font-bold mb-3" style="color: var(--accent);">
                    {{ __('Developer') }}
                </p>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    {{ __('About the Author') }}
                </h2>
            </div>
            <div class="max-w-3xl mx-auto">
                <div class="card-strong overflow-hidden">
                    <div class="h-28" style="background: var(--gradient); position: relative;">
                        <div class="absolute inset-0 opacity-20"
                            style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;40&quot; height=&quot;40&quot; viewBox=&quot;0 0 40 40&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;%23fff&quot; fill-opacity=&quot;0.3&quot;%3E%3Cpath d=&quot;M20 20.5V18H0v-2h20v-2l2 3.5-2 3z&quot;/%3E%3C/g%3E%3C/svg%3E');">
                        </div>
                    </div>
                    <div class="px-8 pb-8 -mt-14 relative">
                        <div class="flex flex-col sm:flex-row items-start gap-6">
                            <div class="w-24 h-24 rounded-2xl border-4 border-white shadow-lg overflow-hidden flex-shrink-0 flex items-center justify-center"
                                style="background: var(--gradient);">
                                <span class="text-3xl font-extrabold text-white">BD</span>
                            </div>
                            <div class="pt-2 sm:pt-6">
                                <h3 class="text-2xl font-extrabold text-slate-900">Nguyễn Bá Duy</h3>
                                <p class="font-bold text-sm mt-1" style="color: var(--accent);">Full-stack Developer &
                                    AI Enthusiast</p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3 text-sm text-slate-600">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path
                                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                                <span>{{ __('Student at') }} <strong>74DCTT23</strong> —
                                    {{ __('Faculty of IT, University of Transport Technology') }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0" style="color: var(--accent);" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>basduy05@gmail.com</span>
                            </div>
                        </div>

                        <p class="mt-5 text-sm text-slate-600 leading-relaxed">
                            {{ __('A passionate young developer with a burning love for technology, always exploring and applying creative solutions to real-world problems. With a special interest in AI, web development, and educational technology products.') }}
                        </p>

                        <div class="mt-6 flex flex-wrap gap-2">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200/60 text-xs font-bold text-amber-700">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                Google Student Ambassador 2026
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 border border-red-200/60 text-xs font-bold text-red-700">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm-3 8a22.963 22.963 0 005 .56 22.96 22.96 0 005-.56V17a2 2 0 01-2 2H7a2 2 0 01-2-2v-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ __('Youth Union Executive Committee — HCM City') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-24 relative overflow-hidden">
        <div class="absolute inset-0" style="background: var(--sidebar-bg);"></div>
        <div class="absolute inset-0"
            style="background: radial-gradient(ellipse at 30% 50%, rgba(99, 102, 241, 0.2) 0%, transparent 60%), radial-gradient(ellipse at 70% 30%, rgba(139, 92, 246, 0.15) 0%, transparent 60%);">
        </div>
        <div class="relative max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4 tracking-tight">
                {{ __('Ready to transform your project management?') }}
            </h2>
            <p class="text-lg text-slate-400 mb-10 max-w-2xl mx-auto">
                {{ __('Join Aperlex today and experience the power of AI-driven project management.') }}
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('register') }}"
                    class="inline-flex items-center gap-2 px-8 py-4 rounded-xl text-sm font-bold bg-white shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all"
                    style="color: var(--accent);">
                    {{ __('Start for free') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-2 px-8 py-4 rounded-xl text-sm font-bold text-white border-2 border-white/20 hover:bg-white/10 transition-all">
                    {{ __('Sign in') }}
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-50 border-t border-slate-100 py-10">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-2.5">
                    <img src="/images/logo-full.png" alt="Aperlex" class="h-7">
                    <span class="font-bold text-slate-900 tracking-tight">Aperlex</span>
                </div>
                <p class="text-sm text-slate-500">© {{ date('Y') }} Aperlex. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </footer>
</body>

</html>