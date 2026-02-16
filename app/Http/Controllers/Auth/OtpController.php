<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpCodeMail;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class OtpController extends Controller
{
    private const OTP_TTL_MINUTES = 10;
    private const OTP_COOLDOWN_SECONDS = 60;
    private const OTP_MAX_PER_HOUR = 5;

    public function showRegisterForm(Request $request): View|RedirectResponse
    {
        $email = (string) $request->session()->get('register_otp_email', '');
        if ($email === '') {
            return redirect()->route('register');
        }

        return view('auth.verify-otp', [
            'title' => __('Verify your email'),
            'description' => __('Enter the OTP sent to your email to finish registration.'),
            'action' => route('register.otp.verify'),
            'resendAction' => route('register.otp.resend'),
            'email' => $email,
        ]);
    }

    public function verifyRegister(Request $request): RedirectResponse
    {
        $email = (string) $request->session()->get('register_otp_email', '');
        if ($email === '') {
            return redirect()->route('register');
        }

        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $otp = $this->latestOtp($email, 'register');
        if (! $otp || $this->isExpiredOrBlocked($otp)) {
            return back()->withErrors(['code' => __('OTP expired. Please resend a new code.')]);
        }

        if (! Hash::check($request->code, $otp->code_hash)) {
            $otp->increment('attempts');
            return back()->withErrors(['code' => __('Invalid OTP code.')]);
        }

        $otp->forceFill(['used_at' => now()])->save();

        $user = User::where('email', $email)->first();
        if (! $user) {
            return redirect()->route('register');
        }

        $user->forceFill(['email_verified_at' => now()])->save();

        $request->session()->forget('register_otp_email');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('status', __('Email verified successfully.'));
    }

    public function resendRegister(Request $request): RedirectResponse
    {
        $email = (string) $request->session()->get('register_otp_email', '');
        if ($email === '') {
            return redirect()->route('register');
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            return redirect()->route('register');
        }

        $error = $this->sendOtp($email, 'register', __('registration'));
        if ($error) {
            return back()->withErrors(['code' => $error]);
        }

        return back()->with('status', __('A new OTP code has been sent.'));
    }

    public function showPasswordForm(Request $request): View|RedirectResponse
    {
        $email = (string) $request->session()->get('password_reset_otp_email', '');
        if ($email === '') {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', [
            'title' => __('Verify OTP for password reset'),
            'description' => __('Enter the OTP sent to your email to continue resetting your password.'),
            'action' => route('password.otp.verify'),
            'resendAction' => route('password.otp.resend'),
            'email' => $email,
        ]);
    }

    public function verifyPassword(Request $request): RedirectResponse
    {
        $email = (string) $request->session()->get('password_reset_otp_email', '');
        if ($email === '') {
            return redirect()->route('password.request');
        }

        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $otp = $this->latestOtp($email, 'password_reset');
        if (! $otp || $this->isExpiredOrBlocked($otp)) {
            return back()->withErrors(['code' => __('OTP expired. Please resend a new code.')]);
        }

        if (! Hash::check($request->code, $otp->code_hash)) {
            $otp->increment('attempts');
            return back()->withErrors(['code' => __('Invalid OTP code.')]);
        }

        $otp->forceFill(['used_at' => now()])->save();

        $user = User::where('email', $email)->first();
        if (! $user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => __('User not found.')]);
        }

        $request->session()->forget('password_reset_otp_email');
        $request->session()->put('password_reset_verified_email', $email);

        return redirect()->route('password.otp.reset.form');
    }

    public function resendPassword(Request $request): RedirectResponse
    {
        $email = (string) $request->session()->get('password_reset_otp_email', '');
        if ($email === '') {
            return redirect()->route('password.request');
        }

        if (! User::where('email', $email)->exists()) {
            return redirect()->route('password.request');
        }

        $error = $this->sendOtp($email, 'password_reset', __('password reset'));
        if ($error) {
            return back()->withErrors(['code' => $error]);
        }

        return back()->with('status', __('A new OTP code has been sent.'));
    }

    public function showOtpResetForm(Request $request): View|RedirectResponse
    {
        $email = (string) $request->session()->get('password_reset_verified_email', '');
        if ($email === '') {
            return redirect()->route('password.request');
        }

        return view('auth.otp-reset-password', [
            'email' => $email,
        ]);
    }

    public function storeOtpResetPassword(Request $request): RedirectResponse
    {
        $email = (string) $request->session()->get('password_reset_verified_email', '');
        if ($email === '') {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $email)->first();
        if (! $user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => __('User not found.')]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        $request->session()->forget('password_reset_verified_email');

        return redirect()->route('login')
            ->with('status', __('Password reset successfully.'));
    }

    public static function sendOtp(string $email, string $purpose, string $purposeLabel): ?string
    {
        $latestOtp = EmailOtp::where('email', $email)
            ->where('purpose', $purpose)
            ->latest('id')
            ->first();

        if ($latestOtp && $latestOtp->created_at) {
            $elapsedSeconds = $latestOtp->created_at->diffInSeconds(now());
            if ($elapsedSeconds < self::OTP_COOLDOWN_SECONDS) {
                $wait = self::OTP_COOLDOWN_SECONDS - $elapsedSeconds;
                return __('Please wait :seconds second(s) before requesting another OTP.', ['seconds' => $wait]);
            }
        }

        $countInHour = EmailOtp::where('email', $email)
            ->where('purpose', $purpose)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($countInHour >= self::OTP_MAX_PER_HOUR) {
            return __('Too many OTP requests. Please try again in about one hour.');
        }

        $code = (string) random_int(100000, 999999);

        EmailOtp::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $otp = EmailOtp::create([
            'email' => $email,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'max_attempts' => 5,
            'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
            'meta' => ['purpose_label' => $purposeLabel],
        ]);

        try {
            Mail::to($email)->send(new OtpCodeMail($code, $purposeLabel));
        } catch (\Throwable $exception) {
            $otp->delete();

            Log::error('OTP email send failed', [
                'email' => $email,
                'purpose' => $purpose,
                'message' => $exception->getMessage(),
            ]);

            return __('Unable to send OTP email right now. Please verify your mail configuration and try again.');
        }

        return null;
    }

    private function latestOtp(string $email, string $purpose): ?EmailOtp
    {
        return EmailOtp::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->latest('id')
            ->first();
    }

    private function isExpiredOrBlocked(EmailOtp $otp): bool
    {
        if ($otp->attempts >= $otp->max_attempts) {
            return true;
        }

        return $otp->expires_at->isPast();
    }
}
