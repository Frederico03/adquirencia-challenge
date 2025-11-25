<?php

namespace App\Models;

use App\Enums\PixTransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PixTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'subadquirente_id',
        'external_id',
        'amount',
        'status',
        'webhook_payload',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PixTransactionStatus::class,
            'webhook_payload' => 'array',
        ];
    }

    /**
     * Get the user that owns the pix transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subadquirente that owns the pix transaction.
     */
    public function subadquirente(): BelongsTo
    {
        return $this->belongsTo(Subadquirente::class);
    }
}

