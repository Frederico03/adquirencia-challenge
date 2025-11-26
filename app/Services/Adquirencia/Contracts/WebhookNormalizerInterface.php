<?php

namespace App\Services\Adquirencia\Contracts;

use App\Services\Adquirencia\Contracts\PixWebhookDto;

interface WebhookNormalizerInterface
{
    /** Indica se este normalizer consegue interpretar o payload. */
    public function supports(array $payload): bool;

    /** Nome/código da origem (ex.: SubadqA, SubadqB). */
    public function getSourceName(): string;

    /**
     * Recebe payload bruto e retorna DTO comum para processamento.
     */
    public function normalize(array $payload): PixWebhookDto;
}
