<?php

namespace App\Services\Adquirencia\SubadqA\Pix\DTO\Request;

class SubadqAPixCreateRequestDto
{
    public function __construct(
        public string $merchant_id,
        public int $amount,
        public string $currency,
        public string $order_id,
        public SubadqAPayerDto $payer,
        public int $expires_in
    ) {}

    public function toArray(): array
    {
        return [
            'merchant_id' => $this->merchant_id,
            'amount'      => $this->amount,
            'currency'    => $this->currency,
            'order_id'    => $this->order_id,
            'payer'       => $this->payer->toArray(),
            'expires_in'  => $this->expires_in,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
