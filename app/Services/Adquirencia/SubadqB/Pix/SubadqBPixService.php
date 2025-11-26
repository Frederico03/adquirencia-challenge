<?php
namespace App\Services\Adquirencia\SubadqB\Pix;

use App\Models\Subadquirente;
use App\Services\Adquirencia\Contracts\Services\PixServiceInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Services\Adquirencia\SubadqB\Pix\DTO\Request\SubadqBPayerDto;
use App\Services\Adquirencia\SubadqB\Pix\DTO\Request\SubadqBPixCreateRequestDto;
use App\Services\Adquirencia\SubadqB\Pix\DTO\Response\SubadqBPixCreateResponseDto;
use App\Services\Adquirencia\SubadqB\Pix\DTO\Response\SubadqBPixErrorResponseDto;
use App\Jobs\PixWebhookJob;
use App\Services\Adquirencia\Contracts\PixWebhookDto;

class SubadqBPixService implements PixServiceInterface
{
    private PendingRequest $httpClient;

    public function __construct(
        private readonly Subadquirente $subadquirente,
    ) {
        $this->httpClient = Http::withoutVerifying()->baseUrl($this->subadquirente->base_url);
    }

    public function createPix(array $payload): SubadqBPixCreateResponseDto
    {
        $payer = new SubadqBPayerDto(
            name: $payload['payer']['name'],
            cpf_cnpj: $payload['payer']['cpf_cnpj'],
        );

        $dto = new SubadqBPixCreateRequestDto(
            seller_id: $payload['seller_id'],
            amount: $payload['amount'],
            order: $payload['order'],
            payer: $payer,
            expires_in: $payload['expires_in'],
        );

        // $response = $this->httpClient
        //     ->withHeader('x-mock-response-name', 'SUCESSO_PIX')
        //     ->post('/pix/create', $dto->toArray());

        // $response->onError(
        //     fn($response) => report($response->toException()),
        // );

        // abort_if($response->failed(), $response->status(), "Erro ao criar PIX SubadqB: {$response->status()}");

        // $data = $response->json();

        $json = '{
            "transaction_id": "SP_ADQB_4f4c9976-f0a7-4656-b823-dbc51aeed2fd",
            "location": "https://subadqB.com/pix/loc/366",
            "qrcode": "00020126530014BR.GOV.BCB.PIX0131backendtest@superpagamentos.com52040000530398654075000.005802BR5901N6001C6205050116304ACDA",
            "expires_at": "1764030932",
            "status": "PROCESSING"
            }';

        $decoded = json_decode($json, true);

        $data = [
                'transaction_id' => $decoded['transaction_id'] ?? null,
                'location' => $decoded['location'] ?? null,
                'qrcode' => $decoded['qrcode'] ?? null,
                'expires_at' => $decoded['expires_at'] ?? null,
                'status' => $decoded['status'] ?? null,
        ];

        $dtoResponse = new SubadqBPixCreateResponseDto(
            transaction_id: $data['transaction_id'],
            location: $data['location'],
            qrcode: $data['qrcode'],
            expires_at: $data['expires_at'],
            status: $data['status'],
        );

        // Simula webhook inicial com status PENDING
        $webhookDto = new PixWebhookDto(
            externalTransactionId: $data['transaction_id'],
            externalPixId: $data['transaction_id'],
            status: 'PENDING',
            amount: $payload['amount'],
            payerName: $payload['payer']['name'] ?? null,
            payerDocument: $payload['payer']['cpf_cnpj'] ?? null,
            confirmedAt: null,
            source: 'SubadqB'
        );
        PixWebhookJob::dispatch($webhookDto);

        return $dtoResponse;
    }

    public function createPixError(array $payload): SubadqBPixErrorResponseDto
    {
        $payer = new SubadqBPayerDto(
            name: $payload['payer']['name'],
            cpf_cnpj: $payload['payer']['cpf_cnpj'],
        );

        $dto = new SubadqBPixCreateRequestDto(
            seller_id: $payload['seller_id'],
            amount: $payload['amount'],
            order: $payload['order'],
            payer: $payer,
            expires_in: $payload['expires_in'],
        );

        // $response = $this->httpClient
        //     ->withHeader('x-mock-response-name', 'ERRO_PIX')
        //     ->post('/pix/create', $dto->toArray());

        // $response->onError(
        //     fn($response) => report($response->toException()),
        // );

        // $data = $response->json();

        $json = '
                {
                "error": "invalid request",
                "message": "amount must be greater than 0"
                }
            ';

        $decoded = json_decode($json, true);

        $data = [
                'error' => $decoded['error'] ?? null,
                'message' => $decoded['message'] ?? null,
        ];

        $dtoResponse = new SubadqBPixErrorResponseDto(
            error: $data['error'],
            message: $data['message'],
        );

        return $dtoResponse;
    }
}
