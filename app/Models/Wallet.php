<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'currency',
        'available_balance',
        'pending_balance',
        'locked_balance',
        'total_withdrawn',
        'stripe_account_id',
        'stripe_onboarding_completed',
        'payouts_enabled',
        'payout_bank_name',
        'payout_account_holder',
        'payout_account_number',
        'payout_phone_number',
        'payout_routing_swift',
        'stripe_connected_at',
    ];

    protected $casts = [
        'available_balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'locked_balance' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'stripe_onboarding_completed' => 'boolean',
        'payouts_enabled' => 'boolean',
        'stripe_connected_at' => 'datetime',
    ];

    public function getBalanceAttribute()
    {
        return $this->available_balance ?? $this->attributes['available_balance'] ?? 0.00;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
