<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\OtpController;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'email_verified_at' => null,
            'terms_accepted_at' => now(),
        ]);

        event(new Registered($user));

        $request->session()->put('register_otp_email', $user->email);
        $error = OtpController::sendOtp($user->email, 'register', __('registration'));

        if ($error) {
            return back()->withInput($request->only('name', 'email'))
                ->withErrors(['email' => $error]);
        }

        return redirect()->route('register.otp.form')
            ->with('status', __('We sent an OTP code to your email.'));
    }
}
