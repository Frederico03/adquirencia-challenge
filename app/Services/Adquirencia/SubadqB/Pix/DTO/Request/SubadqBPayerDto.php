<?php
namespace App\Services\Adquirencia\SubadqB\Pix\DTO\Request;

class SubadqBPayerDto
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
