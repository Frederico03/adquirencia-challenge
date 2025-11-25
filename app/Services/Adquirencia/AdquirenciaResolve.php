<?php

namespace App\Services\Adquirencia;

use App\Models\Subadquirente;
use App\Models\User;
use App\Services\Adquirencia\Contracts\AdquirenciaFactoryInterface;
use Illuminate\Contracts\Container\Container;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AdquirenciaResolve
{
    public function __construct(
        private readonly Container $container
    ) {
    }

    /**
     * Resolve a subadquirente para o usuário e faz o bind dos serviços de Pix e Withdraw.
     */
    public function bindForUser(User $user, ?string $subadquirenteName = null): void
    {
        $subadquirente = $this->resolveSubadquirente($user, $subadquirenteName);
        
        if (! $subadquirente->handler_class || ! class_exists($subadquirente->handler_class)) {
            throw new HttpException(500, 'Handler da subadquirente não configurado.');
        }

        $factory = $this->container->make($subadquirente->handler_class);

        if (! $factory instanceof AdquirenciaFactoryInterface) {
            throw new HttpException(500, 'Handler da subadquirente inválido.');
        }

        $factory->bind($subadquirente);
    }

    private function resolveSubadquirente(User $user, ?string $name): Subadquirente
    {
        $query = $user->subadquirentes()
            ->where('is_active', true);

        if ($name) {
            $query->where('name', $name);
        }

        $subadquirente = $query->first();

        if (! $subadquirente) {
            throw new HttpException(422, 'Subadquirente não encontrada para o usuário.');
        }

        return $subadquirente;
    }
}

