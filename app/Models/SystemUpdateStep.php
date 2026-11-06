<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemUpdateStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'release_id',
        'step',
        'status',
        'started_at',
        'completed_at',
        'message',
        'safe_error',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function release(): BelongsTo
    {
        return $this->belongsTo(SystemRelease::class, 'release_id', 'release_id');
    }
}
