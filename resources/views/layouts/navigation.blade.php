<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-slate-800">QhorizonPM</a>
                <div class="hidden md:flex items-center gap-4 text-sm font-medium text-slate-600">
                    <a href="{{ route('dashboard') }}" class="nav-pill {{ request()->routeIs('dashboard') ? 'nav-pill-active' : '' }}">{{ __('Dashboard') }}</a>
                    <a href="{{ route('projects.index') }}" class="nav-pill {{ request()->routeIs('projects.*') ? 'nav-pill-active' : '' }}">{{ __('Projects') }}</a>
                    <a href="{{ route('tasks.index') }}" class="nav-pill {{ request()->routeIs('tasks.index') ? 'nav-pill-active' : '' }}">{{ __('Tasks') }}</a>
                    <a href="{{ route('messenger.index') }}" class="nav-pill {{ request()->routeIs('messenger.*') ? 'nav-pill-active' : '' }}">{{ __('Messenger') }}</a>
                    @if (Auth::user()?->isAdmin())
                        <a href="{{ route('admin.users.index') }}" class="nav-pill {{ request()->routeIs('admin.users.*') ? 'nav-pill-active' : '' }}">{{ __('Users') }}</a>
                    @endif
                </div>
            </div>

            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('notifications.index') }}" class="relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:text-slate-900 hover:border-slate-300 transition" title="{{ __('Notifications') }}" aria-label="{{ __('Notifications') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5" />
                        <path d="M9 17a3 3 0 0 0 6 0" />
                    </svg>
                    @php($unreadCount = Auth::user()?->unreadNotificationsCountSafe() ?? 0)
                    <span id="nav-unread-count" class="absolute -top-2 -right-3 text-[10px] bg-rose-500 text-white px-2 py-0.5 rounded-full {{ $unreadCount > 0 ? '' : 'hidden' }}">{{ $unreadCount }}</span>
                </a>
                <div class="flex items-center gap-1 text-[10px] font-semibold text-slate-500">
                    <a href="{{ route('lang.switch', 'vi') }}" class="h-6 min-w-7 px-1.5 inline-flex items-center justify-center rounded-full {{ app()->getLocale() === 'vi' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200' }}">VI</a>
                    <a href="{{ route('lang.switch', 'en') }}" class="h-6 min-w-7 px-1.5 inline-flex items-center justify-center rounded-full {{ app()->getLocale() === 'en' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200' }}">EN</a>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-2 border border-slate-200 text-sm rounded-full bg-white shadow-sm text-slate-700">
                        {{ Auth::user()->name }}
                    </span>
                    <a href="{{ route('profile.edit') }}" class="btn-secondary text-xs px-3 py-2">{{ __('Profile') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-secondary text-xs px-3 py-2">{{ __('Log Out') }}</button>
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
            <div class="flex items-center gap-2 pt-2">
                <a href="{{ route('lang.switch', 'vi') }}" class="h-6 min-w-7 px-1.5 inline-flex items-center justify-center text-[10px] font-semibold rounded-full {{ app()->getLocale() === 'vi' ? 'bg-slate-900 text-white' : 'border border-slate-200' }}">VI</a>
                <a href="{{ route('lang.switch', 'en') }}" class="h-6 min-w-7 px-1.5 inline-flex items-center justify-center text-[10px] font-semibold rounded-full {{ app()->getLocale() === 'en' ? 'bg-slate-900 text-white' : 'border border-slate-200' }}">EN</a>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button type="submit" class="text-left text-slate-600">{{ __('Log Out') }}</button>
            </form>
        </div>
    </div>
</nav>
