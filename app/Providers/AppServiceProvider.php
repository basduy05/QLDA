<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Services\BrevoMailTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Brevo mail transport
        Mail::extend('brevo', function (array $config = []) {
            $apiKey = $config['api_key']
                ?? AppSetting::getValue('mail.brevo_api_key')
                ?? '';

            return new BrevoMailTransport($apiKey);
        });

        // Apply dynamic mail settings from database
        $this->applyDynamicMailConfig();

        if (app()->runningInConsole()) {
            return;
        }

        $appUrl = (string) config('app.url');
        $forwardedProto = (string) request()->header('x-forwarded-proto');

        if ($appUrl === '' || str_contains($appUrl, 'your-domain') || str_contains($appUrl, 'localhost')) {
            $rootUrl = request()->getSchemeAndHttpHost();
            URL::forceRootUrl($rootUrl);
        }

        if ($forwardedProto === 'https' || request()->isSecure()) {
            URL::forceScheme('https');
        }
    }

    private function applyDynamicMailConfig(): void
    {
        try {
            $provider = AppSetting::getValue('mail.provider');
            if (! $provider) {
                return;
            }

            if ($provider === 'brevo') {
                config(['mail.default' => 'brevo']);
            } elseif ($provider === 'smtp') {
                $host = AppSetting::getValue('mail.smtp_host');
                $port = AppSetting::getValue('mail.smtp_port');
                $username = AppSetting::getValue('mail.smtp_username');
                $password = AppSetting::getValue('mail.smtp_password');

                if ($host && $port) {
                    config([
                        'mail.default' => 'smtp',
                        'mail.mailers.smtp.host' => $host,
                        'mail.mailers.smtp.port' => (int) $port,
                        'mail.mailers.smtp.username' => $username,
                        'mail.mailers.smtp.password' => $password,
                        'mail.mailers.smtp.scheme' => (int) $port === 465 ? 'smtps' : null,
                    ]);
                }
            }

            $fromAddress = AppSetting::getValue('mail.from_address');
            if ($fromAddress) {
                config([
                    'mail.from.address' => $fromAddress,
                ]);
            }
        } catch (\Throwable) {
            // DB not ready yet (e.g. during migrations)
        }
    }
}
