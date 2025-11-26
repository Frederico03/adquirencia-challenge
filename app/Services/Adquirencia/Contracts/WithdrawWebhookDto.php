<?php

namespace App\Services\Adquirencia\Contracts;

class WithdrawWebhookDto
{
    public function __construct(
        public readonly string $externalWithdrawId,
        public readonly string $status,
        public readonly float $amount,
        public readonly ?string $processedAt,
        public readonly string $source
    ) {
    }
}
