<?php

namespace App\Http\Controllers;

use App\Services\Adquirencia\Contracts\Services\WithdrawServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function __construct(
        private WithdrawServiceInterface $service,
    )
    {}

    public function create(
        Request $request,
    ): JsonResponse {
        $withdraw = null;

        if($request->header(config('services.mock_api.headers.name')) === config('services.mock_api.headers.value.sucesso_wd')) {
            $withdraw = $this->service->createWithdraw(
                $request->all(),
            );
        }

        if($request->header(config('services.mock_api.headers.name')) === config('services.mock_api.headers.value.erro_wd')) {
            $withdraw = $this->service->createWithdrawError(
                $request->all(),
            );
        }

        return !!$withdraw ? response()->json($withdraw, 201) : response()->json(['error' => 'Withdraw not created'], 400);
    }
}
