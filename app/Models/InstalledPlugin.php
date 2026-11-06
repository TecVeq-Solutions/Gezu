<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstalledPlugin extends Model
{
    use HasFactory;

    protected $fillable = [
        'plugin_id',
        'name',
        'version',
        'provider',
        'author',
        'type',
        'status',
        'source',
        'manifest',
        'panels',
        'installed_by',
        'installed_at',
        'disabled_at',
    ];

    protected $casts = [
        'manifest' => 'array',
        'panels' => 'array',
        'installed_at' => 'datetime',
        'disabled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'installed_by');
    }

    public function migrations(): HasMany
    {
        return $this->hasMany(PluginMigration::class, 'plugin_id', 'plugin_id');
    }
}
