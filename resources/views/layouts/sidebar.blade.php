<!-- Sidebar -->
<aside class="w-64 bg-white border-r border-slate-200 flex flex-col">
    <!-- Logo Section -->
    <div class="px-6 py-6 border-b border-slate-200">
        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            QhorizonPM
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
        <!-- Main Navigation -->
        <div>
            <div class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">{{ __('Main') }}</div>
            
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z"/></svg>
                <span>{{ __('Dashboard') }}</span>
            </a>

            <a href="{{ route('projects.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('projects.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ request()->routeIs('projects.*') ? 'text-blue-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h18M3 12h18M3 17h12"/></svg>
                <span>{{ __('Projects') }}</span>
            </a>

            <a href="{{ route('tasks.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('tasks.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ request()->routeIs('tasks.*') ? 'text-blue-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                <span>{{ __('Tasks') }}</span>
            </a>

            <a href="{{ route('messenger.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('messenger.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ request()->routeIs('messenger.*') ? 'text-blue-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10Z"/></svg>
                <span>{{ __('Messenger') }}</span>
            </a>

            <a href="{{ route('ai.chat.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('ai.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ request()->routeIs('ai.*') ? 'text-blue-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c-6.627 0-12 4.925-12 11s5.373 11 12 11 12-4.925 12-11-5.373-11-12-11zm0 19c-5.514 0-10-4.29-10-9 0-4.711 4.486-9 10-9s10 4.289 10 9c0 4.71-4.486 9-10 9z"/><path d="M7 9h2v2H7zM13 9h2v2h-2zM7 13c0 1.104.895 2 2 2h4c1.105 0 2-.896 2-2"/></svg>
                <span>{{ __('AI Assistant') }}</span>
            </a>
        </div>

        <!-- Admin Section -->
        @if (Auth::user()?->isAdmin())
            <div class="mt-8">
                <div class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">{{ __('Admin') }}</div>
                
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
                    <span>{{ __('Users') }}</span>
                </a>

                <a href="{{ route('admin.settings.ai.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ request()->routeIs('admin.settings.*') ? 'text-blue-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"/><path d="m16.24 7.76 2.83-2.83"/><path d="M18 12h4"/><path d="m16.24 16.24 2.83 2.83"/><path d="M12 18v4"/><path d="m4.93 19.07 2.83-2.83"/><path d="M2 12h4"/><path d="m4.93 4.93 2.83 2.83"/><circle cx="12" cy="12" r="4"/></svg>
                    <span>{{ __('Settings') }}</span>
                </a>
            </div>
        @endif
    </nav>

    <!-- Bottom User Section -->
    <div class="border-t border-slate-200 px-3 py-4 space-y-3">
        <a href="{{ route('notifications.index') }}" class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 transition-colors relative group">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.73 21a2 2 0 0 1-3.46 0" /></svg>
                <span class="text-sm font-medium text-slate-700">{{ __('Notifications') }}</span>
            </div>
            @php($unreadCount = Auth::user()?->unreadNotificationsCountSafe() ?? 0)
            @if($unreadCount > 0)
                <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-white text-xs font-semibold">{{ $unreadCount }}</span>
            @endif
        </a>

        <div class="flex items-center gap-2 px-2 py-2 rounded-lg bg-slate-50">
            <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-900 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('profile.edit') }}" class="flex-1 px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors border border-slate-200 text-center">
                {{ __('Profile') }}
            </a>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors border border-slate-200">
                    {{ __('Logout') }}
                </button>
            </form>
        </div>

        <!-- Language Switcher -->
        <div class="border-t border-slate-200 pt-3 flex gap-2">
            <a href="{{ route('lang.switch', 'vi') }}" class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-center {{ app()->getLocale() === 'vi' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-colors">
                VI
            </a>
            <a href="{{ route('lang.switch', 'en') }}" class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-center {{ app()->getLocale() === 'en' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-colors">
                EN
            </a>
        </div>

