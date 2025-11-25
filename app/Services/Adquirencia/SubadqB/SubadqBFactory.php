<?php

namespace App\Services\Adquirencia\SubadqB;

use Api\App\Services\Adquirencia\SubadqB\Pix\SubadqBPixService as PixSubadqBPixService;
use Api\App\Services\Adquirencia\SubadqB\Withdraw\SubadqBWithdrawService;
use App\Models\Subadquirente;
use App\Services\Adquirencia\Contracts\AdquirenciaFactoryInterface;
use App\Services\Adquirencia\Contracts\PixServiceInterface;
use App\Services\Adquirencia\Contracts\WithdrawServiceInterface;
use Illuminate\Contracts\Container\Container;

class SubadqBFactory implements AdquirenciaFactoryInterface
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    public function bind(Subadquirente $subadquirente): void
    {
        $pixService = $this->container->make(PixSubadqBPixService::class, [
            'subadquirente' => $subadquirente,
        ]);

        $withdrawService = $this->container->make(SubadqBWithdrawService::class, [
            'subadquirente' => $subadquirente,
        ]);

        $this->container->instance(PixServiceInterface::class, $pixService);
        $this->container->instance(WithdrawServiceInterface::class, $withdrawService);
    }
}

