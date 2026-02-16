<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-slate-800">QhorizonPM</a>
                <div class="hidden md:flex items-center gap-4 text-sm font-medium text-slate-600">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-slate-900' : '' }}">{{ __('Dashboard') }}</a>
                    <a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects.*') ? 'text-slate-900' : '' }}">{{ __('Projects') }}</a>
                    <a href="{{ route('tasks.index') }}" class="{{ request()->routeIs('tasks.index') ? 'text-slate-900' : '' }}">{{ __('Tasks') }}</a>
                    <a href="{{ route('messenger.index') }}" class="{{ request()->routeIs('messenger.*') ? 'text-slate-900' : '' }}">{{ __('Messenger') }}</a>
                    @if (Auth::user()?->isAdmin())
                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'text-slate-900' : '' }}">{{ __('Users') }}</a>
                    @endif
                </div>
            </div>

            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('notifications.index') }}" class="relative text-sm text-slate-600">
                    {{ __('Notifications') }}
                    @php($unreadCount = Auth::user()?->unreadNotifications()->count() ?? 0)
                    <span id="nav-unread-count" class="absolute -top-2 -right-3 text-[10px] bg-rose-500 text-white px-2 py-0.5 rounded-full {{ $unreadCount > 0 ? '' : 'hidden' }}">{{ $unreadCount }}</span>
                </a>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('lang.switch', 'vi') }}" class="px-2 py-1 rounded-full {{ app()->getLocale() === 'vi' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200' }}">VI</a>
                    <a href="{{ route('lang.switch', 'en') }}" class="px-2 py-1 rounded-full {{ app()->getLocale() === 'en' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200' }}">EN</a>
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
            <a class="block" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
            <a class="block" href="{{ route('projects.index') }}">{{ __('Projects') }}</a>
            <a class="block" href="{{ route('tasks.index') }}">{{ __('Tasks') }}</a>
            <a class="block" href="{{ route('messenger.index') }}">{{ __('Messenger') }}</a>
            <a class="block" href="{{ route('notifications.index') }}">{{ __('Notifications') }}</a>
            @if (Auth::user()?->isAdmin())
                <a class="block" href="{{ route('admin.users.index') }}">{{ __('Users') }}</a>
            @endif
            <a class="block" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
            <div class="flex items-center gap-2 pt-2">
                <a href="{{ route('lang.switch', 'vi') }}" class="px-2 py-1 rounded-full {{ app()->getLocale() === 'vi' ? 'bg-slate-900 text-white' : 'border border-slate-200' }}">VI</a>
                <a href="{{ route('lang.switch', 'en') }}" class="px-2 py-1 rounded-full {{ app()->getLocale() === 'en' ? 'bg-slate-900 text-white' : 'border border-slate-200' }}">EN</a>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button type="submit" class="text-left text-slate-600">{{ __('Log Out') }}</button>
            </form>
        </div>
    </div>
</nav>
