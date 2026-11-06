<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationDevice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'last_successful_delivery_at' => 'datetime',
        'last_failure_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
