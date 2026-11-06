<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    protected $fillable = [
        'submission_id',
        'campaign_id',
        'brand_id',
        'clipper_id',
        'admin_reviewer_id',
        'gross_earning',
        'commission_rate',
        'commission_amount',
        'other_deductions',
        'net_payout',
        'currency',
        'status',
        'payment_method',
        'destination_account',
        'provider_reference',
        'idempotency_key',
        'failure_reason',
        'approved_by_brand_at',
        'reviewed_by_admin_at',
        'paid_at',
    ];

    protected $casts = [
        'gross_earning' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_payout' => 'decimal:2',
        'approved_by_brand_at' => 'datetime',
        'reviewed_by_admin_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(ClipSubmission::class, 'submission_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function clipper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'clipper_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'clipper_id');
    }

    public function adminReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_reviewer_id');
    }
}
