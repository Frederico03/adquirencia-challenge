<?php

namespace App\Services\Adquirencia\SubadqB\Withdraw;

use App\Models\Subadquirente;
use App\Services\Adquirencia\Contracts\Services\WithdrawServiceInterface;
use App\Services\Adquirencia\SubadqB\Withdraw\DTO\Request\SubadqBAccountDto;
use App\Services\Adquirencia\SubadqB\Withdraw\DTO\Request\SubadqBWithdrawCreateRequestDto;
use App\Services\Adquirencia\SubadqB\Withdraw\DTO\Response\SubadqBWithdrawCreateResponseDto;
use App\Services\Adquirencia\SubadqB\Withdraw\DTO\Response\SubadqBWithdrawErrorResponseDto;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Jobs\WithdrawWebhookJob;
use App\Services\Adquirencia\Contracts\WithdrawWebhookDto;

class SubadqBWithdrawService implements WithdrawServiceInterface
{
    private PendingRequest $httpClient;

    public function __construct(
        private readonly Subadquirente $subadquirente,
    ) {
        $this->httpClient = Http::withoutVerifying()->baseUrl($this->subadquirente->base_url);
    }

    public function createWithdraw(array $payload): SubadqBWithdrawCreateResponseDto
    {
        $accountDto = new SubadqBAccountDto(
            bank_code: $payload['account']['bank_code'],
            agencia: $payload['account']['agencia'],
            conta: $payload['account']['conta'],
            type: $payload['account']['type'],
        );

        $dto = new SubadqBWithdrawCreateRequestDto(
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

        // abort_if($response->failed(), $response->status(), "Erro ao Sacar SubadqB: {$response->status()}");

        // $data = $response->json();

        $json = '{
            "withdraw_id": "WD_ADQB_c42002cd-202e-4f22-8c5b-37ac8c94b5f5",
            "status": "DONE"
            }';

        $decoded = json_decode($json, true);

        $data = [
                'withdraw_id' => $decoded['withdraw_id'] ?? null,
                'status' => $decoded['status'] ?? null,
        ];

        $dtoResponse = new SubadqBWithdrawCreateResponseDto(
            withdraw_id: $data['withdraw_id'],
            status: $data['status'],
        );

        // Simula webhook inicial com status PENDING
        $webhookDto = new WithdrawWebhookDto(
            externalWithdrawId: $data['withdraw_id'],
            status: 'PENDING',
            amount: $payload['amount'],
            processedAt: null,
            source: 'SubadqB'
        );
        WithdrawWebhookJob::dispatch($webhookDto);

        return $dtoResponse;
    }

    public function createWithdrawError(array $payload): SubadqBWithdrawErrorResponseDto
    {
        $accountDto = new SubadqBAccountDto(
            bank_code: $payload['account']['bank_code'],
            agencia: $payload['account']['agencia'],
            conta: $payload['account']['conta'],
            type: $payload['account']['type'],
        );

        $dto = new SubadqBWithdrawCreateRequestDto(
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
            "error_code": -2455,
            "type": "Permanent",
            "message": "Saldo insuficiente para realizar o saque."
            }';

        $decoded = json_decode($json, true);

        $data = [
                'error_code' => $decoded['error_code'] ?? null,
                'type' => $decoded['type'] ?? null,
                'message' => $decoded['message'] ?? null,
        ];

        $dtoResponse = new SubadqBWithdrawErrorResponseDto(
            error_code: $data['error_code'],
            type: $data['type'],
            message: $data['message'],
        );

        return $dtoResponse;
    }
}
