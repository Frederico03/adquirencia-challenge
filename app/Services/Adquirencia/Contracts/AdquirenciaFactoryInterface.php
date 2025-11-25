<?php

namespace App\Services\Adquirencia\Contracts;

use App\Models\Subadquirente;

interface AdquirenciaFactoryInterface
{
    /**
     * Registra na aplicação os serviços específicos da subadquirente.
     */
    public function bind(Subadquirente $subadquirente): void;
}

