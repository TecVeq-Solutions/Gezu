<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemBackup extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'version',
        'release_id',
        'storage_path',
        'size',
        'checksum',
        'status',
        'created_by',
        'verified_at',
    ];

    protected $casts = [
        'size' => 'integer',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
