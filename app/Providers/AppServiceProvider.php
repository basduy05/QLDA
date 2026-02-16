<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
}
