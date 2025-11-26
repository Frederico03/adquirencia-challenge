<?php

namespace App\Services\Adquirencia\SubadqA\Webhook;

use App\Services\Adquirencia\Contracts\PixWebhookDto;
use App\Services\Adquirencia\Contracts\WebhookNormalizerInterface;

class SubadqAPixWebhookNormalizer implements WebhookNormalizerInterface
{
    public function supports(array $payload): bool
    {
        if (!is_array($payload)) return false;
        // Heurísticas: possui 'event' e 'transaction_id' e/ou 'pix_id' e metadados opcionais
        return (isset($payload['event']) && is_string($payload['event']))
            && isset($payload['transaction_id'])
            && (isset($payload['pix_id']) || isset($payload['metadata']));
    }

    public function getSourceName(): string
    {
        return 'SubadqA';
    }

    public function normalize(array $payload): PixWebhookDto
    {
        return new PixWebhookDto(
            externalTransactionId: (string)($payload['transaction_id'] ?? ''),
            externalPixId: isset($payload['pix_id']) ? (string)$payload['pix_id'] : null,
            status: (string)($payload['status'] ?? ''),
            amount: (float)($payload['amount'] ?? 0),
            payerName: $payload['payer_name'] ?? null,
            payerDocument: $payload['payer_cpf'] ?? null,
            confirmedAt: $payload['payment_date'] ?? null,
            source: (string)($payload['metadata']['source'] ?? 'SubadqA')
        );
    }
}
