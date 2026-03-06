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

        if (! User::where('email', $request->email)->exists()) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __('We can\'t find a user with that e-mail address.')]);
        }

        $error = OtpController::sendOtp($request->email, 'password_reset', __('password reset'));
        if ($error) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => $error]);
        }

        $request->session()->put('password_reset_otp_email', $request->email);

        return redirect()->route('password.otp.show');
    }
}
