<?php

namespace App\Http\Controllers;

use App\Services\Adquirencia\Contracts\PixCreateDtoInterface;
use App\Services\Adquirencia\Contracts\Services\PixServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PixController extends Controller
{
        public function __construct(
        private PixServiceInterface $service,
    )
    {}
    public function create(
        Request $request,
    ): JsonResponse {
        $pix = null;

        if($request->header(config('services.mock_api.headers.name')) === config('services.mock_api.headers.value.sucesso_pix')) {
            $pix = $this->service->createPix(
                $request->all(),
            );
        }

        if($request->header(config('services.mock_api.headers.name')) === config('services.mock_api.headers.value.erro_pix')) {
            $pix = $this->service->createPixError(
                payload: $request->all(),
            );
        }

        return !!$pix ? response()->json($pix, 201) : response()->json(['error' => 'Pix creation failed'], 400);
    }
}
