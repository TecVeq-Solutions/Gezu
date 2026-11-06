<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'allowed_platforms' => 'array',
        'requirements_json' => 'array',
        'total_budget' => 'decimal:2',
        'remaining_budget' => 'decimal:2',
        'reward_rate_per_thousand' => 'decimal:2',
        'reward_per_video' => 'decimal:2',
        'target_views_per_video' => 'integer',
        'target_views' => 'integer',
        'verified_views' => 'integer',
        'start_at' => 'date',
        'end_at' => 'date',
    ];

    public function setAllowedPlatformsAttribute($value)
    {
        $this->attributes['allowed_platforms'] = is_array($value) ? json_encode(array_values($value)) : $value;
    }

    public function getAllowedPlatformsAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return ['tiktok', 'instagram', 'youtube'];
    }

    /**
     * Self-healing Schema Check for campaigns table
     */
    public static function ensureSchema(): void
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        if (!\Illuminate\Support\Facades\Schema::hasTable('campaigns')) return;

        $missing = [];
        if (!\Illuminate\Support\Facades\Schema::hasColumn('campaigns', 'rules')) {
            $missing['rules'] = true;
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('campaigns', 'payout_model')) {
            $missing['payout_model'] = true;
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('campaigns', 'reward_per_video')) {
            $missing['reward_per_video'] = true;
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('campaigns', 'target_views_per_video')) {
            $missing['target_views_per_video'] = true;
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('campaigns', 'asset_url')) {
            $missing['asset_url'] = true;
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('campaigns', 'allowed_platforms')) {
            $missing['allowed_platforms'] = true;
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('campaigns', 'expires_at')) {
            $missing['expires_at'] = true;
        }

        if (!empty($missing)) {
            try {
                \Illuminate\Support\Facades\Schema::table('campaigns', function (\Illuminate\Database\Schema\Blueprint $table) use ($missing) {
                    if (isset($missing['rules'])) {
                        $table->text('rules')->nullable()->after('description');
                    }
                    if (isset($missing['payout_model'])) {
                        $table->string('payout_model', 50)->default('cpm')->after('category');
                    }
                    if (isset($missing['reward_per_video'])) {
                        $table->decimal('reward_per_video', 10, 2)->nullable()->after('reward_rate_per_thousand');
                    }
                    if (isset($missing['target_views_per_video'])) {
                        $table->unsignedBigInteger('target_views_per_video')->nullable()->after('reward_per_video');
                    }
                    if (isset($missing['asset_url'])) {
                        $table->string('asset_url', 255)->nullable()->after('source_video_url');
                    }
                    if (isset($missing['allowed_platforms'])) {
                        $table->longText('allowed_platforms')->nullable()->after('requirements_json');
                    }
                    if (isset($missing['expires_at'])) {
                        $table->date('expires_at')->nullable()->after('end_at');
                    }
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Self-healing campaigns schema update failed: ' . $e->getMessage());
            }
        }
    }

    public function getRulesAttribute($value)
    {
        if ($value !== null && $value !== '') {
            return $value;
        }
        $req = $this->requirements_json;
        if (is_array($req) && !empty($req['country_targeting']['rules'])) {
            return $req['country_targeting']['rules'];
        }
        return $value;
    }

    public function getPayoutModelAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        $req = $this->requirements_json;
        if (is_array($req) && !empty($req['country_targeting']['payout_model'])) {
            return $req['country_targeting']['payout_model'];
        }
        return 'cpm';
    }

    public function getRewardPerVideoAttribute($value)
    {
        if ($value !== null && (float)$value > 0) {
            return $value;
        }
        $req = $this->requirements_json;
        if (is_array($req) && isset($req['country_targeting']['reward_per_video'])) {
            return $req['country_targeting']['reward_per_video'];
        }
        return $value;
    }

    public function getTargetViewsPerVideoAttribute($value)
    {
        if ($value !== null && (int)$value > 0) {
            return $value;
        }
        $req = $this->requirements_json;
        if (is_array($req) && isset($req['country_targeting']['target_views_per_video'])) {
            return $req['country_targeting']['target_views_per_video'];
        }
        return $value;
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function countries(): HasMany
    {
        return $this->hasMany(CampaignCountry::class);
    }

    public function platforms(): HasMany
    {
        return $this->hasMany(CampaignPlatform::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ClipSubmission::class);
    }

    public function participations(): HasMany
    {
        return $this->hasMany(CampaignParticipation::class);
    }

    public function saves(): HasMany
    {
        return $this->hasMany(SavedCampaign::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'campaign_participations')
            ->withPivot(['status', 'joined_at', 'completed_at'])
            ->withTimestamps();
    }

    /**
     * Determine Country Targeting Mode (worldwide | single | multiple)
     */
    public function getCountryTargetingModeAttribute(): string
    {
        $req = $this->requirements_json;
        if (is_array($req) && !empty($req['country_targeting']['mode'])) {
            return $req['country_targeting']['mode'];
        }

        $count = $this->relationLoaded('countries') ? $this->countries->count() : $this->countries()->count();
        if ($count === 0) {
            return 'worldwide';
        }
        return $count === 1 ? 'single' : 'multiple';
    }

    /**
     * Get target countries summary for badges and displays
     */
    public function getTargetCountriesSummaryAttribute(): string
    {
        $mode = $this->country_targeting_mode;
        if ($mode === 'worldwide') {
            return '🌍 Worldwide';
        }

        $list = $this->relationLoaded('countries') ? $this->countries : $this->countries()->get();
        if ($list->isEmpty()) {
            $req = $this->requirements_json;
            if (!empty($req['country_targeting']['countries'])) {
                $rawList = $req['country_targeting']['countries'];
                $names = array_column($rawList, 'name');
                return implode(', ', array_slice($names, 0, 2)) . (count($names) > 2 ? ' +' . (count($names) - 2) : '');
            }
            return '🌍 Worldwide';
        }

        if ($list->count() === 1) {
            $c = $list->first();
            return $c->flag_emoji . ' ' . $c->country_name . ' ($' . number_format($c->cpm_rate, 2) . '/1k)';
        }

        $firstTwo = $list->take(2)->map(function ($c) {
            $name = !empty($c->country_name) && strlen($c->country_name) > 2 ? $c->country_name : $c->country_code;
            return $name . ' ($' . number_format($c->cpm_rate ?? $c->reward_multiplier ?? 1, 2) . ')';
        })->implode(', ');

        if ($list->count() > 2) {
            $firstTwo .= ' +' . ($list->count() - 2) . ' more';
        }

        return $firstTwo;
    }

    /**
     * Get uniform collection of target country objects with codes, names, CPM rates and flags
     */
    public function getTargetCountriesListAttribute()
    {
        $list = $this->relationLoaded('countries') ? $this->countries : $this->countries()->get();
        if ($list->isNotEmpty()) {
            return $list;
        }

        $req = $this->requirements_json;
        if (!empty($req['country_targeting']['countries']) && is_array($req['country_targeting']['countries'])) {
            return collect($req['country_targeting']['countries'])->map(function ($c) {
                $code = strtoupper(trim($c['code'] ?? 'US'));
                $cpm = isset($c['cpm']) && (float)$c['cpm'] > 0 ? (float)$c['cpm'] : (float)$this->reward_rate_per_thousand;
                $budget = (float)($this->total_budget ?? 0);
                $views = isset($c['target_views']) && (int)$c['target_views'] > 0 
                    ? (int)$c['target_views'] 
                    : ($cpm > 0 && $budget > 0 ? (int)floor(($budget / $cpm) * 1000) : 0);

                return (object)[
                    'country_code' => $code,
                    'country_name' => $c['name'] ?? $code,
                    'cpm_rate' => $cpm,
                    'cpm' => $cpm,
                    'target_views' => $views,
                    'flag_url' => 'https://flagcdn.com/24x18/' . strtolower($code) . '.png',
                    'flag_emoji' => (strlen($code) === 2 && ctype_alpha($code))
                        ? (mb_chr(ord($code[0]) + 127397, 'UTF-8') . mb_chr(ord($code[1]) + 127397, 'UTF-8'))
                        : '🌐',
                ];
            });
        }

        return collect();
    }

    /**
     * Get Primary Creative Asset URL (explicit asset_url, source_video_url, or auto-extracted from description)
     */
    public function getPrimaryAssetUrlAttribute(): ?string
    {
        if (!empty($this->asset_url)) {
            return $this->asset_url;
        }
        if (!empty($this->source_video_url) && !str_contains($this->source_video_url, 'sample-video-source')) {
            return $this->source_video_url;
        }

        // Auto-extract Google Drive, Dropbox, Mega, OneDrive links if embedded in description
        if (!empty($this->description)) {
            if (preg_match('/(https?:\/\/(?:drive\.google\.com|dropbox\.com|mega\.nz|1drv\.ms|mediafire\.com|youtube\.com|youtu\.be)[^\s\)\"\']+)/i', $this->description, $matches)) {
                return $matches[1];
            }
        }

        return $this->source_video_url ?: null;
    }

    /**
     * Get Structured Rules Array for Campaign Guidelines
     */
    public function getCleanRulesAttribute(): array
    {
        if (!empty($this->rules)) {
            $lines = preg_split('/[\r\n]+/', trim($this->rules));
            $cleaned = array_filter(array_map('trim', $lines));
            if (!empty($cleaned)) {
                return array_values($cleaned);
            }
        }

        // Fallback: check if description has capitalized sections like "DELIVERY", "CREATIVE DIRECTION", "RULES"
        $rules = [];
        if (!empty($this->description)) {
            if (preg_match('/DELIVERY\s*[:\-]?\s*([^.]+?\.)/i', $this->description, $m)) {
                $rules[] = trim($m[1]);
            }
            if (preg_match('/APPROVED FOOTAGE\s*[:\-]?\s*([^.]+?\.)/i', $this->description, $m)) {
                $rules[] = trim($m[1]);
            }
            if (preg_match('/CREATIVE DIRECTION\s*[:\-]?\s*([^.]+?\.)/i', $this->description, $m)) {
                $rules[] = trim($m[1]);
            }
        }

        if (empty($rules)) {
            $rules = [
                'Create high-pacing 9:16 vertical video edits (15 to 60 seconds) with clear sound.',
                'Post to your public TikTok, YouTube Shorts, or Instagram Reels channel.',
                'Include campaign hashtags and title in caption or pinned comment.',
                'Authentic views only — bot traffic or artificial engagement will lead to disqualification.',
                'Earnings are paid out directly upon video verification and review.'
            ];
        }

        return $rules;
    }

    /**
     * Check if campaign uses fixed payment per approved video
     */
    public function isFixedPerVideo(): bool
    {
        return ($this->payout_model ?? 'cpm') === 'fixed_per_video';
    }

    /**
     * Formatted string for expected views per video (e.g. "80k views" or "80,000 views")
     */
    public function getFormattedExpectedViewsAttribute(): string
    {
        $views = (int) ($this->target_views_per_video ?? 0);
        if ($views <= 0) {
            return '';
        }
        if ($views >= 1000000) {
            return round($views / 1000000, 1) . 'M views';
        }
        if ($views >= 1000) {
            return round($views / 1000) . 'k views';
        }
        return number_format($views) . ' views';
    }

    /**
     * Human-readable payout summary string
     */
    public function getPayoutSummaryAttribute(): string
    {
        if ($this->isFixedPerVideo() && (float) $this->reward_per_video > 0) {
            $summary = '$' . number_format($this->reward_per_video, 2) . ' / video';
            if ($this->formatted_expected_views) {
                $summary .= ' (' . $this->formatted_expected_views . ' target)';
            }
            return $summary;
        }
        $rate = (float) ($this->reward_rate_per_thousand > 0 ? $this->reward_rate_per_thousand : 2.50);
        return '$' . number_format($rate, 2) . ' / 1k views';
    }

    /**
     * Check if campaign accepts submissions
     */
    public function isOpenForSubmissions(): bool
    {
        return in_array($this->status, ['active', 'half_filled']) && (float) ($this->remaining_budget ?? 0) > 0;
    }

    public function isHalfFilled(): bool
    {
        return $this->status === 'half_filled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getClaimedAmountAttribute(): float
    {
        $total = (float) ($this->total_budget ?? 0);
        $rem = (float) ($this->remaining_budget ?? 0);
        return max(0, $total - $rem);
    }

    public function getClaimedPercentageAttribute(): int
    {
        $total = (float) ($this->total_budget ?? 0);
        if ($total <= 0) return 0;
        $rem = (float) ($this->remaining_budget ?? 0);
        return min(100, max(0, (int) round((($total - $rem) / $total) * 100)));
    }
}

