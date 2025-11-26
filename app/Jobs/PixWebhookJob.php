<?php

namespace App\Jobs;

use App\Models\PixTransaction;
use App\Services\Adquirencia\Contracts\PixWebhookDto;
use App\Services\Adquirencia\Status\PixStatusMapper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PixWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private PixWebhookDto $dto)
    {
    }

    public function handle(): void
    {
        $transaction = PixTransaction::query()
            ->where('external_id', $this->dto->externalTransactionId)
            ->first();

        if (!$transaction) {
            // Cria o registro se não existir
            PixTransaction::create([
                'user_id' => auth()->id() ?? 1,
                'subadquirente_id' => 1,
                'external_id' => $this->dto->externalTransactionId,
                'amount' => $this->dto->amount,
                'status' => PixStatusMapper::mapExternal($this->dto->status),
                'webhook_payload' => [
                    'external_transaction_id' => $this->dto->externalTransactionId,
                    'external_pix_id' => $this->dto->externalPixId,
                    'status' => $this->dto->status,
                    'amount' => $this->dto->amount,
                    'payer_name' => $this->dto->payerName,
                    'payer_document' => $this->dto->payerDocument,
                    'confirmed_at' => $this->dto->confirmedAt,
                    'source' => $this->dto->source,
                ],
            ]);
            return;
        }

        $transaction->status = PixStatusMapper::mapExternal($this->dto->status);
        if ($this->dto->amount > 0) {
            $transaction->amount = $this->dto->amount;
        }
        $transaction->save();
    }
}
