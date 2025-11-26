<?php

namespace App\Services\Adquirencia\Status;

class WithdrawStatusMapper
{
    /**
     * Converte status externos para valores permitidos no banco: PENDING, COMPLETED, FAILED
     */
    public static function mapExternal(string $status): string
    {
        $normalized = strtoupper(trim($status));
        return match ($normalized) {
            'PENDING', 'PROCESSING', 'REQUESTED' => 'PENDING',
            'SUCCESS', 'DONE', 'COMPLETED' => 'COMPLETED',
            'CANCELLED', 'FAILED', 'ERROR' => 'FAILED',
            default => 'PENDING',
        };
    }
}
