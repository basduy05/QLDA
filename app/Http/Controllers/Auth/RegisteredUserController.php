<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\OtpController;
use App\Models\PendingRegistration;
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

        $locale = app()->getLocale();

        $pending = PendingRegistration::updateOrCreate([
            'email' => $request->email,
        ], [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'locale' => $locale,
            'terms_accepted_at' => now(),
            'expires_at' => now()->addMinutes(30),
        ]);

        $request->session()->put('register_otp_email', $pending->email);
        $error = OtpController::sendOtp($pending->email, 'register_pending', __('registration'));

        if ($error) {
            return back()->withInput($request->only('name', 'email'))
                ->withErrors(['email' => $error]);
        }

        return redirect()->route('register.otp.form')
            ->with('status', __('We sent an OTP code to your email.'));
    }
}
