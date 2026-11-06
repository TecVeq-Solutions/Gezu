<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClipSubmission extends Model
{
    protected $fillable = [
        'campaign_id',
        'user_id',
        'social_account_id',
        'platform',
        'post_url',
        'video_file_path',
        'external_post_id',
        'status',
        'observed_views',
        'observed_likes',
        'observed_comments',
        'observed_shares',
        'eligible_views',
        'estimated_reward',
        'verified_reward',
        'rejection_reason',
        'fraud_risk_score',
        'verified_at',
    ];

    protected $casts = [
        'observed_views' => 'integer',
        'observed_likes' => 'integer',
        'observed_comments' => 'integer',
        'observed_shares' => 'integer',
        'eligible_views' => 'integer',
        'estimated_reward' => 'decimal:2',
        'verified_reward' => 'decimal:2',
        'fraud_risk_score' => 'integer',
        'verified_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function socialAccount(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class);
    }
}
