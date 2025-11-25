<?php

namespace App\Services\Adquirencia\SubadqA\Withdraw\DTO\Response;

class SubadqAWithdrawCreateResponseDto
{
    public function __construct(
        public string $withdraw_id,
        public string $status,
    ) {
    }

    public function toArray(): array
    {
        return [
            'withdraw_id' => $this->withdraw_id,
            'status'      => $this->status,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}

