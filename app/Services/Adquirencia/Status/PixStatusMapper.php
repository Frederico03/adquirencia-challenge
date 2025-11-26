<?php

namespace App\Services\Adquirencia\Status;

class PixStatusMapper
{
    /**
     * Mapeia status externos para valores persistidos na tabela (PENDING, CONFIRMED, FAILED).
     */
    public static function mapExternal(string $status): string
    {
        $normalized = strtoupper(trim($status));
        return match ($normalized) {
            'PENDING', 'PROCESSING' => 'PENDING',
            'CONFIRMED', 'PAID' => 'CONFIRMED',
            'CANCELLED', 'FAILED' => 'FAILED',
            default => 'PENDING',
        };
    }
}
