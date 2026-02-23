<div>
    <h3 class="text-lg font-bold text-slate-900 mb-4">{{ __('Delete Account') }}</h3>
    <p class="text-sm text-slate-600 mb-6">{{ __('Once deleted, all account data will be permanently removed. Please download any needed data first.') }}</p>

    <button 
        type="button"
        onclick="document.getElementById('deleteModal').classList.remove('hidden')"
        class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 text-sm font-medium"
    >
        {{ __('Delete Account') }}
    </button>

    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
            <h3 class="text-lg font-bold text-slate-900 mb-2">{{ __('Delete Account?') }}</h3>
            <p class="text-sm text-slate-600 mb-6">{{ __('Enter your password to confirm account deletion.') }}</p>

            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('delete')

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Password') }}</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="{{ __('Password') }}"
                        required
                    />
                    @error('password', 'userDeletion')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <button
                        type="button"
                        onclick="document.getElementById('deleteModal').classList.add('hidden')"
                        class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 text-sm font-medium"
                    >
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 text-sm font-medium">
                        {{ __('Delete') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
