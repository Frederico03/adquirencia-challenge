<?php

namespace App\Services\Adquirencia\Contracts\Services;

interface PixServiceInterface
{
    /**
     * Cria uma transação Pix para o usuário autenticado.
     */
    public function createPix(array $payload);

    /**
     * Cria uma transação Pix de erro para o usuário autenticado.
     */
    public function createPixError(array $payload);
}

