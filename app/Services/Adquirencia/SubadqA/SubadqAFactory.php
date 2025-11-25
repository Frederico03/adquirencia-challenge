<?php

namespace App\Services\Adquirencia\SubadqA;

use App\Models\Subadquirente;
use App\Services\Adquirencia\Contracts\AdquirenciaFactoryInterface;
use App\Services\Adquirencia\Contracts\Services\PixServiceInterface;
use App\Services\Adquirencia\Contracts\Services\WithdrawServiceInterface;
use App\Services\Adquirencia\SubadqA\Pix\SubadqAPixService;
use App\Services\Adquirencia\SubadqA\Withdraw\SubadqAWithdrawService;
use Illuminate\Contracts\Container\Container;

class SubadqAFactory implements AdquirenciaFactoryInterface
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    public function bind(Subadquirente $subadquirente): void
    {
        $pixService = $this->container->make(SubadqAPixService::class, [
            'subadquirente' => $subadquirente,
        ]);

        $withdrawService = $this->container->make(SubadqAWithdrawService::class, [
            'subadquirente' => $subadquirente,
        ]);

        $this->container->instance(PixServiceInterface::class, $pixService);
        $this->container->instance(WithdrawServiceInterface::class, $withdrawService);
    }
}
