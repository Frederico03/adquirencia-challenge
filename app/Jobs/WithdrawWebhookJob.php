<?php

namespace App\Jobs;

use App\Models\Withdraw;
use App\Services\Adquirencia\Contracts\WithdrawWebhookDto;
use App\Services\Adquirencia\Status\WithdrawStatusMapper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WithdrawWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private WithdrawWebhookDto $dto)
    {
    }

    public function handle(): void
    {
        $withdraw = Withdraw::query()
            ->where('external_id', $this->dto->externalWithdrawId)
            ->first();

        if (! $withdraw) {
            // Cria o registro se não existir
            Withdraw::create([
                'user_id' => auth()->id() ?? 1,
                'subadquirente_id' => 1,
                'external_id' => $this->dto->externalWithdrawId,
                'amount' => $this->dto->amount,
                'status' => WithdrawStatusMapper::mapExternal($this->dto->status),
                'webhook_payload' => [
                    'external_withdraw_id' => $this->dto->externalWithdrawId,
                    'status' => $this->dto->status,
                    'amount' => $this->dto->amount,
                    'processed_at' => $this->dto->processedAt,
                    'source' => $this->dto->source,
                ],
            ]);
            return;
        }

        // Atualiza se já existir
        $withdraw->status = WithdrawStatusMapper::mapExternal($this->dto->status);
        if ($this->dto->amount > 0) {
            $withdraw->amount = $this->dto->amount;
        }
        $withdraw->save();
    }
}
