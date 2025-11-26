<?php

namespace App\Services\Adquirencia\Contracts;

class PixWebhookDto
{
    public function __construct(
        public readonly string $externalTransactionId,
        public readonly ?string $externalPixId,
        public readonly string $status,
        public readonly float $amount,
        public readonly ?string $payerName,
        public readonly ?string $payerDocument,
        public readonly ?string $confirmedAt,
        public readonly string $source
    ) {
    }
}
