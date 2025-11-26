<?php

namespace App\Services\Adquirencia\SubadqB\Webhook;

use App\Services\Adquirencia\Contracts\WebhookWithdrawNormalizerInterface;
use App\Services\Adquirencia\Contracts\WithdrawWebhookDto;

class SubadqBWithdrawWebhookNormalizer implements WebhookWithdrawNormalizerInterface
{
    public function supports(array $payload): bool
    {
        // Heurística: SubadqB envia 'type' contendo 'withdraw.status_update' e objeto 'data'
        return isset($payload['type'])
            && is_string($payload['type'])
            && str_contains(strtolower($payload['type']), 'withdraw.status_update')
            && isset($payload['data'])
            && is_array($payload['data']);
    }

    public function getSourceName(): string
    {
        return 'SubadqB';
    }

    public function normalize(array $payload): WithdrawWebhookDto
    {
        $data = $payload['data'] ?? [];
        return new WithdrawWebhookDto(
            externalWithdrawId: (string)($data['id'] ?? ''),
            status: (string)($data['status'] ?? ''),
            amount: (float)($data['amount'] ?? 0),
            processedAt: $data['processed_at'] ?? null,
            source: 'SubadqB'
        );
    }
}
