<?php

namespace App\Services\Adquirencia\Contracts;

use App\Services\Adquirencia\Contracts\WithdrawWebhookDto;

interface WebhookWithdrawNormalizerInterface
{
    public function supports(array $payload): bool;
    public function getSourceName(): string;
    public function normalize(array $payload): WithdrawWebhookDto;
}
