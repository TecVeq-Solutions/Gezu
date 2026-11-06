<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getNameAttribute()
    {
        return $this->company_name ?? $this->attributes['company_name'] ?? 'Sponsor Brand';
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
}
