<?php

use App\Mail\WelcomeUserMail;
use App\Models\EmailOtp;
use App\Models\PendingRegistration;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:cleanup-transient-data', function () {
    $expiredPending = PendingRegistration::whereNotNull('expires_at')
        ->where('expires_at', '<', now())
        ->delete();

    $stalePending = PendingRegistration::whereNull('expires_at')
        ->where('created_at', '<', now()->subHours(2))
        ->delete();

    $expiredUnusedOtps = EmailOtp::whereNull('used_at')
        ->where('expires_at', '<', now()->subMinutes(5))
        ->delete();

    $oldUsedOtps = EmailOtp::whereNotNull('used_at')
        ->where('used_at', '<', now()->subDay())
        ->delete();

    $this->info('Cleanup done.');
    $this->line('Deleted pending registrations: '.($expiredPending + $stalePending));
    $this->line('Deleted OTP rows: '.($expiredUnusedOtps + $oldUsedOtps));
})->purpose('Cleanup expired pending registrations and OTP data');

Schedule::command('app:cleanup-transient-data')->hourly();

Artisan::command('app:welcome-existing-users', function () {
    // Mark all unverified users as verified
    $verified = User::whereNull('email_verified_at')
        ->update(['email_verified_at' => now()]);
    $this->info("Marked {$verified} users as verified.");

    // Send welcome email to ALL existing users
    $users = User::all();
    $sent = 0;
    $failed = 0;

    foreach ($users as $user) {
        try {
            Mail::to($user->email)->send(
                (new WelcomeUserMail($user))->locale($user->locale ?? 'vi')
            );
            $sent++;
            $this->line("✓ Sent to {$user->email}");
        } catch (\Throwable $e) {
            $failed++;
            $this->error("✗ Failed for {$user->email}: {$e->getMessage()}");
            Log::warning('Welcome email failed (batch)', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);
        }

        // Brief pause to avoid rate limiting
        usleep(500_000);
    }

    $this->info("Done. Sent: {$sent}, Failed: {$failed}");
})->purpose('Send welcome email to all existing users and verify unverified ones');
