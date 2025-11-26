<?php
namespace App\Services\Adquirencia\SubadqB\Pix\DTO\Request;

use App\Services\Adquirencia\SubadqB\Pix\DTO\Request\SubadqBPayerDto;

class SubadqBPixCreateRequestDto
{
    public function __construct(
        public string $seller_id,
        public int $amount,
        public string $order,
        public SubadqBPayerDto $payer,
        public int $expires_in
    ) {}

    public function toArray(): array
    {
        return [
            'seller_id'  => $this->seller_id,
            'amount'     => $this->amount,
            'order'      => $this->order,
            'payer'      => $this->payer->toArray(),
            'expires_in' => $this->expires_in,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
