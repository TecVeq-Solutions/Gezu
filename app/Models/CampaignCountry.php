<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignCountry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'reward_multiplier' => 'decimal:2',
        'cpm' => 'decimal:2',
        'target_views' => 'integer',
    ];

    protected $appends = ['cpm_rate', 'flag_emoji', 'flag_url'];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get computed or direct CPM rate ($ per 1,000 views)
     */
    public function getCpmRateAttribute(): float
    {
        if (isset($this->attributes['cpm']) && $this->attributes['cpm'] !== null && (float)$this->attributes['cpm'] > 0) {
            return (float)$this->attributes['cpm'];
        }

        $baseRate = (float)($this->campaign?->reward_rate_per_thousand ?? 2.50);
        $mult = (float)($this->reward_multiplier ?? 1.00);
        return round($baseRate * ($mult > 0 ? $mult : 1.00), 2);
    }

    /**
     * Get national flag emoji from 2-letter country code
     */
    public function getFlagEmojiAttribute(): string
    {
        $code = strtoupper(trim($this->country_code ?? ''));
        if (strlen($code) !== 2 || !ctype_alpha($code)) {
            return '🌐';
        }
        return mb_chr(ord($code[0]) + 127397, 'UTF-8') . mb_chr(ord($code[1]) + 127397, 'UTF-8');
    }

    /**
     * Get reliable CDN flag image URL
     */
    public function getFlagUrlAttribute(): string
    {
        $code = strtolower(trim($this->country_code ?? ''));
        if (strlen($code) === 2 && ctype_alpha($code)) {
            return "https://flagcdn.com/24x18/{$code}.png";
        }
        return "https://flagcdn.com/24x18/un.png";
    }
}
