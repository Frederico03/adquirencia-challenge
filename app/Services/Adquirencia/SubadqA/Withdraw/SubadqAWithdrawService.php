<?php

namespace App\Services\Adquirencia\SubadqA\Withdraw;

use App\Models\Subadquirente;
use App\Services\Adquirencia\Contracts\Services\WithdrawServiceInterface;
use App\Services\Adquirencia\SubadqA\Withdraw\DTO\Request\SubadqAAccountDto;
use App\Services\Adquirencia\SubadqA\Withdraw\DTO\Request\SubadqAWithdrawCreateRequestDto;
use App\Services\Adquirencia\SubadqA\Withdraw\DTO\Response\SubadqAWithdrawCreateResponseDto;
use App\Services\Adquirencia\SubadqA\Withdraw\DTO\Response\SubadqAWithdrawErrorResponseDto;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class SubadqAWithdrawService implements WithdrawServiceInterface
{
    private PendingRequest $httpClient;

    public function __construct(
        private readonly Subadquirente $subadquirente,
    ) {
        $this->httpClient = Http::withoutVerifying()->baseUrl($this->subadquirente->base_url);
    }

    public function createWithdraw(array $payload)
    {
        $accountDto = new SubadqAAccountDto(
            bank_code: $payload['account']['bank_code'],
            agencia: $payload['account']['agencia'],
            conta: $payload['account']['conta'],
            type: $payload['account']['type'],
        );

        $dto = new SubadqAWithdrawCreateRequestDto(
            merchant_id: $payload['merchant_id'],
            account: $accountDto,
            amount: $payload['amount'],
            transaction_id: $payload['transaction_id'],
        );

        // $response = $this->httpClient
        //     ->withHeader('x-mock-response-name', 'SUCESSO_WD')
        //     ->post('/withdraw', $dto->toArray());

        // $response->onError(
        //     fn($response) => report($response->toException()),
        // );

        // abort_if($response->failed(), $response->status(), "Erro ao Sacar SubadqA: {$response->status()}");

        // $data = $response->json();

        $json = '{
            "withdraw_id": "wd_123456789",
            "status": "processing"
            }';

        $decoded = json_decode($json, true);

        $data = [
                'withdraw_id' => $decoded['withdraw_id'] ?? null,
                'status' => $decoded['status'] ?? null,
        ];

        $dtoResponse = new SubadqAWithdrawCreateResponseDto(
            withdraw_id: $data['withdraw_id'],
            status: $data['status'],
        );

        return $dtoResponse;
    }

    public function createWithdrawError(array $payload)
    {
        $accountDto = new SubadqAAccountDto(
            bank_code: $payload['account']['bank_code'],
            agencia: $payload['account']['agencia'],
            conta: $payload['account']['conta'],
            type: $payload['account']['type'],
        );

        $dto = new SubadqAWithdrawCreateRequestDto(
            merchant_id: $payload['merchant_id'],
            account: $accountDto,
            amount: $payload['amount'],
            transaction_id: $payload['transaction_id'],
        );

        // $response = $this->httpClient
        //     ->post('/withdraw', $dto->toArray());

        // $response->onError(
        //     fn($response) => report($response->toException()),
        // );

        // $data = $response->json();

        $json = '{
            "error": "insufficient_balance",
            "message": "Saldo insuficiente para realizar o saque."
            }';

        $decoded = json_decode($json, true);

        $data = [
                'error' => $decoded['error'] ?? null,
                'message' => $decoded['message'] ?? null,
        ];

        $dtoResponse = new SubadqAWithdrawErrorResponseDto(
            error: $data['error'],
            message: $data['message'],
        );

        return $dtoResponse;
    }
}

