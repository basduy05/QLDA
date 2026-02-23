<div>
    <h3 class="text-lg font-bold text-slate-900 mb-4">{{ __('Profile Information') }}</h3>
    <p class="text-sm text-slate-600 mb-6">{{ __('Update your account details and email address.') }}</p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}" style="display:none;">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="w-full px-3 py-2 border border-slate-300 rounded-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @error('name')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="w-full px-3 py-2 border border-slate-300 rounded-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @error('email')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="underline text-sm text-yellow-700 hover:text-yellow-900 ml-1">
                            {{ __('Click here to resend the verification email.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm text-emerald-700">{{ __('A new verification link has been sent.') }}</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="pt-4 border-t border-slate-200 flex items-center gap-3">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                {{ __('Save') }}
            </button>
            @if (session('status') === 'profile-updated')
                <p class="text-sm text-emerald-600">{{ __('Saved successfully.') }}</p>
            @endif
        </div>
    </form>
</div>
