<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\OtpController;
use App\Models\PendingRegistration;
use App\Models\User;
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

        PendingRegistration::where('email', $request->email)->delete();

        PendingRegistration::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'locale' => app()->getLocale(),
            'terms_accepted_at' => now(),
            'expires_at' => now()->addMinutes(30),
        ]);

        $error = OtpController::sendOtp($request->email, 'register_pending', __('registration'));
        if ($error) {
            return back()->withInput()->withErrors(['email' => $error]);
        }

        $request->session()->put('register_otp_email', $request->email);

        return redirect()->route('register.otp.show');
    }
}
