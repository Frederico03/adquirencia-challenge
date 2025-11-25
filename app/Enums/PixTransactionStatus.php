<?php

namespace App\Enums;

enum PixTransactionStatus: string
{
    case PENDING = 'PENDING';
    case PROCESSING = 'PROCESSING';
    case CONFIRMED = 'CONFIRMED';
    case PAID = 'PAID';
    case CANCELLED = 'CANCELLED';
    case FAILED = 'FAILED';

    /**
     * Get the description for the status
     */
    public function description(): string
    {
        return match($this) {
            self::PENDING => 'Pix criado, aguardando pagamento',
            self::PROCESSING => 'Pix criado, aguardando pagamento',
            self::CONFIRMED => 'Pagamento confirmado',
            self::PAID => 'Pagamento concluído com sucesso',
            self::CANCELLED => 'Pagamento cancelado pela subadquirente',
            self::FAILED => 'Erro no processamento do pagamento',
        };
    }

    /**
     * Get all status values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

