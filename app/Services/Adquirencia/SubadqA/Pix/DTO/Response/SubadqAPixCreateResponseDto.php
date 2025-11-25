<?php

namespace App\Services\Adquirencia\SubadqA\Pix\DTO\Response;

class SubadqAPixCreateResponseDto
{
    public function __construct(
        public string $transaction_id,
        public string $location,
        public string $qrcode,
        public string $expires_at,
        public string $status
    ) {}

    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transaction_id,
            'location'       => $this->location,
            'qrcode'         => $this->qrcode,
            'expires_at'     => $this->expires_at,
            'status'         => $this->status,
        ];
    }


    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
