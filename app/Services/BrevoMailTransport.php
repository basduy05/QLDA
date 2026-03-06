<?php

namespace App\Services;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\RawMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoMailTransport extends AbstractTransport
{
    public function __construct(
        private string $apiKey,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $from = $email->getFrom()[0] ?? null;
        if (! $from) {
            throw new \RuntimeException('No sender address set.');
        }

        $to = array_map(fn(Address $a) => [
            'email' => $a->getAddress(),
            'name' => $a->getName() ?: $a->getAddress(),
        ], $email->getTo());

        $payload = [
            'sender' => [
                'email' => $from->getAddress(),
                'name' => $from->getName() ?: $from->getAddress(),
            ],
            'to' => $to,
            'subject' => $email->getSubject(),
        ];

        $html = $email->getHtmlBody();
        $text = $email->getTextBody();

        if ($html) {
            $payload['htmlContent'] = $html;
        }
        if ($text) {
            $payload['textContent'] = $text;
        }

        $response = Http::withHeaders([
            'api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(15)->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            $body = $response->body();
            Log::error('Brevo API email failed', [
                'status' => $response->status(),
                'body' => $body,
            ]);
            throw new \RuntimeException('Brevo API error: ' . $body);
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
