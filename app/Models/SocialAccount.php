<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'followers_count' => 'integer',
        'connected_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'token_expires_at' => 'datetime',
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
