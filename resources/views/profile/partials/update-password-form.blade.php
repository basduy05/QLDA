<div>
    <h3 class="text-lg font-bold text-slate-900 mb-4">{{ __('Update Password') }}</h3>
    <p class="text-sm text-slate-600 mb-6">{{ __('Use a long, random password for security.') }}</p>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="w-full px-3 py-2 border border-slate-300 rounded-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500" autocomplete="current-password" />
            @error('current_password', 'updatePassword')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-slate-700 mb-1">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="w-full px-3 py-2 border border-slate-300 rounded-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500" autocomplete="new-password" />
            @error('password', 'updatePassword')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full px-3 py-2 border border-slate-300 rounded-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500" autocomplete="new-password" />
            @error('password_confirmation', 'updatePassword')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 border-t border-slate-200 flex items-center gap-3">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                {{ __('Update Password') }}
            </button>
            @if (session('status') === 'password-updated')
                <p class="text-sm text-emerald-600">{{ __('Password updated successfully.') }}</p>
            @endif
        </div>
    </form>
</div>
