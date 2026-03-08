{{-- ── Sidebar Navigation ── --}}
<aside class="sidebar" :class="{ 'collapsed': sidebarCollapsed, 'mobile-open': mobileOpen }">

    {{-- ── Logo ── --}}
    <div class="sidebar-logo">
        <a wire:navigate href="{{ route('dashboard') }}" class="flex items-center w-full">
            <img src="/images/logo-full.png" alt="Aperlex" class="h-8 object-contain shrink-0"
                :class="{ 'mx-auto': sidebarCollapsed }">
        </a>
    </div>

    {{-- ── Navigation Links ── --}}
    <nav class="sidebar-nav">
        <div class="sidebar-section-label">{{ __('Main') }}</div>

        <a wire:navigate href="{{ route('dashboard') }}"
            class="sidebar-link relative {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-link-icon" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.75">
                <rect x="3" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="3" width="7" height="4" rx="1" />
                <rect x="14" y="10" width="7" height="11" rx="1" />
                <rect x="3" y="13" width="7" height="8" rx="1" />
            </svg>
            <span class="sidebar-link-text">{{ __('Dashboard') }}</span>
        </a>

        <a wire:navigate href="{{ route('projects.index') }}"
            class="sidebar-link relative {{ request()->routeIs('projects.*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-link-icon" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.75">
                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
            </svg>
            <span class="sidebar-link-text">{{ __('Projects') }}</span>
        </a>

        <a wire:navigate href="{{ route('tasks.index') }}"
            class="sidebar-link relative {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-link-icon" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.75">
                <path d="M9 11 12 14 22 4" />
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
            </svg>
            <span class="sidebar-link-text">{{ __('Tasks') }}</span>
        </a>

        <a wire:navigate href="{{ route('messenger.index') }}"
            class="sidebar-link relative {{ request()->routeIs('messenger.*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-link-icon" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.75">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
            </svg>
            <span class="sidebar-link-text">{{ __('Messenger') }}</span>
        </a>

        <a wire:navigate href="{{ route('notifications.index') }}"
            class="sidebar-link relative {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-link-icon" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.75">
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            <span class="sidebar-link-text">{{ __('Notifications') }}</span>
        </a>

        @if (Auth::user()?->isAdmin())
            <div class="sidebar-section-label">{{ __('Administration') }}</div>

            <a wire:navigate href="{{ route('admin.users.index') }}"
                class="sidebar-link relative {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-link-icon" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.75">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <span class="sidebar-link-text">{{ __('Users') }}</span>
            </a>

            <a wire:navigate href="{{ route('admin.settings.ai.edit') }}"
                class="sidebar-link relative {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-link-icon" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.75">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                </svg>
                <span class="sidebar-link-text">{{ __('Settings') }}</span>
            </a>

            <a wire:navigate href="{{ route('admin.email-composer') }}"
                class="sidebar-link relative {{ request()->routeIs('admin.email-composer*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="sidebar-link-icon" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.75">
                    <rect width="20" height="16" x="2" y="4" rx="2" />
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                </svg>
                <span class="sidebar-link-text">{{ __('Email Composer') }}</span>
            </a>
        @endif
    </nav>

    {{-- ── Sidebar Footer ── --}}
    <div class="sidebar-footer">
        <div class="flex items-center justify-between">
            <a wire:navigate href="{{ route('profile.edit') }}"
                class="sidebar-link relative flex-1 {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <div
                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-xs font-bold text-white shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="sidebar-link-text min-w-0">
                    <p class="text-sm font-medium text-slate-700 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </a>
            <button @click="sidebarCollapsed = !sidebarCollapsed" class="sidebar-toggle hidden md:flex shrink-0"
                title="{{ __('Toggle sidebar') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform"
                    :class="{ 'rotate-180': sidebarCollapsed }" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m11 17-5-5 5-5" />
                    <path d="m18 17-5-5 5-5" />
                </svg>
            </button>
        </div>
    </div>
</aside>