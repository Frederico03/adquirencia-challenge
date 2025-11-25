<?php

namespace App\Services\Adquirencia\SubadqA\Withdraw\DTO\Response;

class SubadqAWithdrawErrorResponseDto
{
    public function __construct(
        public string $error,
        public string $message,
    ) {
    }

    public function toArray(): array
    {
        return [
            'error'   => $this->error,
            'message' => $this->message,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}

