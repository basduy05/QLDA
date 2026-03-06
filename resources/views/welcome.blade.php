<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Aperlex') }} â€” {{ __('Smart Project Management') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/images/logo-full.png">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|fraunces:400,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .hero-gradient {
                background: linear-gradient(135deg, #ecfeff 0%, #ede9fe 50%, #f5f3ff 100%);
            }
            .hero-blob-1 {
                position: absolute; top: -80px; left: -60px; width: 320px; height: 320px;
                background: radial-gradient(circle, rgba(0,229,255,0.15) 0%, transparent 70%);
                border-radius: 50%; filter: blur(40px); pointer-events: none;
            }
            .hero-blob-2 {
                position: absolute; bottom: -100px; right: -80px; width: 400px; height: 400px;
                background: radial-gradient(circle, rgba(124,58,237,0.12) 0%, transparent 70%);
                border-radius: 50%; filter: blur(50px); pointer-events: none;
            }
            .feature-icon {
                width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
            }
            .stat-number {
                background: linear-gradient(135deg, #0891b2, #6366f1);
                -webkit-background-clip: text; -webkit-text-fill-color: transparent;
                background-clip: text;
            }
        </style>
    </head>
    <body class="antialiased bg-white">
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-lg border-b border-slate-100/80">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2.5">
                    <img src="/images/logo-full.png" alt="Aperlex" class="h-8">
                </a>
                <div class="flex items-center gap-3 text-sm">
                    <a href="{{ route('lang.switch', 'vi') }}" class="px-3 py-1 rounded-full transition {{ app()->getLocale() === 'vi' ? 'bg-gradient-to-r from-cyan-600 to-indigo-600 text-white' : 'border border-slate-200 text-slate-600 hover:border-slate-300' }}">VI</a>
                    <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1 rounded-full transition {{ app()->getLocale() === 'en' ? 'bg-gradient-to-r from-cyan-600 to-indigo-600 text-white' : 'border border-slate-200 text-slate-600 hover:border-slate-300' }}">EN</a>
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

        <!-- Hero Section -->
        <section class="relative overflow-hidden hero-gradient pt-32 pb-20 md:pt-40 md:pb-28">
            <div class="hero-blob-1"></div>
            <div class="hero-blob-2"></div>
            <div class="relative max-w-6xl mx-auto px-6">
                <div class="grid gap-12 lg:grid-cols-2 items-center">
                    <div class="space-y-8">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/80 border border-cyan-200/60 text-sm text-cyan-700 font-medium shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            {{ __('AI-Powered Project Management') }}
                        </div>
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-slate-900 leading-tight">
                            {{ __('Manage projects') }}
                            <span class="bg-gradient-to-r from-cyan-500 via-indigo-500 to-purple-600 bg-clip-text text-transparent">{{ __('smarter') }}</span>,
                            {{ __('deliver faster') }}
                        </h1>
                        <p class="text-lg text-slate-600 max-w-xl leading-relaxed">
                            {{ __('Aperlex combines intelligent task management, real-time collaboration, and AI insights to help your team stay aligned and ship on time.') }}
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-7 py-3 rounded-full text-sm font-semibold text-white shadow-lg bg-gradient-to-r from-cyan-500 via-indigo-500 to-purple-600 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                                {{ __('Get started free') }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                            <a href="{{ route('login') }}" class="btn-secondary !px-7 !py-3">{{ __('Sign in') }}</a>
                        </div>
                        <div class="flex items-center gap-6 pt-2">
                            <div class="text-center">
                                <p class="text-2xl font-bold stat-number">99%</p>
                                <p class="text-xs text-slate-500">{{ __('Uptime') }}</p>
                            </div>
                            <div class="w-px h-8 bg-slate-200"></div>
                            <div class="text-center">
                                <p class="text-2xl font-bold stat-number">2x</p>
                                <p class="text-xs text-slate-500">{{ __('Faster delivery') }}</p>
                            </div>
                            <div class="w-px h-8 bg-slate-200"></div>
                            <div class="text-center">
                                <p class="text-2xl font-bold stat-number">AI</p>
                                <p class="text-xs text-slate-500">{{ __('Powered') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Hero visual card -->
                    <div class="relative">
                        <div class="card-strong p-5 space-y-4">
                            <div class="flex items-center gap-3 mb-4">
                                <img src="/images/logo-full.png" alt="Aperlex" class="h-6">
                                <span class="font-semibold text-slate-800">{{ __('Project Overview') }}</span>
                            </div>
                            <div class="grid gap-3">
                                <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-cyan-50 to-cyan-100/40 border border-cyan-200/30">
                                    <div>
                                        <p class="text-xs text-cyan-600 font-medium">{{ __('Active projects') }}</p>
                                        <p class="text-2xl font-bold text-cyan-700">12</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7h18M3 12h18M3 17h12"/></svg>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-indigo-50 to-indigo-100/40 border border-indigo-200/30">
                                    <div>
                                        <p class="text-xs text-indigo-600 font-medium">{{ __('Tasks completed') }}</p>
                                        <p class="text-2xl font-bold text-indigo-700">156</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-purple-50 to-purple-100/40 border border-purple-200/30">
                                    <div>
                                        <p class="text-xs text-purple-600 font-medium">{{ __('On-track delivery') }}</p>
                                        <p class="text-2xl font-bold text-purple-700">92%</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white">
            <div class="max-w-6xl mx-auto px-6">
                <div class="text-center mb-14">
                    <p class="text-sm uppercase tracking-widest text-indigo-600 font-semibold mb-3">{{ __('Features') }}</p>
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900">{{ __('Everything your team needs') }}</h2>
                    <p class="text-slate-500 mt-3 max-w-2xl mx-auto">{{ __('From task management to AI-powered insights, Aperlex brings all your tools together.') }}</p>
                </div>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="card-strong p-5 group">
                        <div class="feature-icon bg-gradient-to-br from-cyan-100 to-cyan-200/50 mb-4">
                            <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-1">{{ __('Role-based access') }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ __('Admins oversee all projects while users focus on their assigned work.') }}</p>
                    </div>
                    <!-- Feature 2 -->
                    <div class="card-strong p-5 group">
                        <div class="feature-icon bg-gradient-to-br from-indigo-100 to-indigo-200/50 mb-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 2v4m4.24 1.76l2.83-2.83M18 12h4m-4.24 4.24l2.83 2.83M12 18v4m-7.07-2.83l2.83-2.83M2 12h4m-.93-7.07l2.83 2.83"/><circle cx="12" cy="12" r="4"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-1">{{ __('AI-Powered insights') }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ __('Get smart task suggestions, risk detection, and automated reports powered by AI.') }}</p>
                    </div>
                    <!-- Feature 3 -->
                    <div class="card-strong p-5 group">
                        <div class="feature-icon bg-gradient-to-br from-purple-100 to-purple-200/50 mb-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-1">{{ __('Real-time collaboration') }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ __('Chat, comment, and collaborate with your team in real time.') }}</p>
                    </div>
                    <!-- Feature 4 -->
                    <div class="card-strong p-5 group">
                        <div class="feature-icon bg-gradient-to-br from-cyan-100 to-indigo-200/50 mb-4">
                            <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-1">{{ __('Visual dashboards') }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ __('Track status, milestones, and deadlines in a unified view with charts.') }}</p>
                    </div>
                    <!-- Feature 5 -->
                    <div class="card-strong p-5 group">
                        <div class="feature-icon bg-gradient-to-br from-indigo-100 to-purple-200/50 mb-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-1">{{ __('Email notifications') }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ __('Stay updated with task assignments, comments, and project changes.') }}</p>
                    </div>
                    <!-- Feature 6 -->
                    <div class="card-strong p-5 group">
                        <div class="feature-icon bg-gradient-to-br from-purple-100 to-cyan-200/50 mb-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-1">{{ __('Bilingual interface') }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ __('Switch between Vietnamese and English with one click.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Author Section -->
        <section class="py-20 bg-slate-50/50">
            <div class="max-w-6xl mx-auto px-6">
                <div class="text-center mb-10">
                    <p class="text-sm uppercase tracking-widest text-indigo-600 font-semibold mb-3">{{ __('Developer') }}</p>
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900">{{ __('About the Author') }}</h2>
                </div>
                <div class="max-w-3xl mx-auto">
                    <div class="card-strong overflow-hidden">
                        <div class="h-32 bg-gradient-to-r from-cyan-500 via-indigo-500 to-purple-600 relative">
                            <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;40&quot; height=&quot;40&quot; viewBox=&quot;0 0 40 40&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;%23fff&quot; fill-opacity=&quot;0.3&quot;%3E%3Cpath d=&quot;M20 20.5V18H0v-2h20v-2l2 3.5-2 3z&quot;/%3E%3C/g%3E%3C/svg%3E');"></div>
                        </div>
                        <div class="px-8 pb-8 -mt-16 relative">
                            <div class="flex flex-col sm:flex-row items-start gap-6">
                                <div class="w-28 h-28 rounded-2xl border-4 border-white shadow-lg overflow-hidden bg-gradient-to-br from-cyan-100 to-indigo-100 flex-shrink-0 flex items-center justify-center">
                                    <span class="text-4xl font-bold bg-gradient-to-br from-cyan-600 to-indigo-600 bg-clip-text text-transparent">BD</span>
                                </div>
                                <div class="pt-2 sm:pt-8">
                                    <h3 class="text-2xl font-bold text-slate-900">Nguyá»…n BÃ¡ Duy</h3>
                                    <p class="text-indigo-600 font-semibold text-sm mt-1">Full-stack Developer & AI Enthusiast</p>
                                </div>
                            </div>

                            <div class="mt-6 space-y-3 text-sm text-slate-600">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-cyan-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                    <span>{{ __('Student at') }} <strong>74DCTT23</strong> â€” {{ __('Faculty of IT, University of Transport Technology') }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-cyan-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span>basduy05@gmail.com</span>
                                </div>
                            </div>

                            <p class="mt-5 text-sm text-slate-600 leading-relaxed">
                                {{ __('A passionate young developer with a burning love for technology, always exploring and applying creative solutions to real-world problems. With a special interest in AI, web development, and educational technology products.') }}
                            </p>

                            <div class="mt-6 flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 border border-amber-200/60 text-xs font-semibold text-amber-700">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    Google Student Ambassador 2026
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 border border-red-200/60 text-xs font-semibold text-red-700">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm-3 8a22.963 22.963 0 005 .56 22.96 22.96 0 005-.56V17a2 2 0 01-2 2H7a2 2 0 01-2-2v-4z" clip-rule="evenodd"/></svg>
                                    {{ __('Youth Union Executive Committee â€” HCM City') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-cyan-600 via-indigo-600 to-purple-700"></div>
            <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.4&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            <div class="relative max-w-4xl mx-auto px-6 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">{{ __('Ready to transform your project management?') }}</h2>
                <p class="text-lg text-white/80 mb-8 max-w-2xl mx-auto">{{ __('Join Aperlex today and experience the power of AI-driven project management.') }}</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full text-sm font-semibold bg-white text-indigo-700 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        {{ __('Start for free') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full text-sm font-semibold text-white border-2 border-white/30 hover:bg-white/10 transition-all">
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
                    </div>
                    <p class="text-sm text-slate-500">Â© {{ date('Y') }} Aperlex. {{ __('All rights reserved.') }}</p>
                </div>
            </div>
        </footer>
    </body>
</html>
