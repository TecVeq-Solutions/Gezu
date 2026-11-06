<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'release_id',
        'type',
        'description',
        'manifest',
        'archive_hash',
        'signature_verified',
        'uploaded_by',
        'uploaded_at',
        'started_at',
        'completed_at',
        'status',
        'previous_version',
        'backup_id',
        'failure_stage',
        'failure_message',
        'rollback_status',
    ];

    protected $casts = [
        'manifest' => 'array',
        'signature_verified' => 'boolean',
        'uploaded_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(SystemUpdateStep::class, 'release_id', 'release_id')->orderBy('id', 'asc');
    }

    public function backup(): BelongsTo
    {
        return $this->belongsTo(SystemBackup::class, 'backup_id');
    }
}
