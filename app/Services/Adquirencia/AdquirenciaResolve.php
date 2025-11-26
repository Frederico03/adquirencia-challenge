<?php

namespace App\Services\Adquirencia;

use App\Models\Subadquirente;
use App\Models\User;
use App\Services\Adquirencia\Contracts\AdquirenciaFactoryInterface;
use Illuminate\Contracts\Container\Container;
use App\Services\Adquirencia\Contracts\WebhookNormalizerInterface;
use App\Services\Adquirencia\SubadqA\Webhook\SubadqAPixWebhookNormalizer;
use App\Services\Adquirencia\SubadqB\Webhook\SubadqBPixWebhookNormalizer;
use App\Services\Adquirencia\Contracts\WebhookWithdrawNormalizerInterface;
use App\Services\Adquirencia\SubadqA\Webhook\SubadqAWithdrawWebhookNormalizer;
use App\Services\Adquirencia\SubadqB\Webhook\SubadqBWithdrawWebhookNormalizer;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AdquirenciaResolve
{
    private array $pixNormalizers;
    private array $withdrawNormalizers;

    public function __construct(
        private readonly Container $container
    ) {
        $this->pixNormalizers = [
            'subadqA' => new SubadqAPixWebhookNormalizer(),
            'subadqB' => new SubadqBPixWebhookNormalizer(),
        ];

        $this->withdrawNormalizers = [
            'subadqA' => new SubadqAWithdrawWebhookNormalizer(),
            'subadqB' => new SubadqBWithdrawWebhookNormalizer(),
        ];
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

    /**
     * Resolve dinamicamente o normalizador de webhook com base no payload recebido (sem header).
     */
    public function resolvePixWebhookNormalizer(array $payload): WebhookNormalizerInterface
    {
        foreach ($this->pixNormalizers as $key => $normalizer) {
            if ($normalizer->supports($payload)) {
                return $normalizer;
            }
        }

        throw new HttpException(422, 'Formato de webhook desconhecido para as subadquirentes suportadas.');
    }

    /**
     * Resolve normalizer de withdraw com base no payload.
     */
    public function resolveWithdrawWebhookNormalizer(array $payload): WebhookWithdrawNormalizerInterface
    {
        foreach ($this->withdrawNormalizers as $key => $normalizer) {
            if ($normalizer->supports($payload)) {
                return $normalizer;
            }
        }

        throw new HttpException(422, 'Formato de webhook de withdraw desconhecido para as subadquirentes suportadas.');
    }

    /**
     * Busca normalizer de Pix pela chave (ex: 'subadqA', 'subadqB').
     */
    public function getPixNormalizerByKey(string $key): ?WebhookNormalizerInterface
    {
        return $this->pixNormalizers[$key] ?? null;
    }

    /**
     * Busca normalizer de Withdraw pela chave.
     */
    public function getWithdrawNormalizerByKey(string $key): ?WebhookWithdrawNormalizerInterface
    {
        return $this->withdrawNormalizers[$key] ?? null;
    }
}

