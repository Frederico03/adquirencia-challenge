<?php

namespace App\Services\Adquirencia\SubadqB\Webhook;

use App\Services\Adquirencia\Contracts\PixWebhookDto;
use App\Services\Adquirencia\Contracts\WebhookNormalizerInterface;

class SubadqBPixWebhookNormalizer implements WebhookNormalizerInterface
{
    public function supports(array $payload): bool
    {
        if (!is_array($payload)) return false;
        // Heurísticas: possui 'type' com 'pix.status_update' e objeto 'data'
        return isset($payload['type'])
            && is_string($payload['type'])
            && str_contains(strtolower($payload['type']), 'pix.status_update')
            && isset($payload['data'])
            && is_array($payload['data']);
    }

    public function getSourceName(): string
    {
        return 'SubadqB';
    }

    public function normalize(array $payload): PixWebhookDto
    {
        $data = $payload['data'] ?? [];
        return new PixWebhookDto(
            externalTransactionId: (string)($data['id'] ?? ''),
            externalPixId: (string)($data['id'] ?? ''),
            status: (string)($data['status'] ?? ''),
            amount: (float)($data['value'] ?? 0),
            payerName: isset($data['payer']['name']) ? (string)$data['payer']['name'] : null,
            payerDocument: isset($data['payer']['document']) ? (string)$data['payer']['document'] : null,
            confirmedAt: $data['confirmed_at'] ?? null,
            source: 'SubadqB'
        );
    }
}
