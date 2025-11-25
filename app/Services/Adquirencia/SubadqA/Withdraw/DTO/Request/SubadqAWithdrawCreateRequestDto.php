<?php

namespace App\Services\Adquirencia\SubadqA\Withdraw\DTO\Request;

class SubadqAWithdrawCreateRequestDto
{
    public function __construct(
        public string $merchant_id,
        public SubadqAAccountDto $account,
        public int $amount,
        public string $transaction_id,
    ) {
    }

    public function toArray(): array
    {
        return [
            'merchant_id'    => $this->merchant_id,
            'account'        => $this->account->toArray(),
            'amount'         => $this->amount,
            'transaction_id' => $this->transaction_id,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}

