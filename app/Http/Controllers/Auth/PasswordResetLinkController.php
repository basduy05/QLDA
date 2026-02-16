<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\OtpController;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __('We can\'t find a user with that email address.')]);
        }

        $request->session()->put('password_reset_otp_email', $user->email);
        $error = OtpController::sendOtp($user->email, 'password_reset', __('password reset'));

        if ($error) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => $error]);
        }

        return redirect()->route('password.otp.form')
            ->with('status', __('We sent an OTP code to your email.'));
    }
}
