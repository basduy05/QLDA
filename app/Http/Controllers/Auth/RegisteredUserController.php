<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'accept_terms' => ['accepted'],
        ]);

        $role = User::where('role', 'admin')->exists() ? 'user' : 'admin';
        $locale = app()->getLocale();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'locale' => $locale,
            'email_verified_at' => now(),
            'terms_accepted_at' => now(),
        ]);

        event(new Registered($user));

        $welcomeUserId = (int) $user->id;
        $welcomeEmail = (string) $user->email;
        $welcomeLocale = (string) ($user->locale ?? app()->getLocale());

        app()->terminating(function () use ($welcomeUserId, $welcomeEmail, $welcomeLocale) {
            try {
                $welcomeUser = User::find($welcomeUserId);
                if (! $welcomeUser) {
                    return;
                }

                Mail::to($welcomeEmail)->send(
                    (new WelcomeUserMail($welcomeUser))->locale($welcomeLocale)
                );
            } catch (\Throwable $exception) {
                Log::warning('Welcome email send failed', [
                    'user_id' => $welcomeUserId,
                    'email' => $welcomeEmail,
                    'message' => $exception->getMessage(),
                ]);
            }
        });

        Auth::login($user);

        $request->session()->regenerate();

        return redirect(route('dashboard', absolute: false));
    }
}
