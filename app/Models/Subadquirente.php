<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subadquirente extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'base_url',
        'handler_class',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the users for the subadquirente.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_subadquirentes');
    }

    /**
     * Get the pix transactions for the subadquirente.
     */
    public function pixTransactions(): HasMany
    {
        return $this->hasMany(PixTransaction::class);
    }

    /**
     * Get the withdraws for the subadquirente.
     */
    public function withdraws(): HasMany
    {
        return $this->hasMany(Withdraw::class);
    }
}
