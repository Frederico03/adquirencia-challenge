<?php

namespace App\Services\Adquirencia\SubadqA\Webhook;

use App\Services\Adquirencia\Contracts\WebhookWithdrawNormalizerInterface;
use App\Services\Adquirencia\Contracts\WithdrawWebhookDto;

class SubadqAWithdrawWebhookNormalizer implements WebhookWithdrawNormalizerInterface
{
    public function supports(array $payload): bool
    {
        // Heurística: SubadqA envia 'event' e 'withdraw_id'
        return isset($payload['event']) && isset($payload['withdraw_id']);
    }

    public function getSourceName(): string
    {
        return 'SubadqA';
    }

    public function normalize(array $payload): WithdrawWebhookDto
    {
        return new WithdrawWebhookDto(
            externalWithdrawId: (string)($payload['withdraw_id'] ?? ''),
            status: (string)($payload['status'] ?? ''),
            amount: (float)($payload['amount'] ?? 0),
            processedAt: $payload['processed_at'] ?? null,
            source: 'SubadqA'
        );
    }
}
