<?php

namespace App\Services\Adquirencia\Contracts\Services;

interface WithdrawServiceInterface
{
    /**
     * Cria um saque para o usuário autenticado.
     */
    public function createWithdraw(array $payload);

    /**
     * Erro um saque para o usuário autenticado.
     */
    public function createWithdrawError(array $payload);
}

