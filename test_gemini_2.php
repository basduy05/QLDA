<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = App\Models\AppSetting::getValue('ai.gemini_api_key');
$model = 'gemini-2.5-flash';

$response = Illuminate\Support\Facades\Http::withoutVerifying()
    ->withHeaders(['Content-Type' => 'application/json'])
    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
        'contents' => [
            [
                'parts' => [
                    ['text' => 'Hello']
                ]
            ]
        ]
    ]);

echo $response->body();
