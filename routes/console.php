<?php

use App\Models\EmailOtp;
use App\Models\PendingRegistration;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
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
