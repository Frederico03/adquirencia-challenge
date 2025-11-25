<?php

namespace App\Http\Middleware;

use App\Services\Adquirencia\AdquirenciaResolve;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveAdquirencia
{
    public function __construct(
        private readonly AdquirenciaResolve $resolve
    ) {
    }

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return new JsonResponse(['message' => 'Usuário não autenticado.'], 401);
        }

        $this->resolve->bindForUser($user, $request->query('subadquirente'));

        return $next($request);
    }
}

