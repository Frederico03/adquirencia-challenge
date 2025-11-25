<?php

namespace App\Services\Adquirencia\SubadqB\Withdraw\DTO\Request;

class SubadqBAccountDto
{
    public function __construct(
        public string $bank_code,
        public string $agencia,
        public string $conta,
        public string $type
    ) {}

    public function toArray(): array
    {
        return [
            'bank_code' => $this->bank_code,
            'agencia'   => $this->agencia,
            'conta'     => $this->conta,
            'type'      => $this->type,
        ];
    }
}
