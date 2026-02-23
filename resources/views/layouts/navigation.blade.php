<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-slate-800">QhorizonPM</a>
                <div class="hidden md:flex items-center gap-2 text-sm font-medium text-slate-600">
                    <a href="{{ route('dashboard') }}" class="nav-pill {{ request()->routeIs('dashboard') ? 'nav-pill-active' : '' }}" title="{{ __('Dashboard') }}" aria-label="{{ __('Dashboard') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z"/></svg>
                        <span class="hidden lg:inline">{{ __('Dashboard') }}</span>
                    </a>
                    <a href="{{ route('projects.index') }}" class="nav-pill {{ request()->routeIs('projects.*') ? 'nav-pill-active' : '' }}" title="{{ __('Projects') }}" aria-label="{{ __('Projects') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h18M3 12h18M3 17h12"/></svg>
                        <span class="hidden lg:inline">{{ __('Projects') }}</span>
                    </a>
                    <a href="{{ route('tasks.index') }}" class="nav-pill {{ request()->routeIs('tasks.index') ? 'nav-pill-active' : '' }}" title="{{ __('Tasks') }}" aria-label="{{ __('Tasks') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        <span class="hidden lg:inline">{{ __('Tasks') }}</span>
                    </a>
                    <a href="{{ route('messenger.index') }}" class="nav-pill {{ request()->routeIs('messenger.*') ? 'nav-pill-active' : '' }}" title="{{ __('Messenger') }}" aria-label="{{ __('Messenger') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10Z"/></svg>
                        <span class="hidden lg:inline">{{ __('Messenger') }}</span>
                    </a>
                    @if (Auth::user()?->isAdmin())
                        <a href="{{ route('admin.users.index') }}" class="nav-pill {{ request()->routeIs('admin.users.*') ? 'nav-pill-active' : '' }}" title="{{ __('Users') }}" aria-label="{{ __('Users') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
                            <span class="hidden lg:inline">{{ __('Users') }}</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden md:flex items-center gap-2">
                <a href="{{ route('notifications.index') }}" class="nav-action-btn" title="{{ __('Notifications') }}" aria-label="{{ __('Notifications') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
                        <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                    </svg>
                    @php($unreadCount = Auth::user()?->unreadNotificationsCountSafe() ?? 0)
                    <span id="nav-unread-count" class="absolute -top-2 -right-3 text-[10px] bg-rose-500 text-white px-2 py-0.5 rounded-full {{ $unreadCount > 0 ? '' : 'hidden' }}">{{ $unreadCount }}</span>
                </a>
                <div class="nav-lang" aria-label="{{ __('Locale') }}">
                    <a href="{{ route('lang.switch', 'vi') }}" class="nav-lang-btn {{ app()->getLocale() === 'vi' ? 'nav-lang-btn-active' : '' }}">VI</a>
                    <a href="{{ route('lang.switch', 'en') }}" class="nav-lang-btn {{ app()->getLocale() === 'en' ? 'nav-lang-btn-active' : '' }}">EN</a>
                </div>
                <div class="flex items-center gap-2">
                    <span class="nav-profile-chip">
                        {{ Auth::user()->name }}
                    </span>
                    <a href="{{ route('profile.edit') }}" class="btn-secondary h-9 text-xs !px-3">{{ __('Profile') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-secondary h-9 text-xs !px-3">{{ __('Log Out') }}</button>
                    </form>
                </div>
            </div>

            <div class="md:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-500 hover:text-slate-700">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden border-t border-slate-100 bg-white">
        <div class="px-6 py-4 space-y-2 text-sm text-slate-600">
            <a class="nav-pill block" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
            <a class="nav-pill block" href="{{ route('projects.index') }}">{{ __('Projects') }}</a>
            <a class="nav-pill block" href="{{ route('tasks.index') }}">{{ __('Tasks') }}</a>
            <a class="nav-pill block" href="{{ route('messenger.index') }}">{{ __('Messenger') }}</a>
            <a class="nav-pill block" href="{{ route('notifications.index') }}">{{ __('Notifications') }}</a>
            @if (Auth::user()?->isAdmin())
                <a class="nav-pill block" href="{{ route('admin.users.index') }}">{{ __('Users') }}</a>
            @endif
            <a class="nav-pill block" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
            <div class="pt-2">
                <div class="nav-lang">
                    <a href="{{ route('lang.switch', 'vi') }}" class="nav-lang-btn {{ app()->getLocale() === 'vi' ? 'nav-lang-btn-active' : '' }}">VI</a>
                    <a href="{{ route('lang.switch', 'en') }}" class="nav-lang-btn {{ app()->getLocale() === 'en' ? 'nav-lang-btn-active' : '' }}">EN</a>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button type="submit" class="text-left text-slate-600">{{ __('Log Out') }}</button>
            </form>
        </div>
    </div>
</nav>
