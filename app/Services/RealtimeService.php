<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RealtimeService
{
    protected string $url;
    protected string $secret;

    public function __construct()
    {
        $this->url = config('services.realtime.url', 'http://localhost:8081');
        $this->secret = config('services.realtime.secret', '');
    }

    public function broadcast(array $channels, string $event, array $payload = []): void
    {
        try {
            Http::timeout(2)->post("{$this->url}/broadcast", [
                'channels' => $channels,
                'event' => $event,
                'payload' => $payload,
                'secret' => $this->secret,
            ]);
        } catch (\Exception $e) {
            Log::error("Realtime broadcast failed: " . $e->getMessage());
        }
    }
}
