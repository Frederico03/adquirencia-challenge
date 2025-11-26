<?php

namespace App\Http\Controllers;

use App\Jobs\WithdrawWebhookJob;
use App\Services\Adquirencia\AdquirenciaResolve;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookWithdrawController extends Controller
{
    public function __construct(private readonly AdquirenciaResolve $resolver)
    {
    }

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        $normalizer = $this->resolver->resolveWithdrawWebhookNormalizer($payload);
        $dto = $normalizer->normalize($payload);
        WithdrawWebhookJob::dispatch($dto);
        return response()->json(['ok' => true, 'source' => $normalizer->getSourceName()]);
    }
}
