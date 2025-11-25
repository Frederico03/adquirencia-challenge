<?php

namespace App\Services\Adquirencia\SubadqB\Withdraw\DTO\Response;

class SubadqBWithdrawErrorResponseDto
{
    public function __construct(
        public int $error_code,
        public string $type,
        public string $message,
    ) {
    }

    public function toArray(): array
    {
        return [
            'error_code' => $this->error_code,
            'type'       => $this->type,
            'message'    => $this->message,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
