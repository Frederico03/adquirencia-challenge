<?php

namespace App\Services\Adquirencia\SubadqA\Pix\DTO\Request;

class SubadqAPayerDto
{
    public function __construct(
        public string $name,
        public string $cpf_cnpj
    ) {}

    public function toArray(): array
    {
        return [
            'name'     => $this->name,
            'cpf_cnpj' => $this->cpf_cnpj,
        ];
    }
}
