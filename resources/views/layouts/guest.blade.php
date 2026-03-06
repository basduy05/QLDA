<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Aperlex') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/images/logo-full.png">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|fraunces:400,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="page-shell min-h-screen flex flex-col justify-center items-center px-6 py-10">
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center gap-2">
                    <img src="/images/logo-full.png" alt="Aperlex" class="h-10">
                </a>
                <p class="text-sm text-slate-500 mt-2">{{ __('Plan, deliver, and celebrate project wins.') }}</p>
            </div>

            <div class="w-full max-w-md card-strong px-7 py-6">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
