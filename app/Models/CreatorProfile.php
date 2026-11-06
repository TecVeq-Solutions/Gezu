<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorProfile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'total_earnings' => 'decimal:2',
        'rating' => 'decimal:2',
        'total_verified_views' => 'integer',
        'total_campaigns' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
