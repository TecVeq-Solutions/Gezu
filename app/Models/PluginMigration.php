<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginMigration extends Model
{
    use HasFactory;

    protected $fillable = [
        'plugin_id',
        'migration',
        'version',
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];

    public function plugin(): BelongsTo
    {
        return $this->belongsTo(InstalledPlugin::class, 'plugin_id', 'plugin_id');
    }
}
