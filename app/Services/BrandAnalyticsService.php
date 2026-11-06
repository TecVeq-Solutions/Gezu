<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Campaign;
use App\Models\ClipSubmission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BrandAnalyticsService
{
    /**
     * Get aggregate analytics summary for a brand.
     */
    public static function getSummary(Brand $brand): array
    {
        $campaigns = Campaign::where('brand_id', $brand->id)->get();
        $campaignIds = $campaigns->pluck('id');

        $totalBudget = (float) $campaigns->sum('total_budget');
        $remainingBudget = (float) $campaigns->sum('remaining_budget');
        $totalSpend = max(0, $totalBudget - $remainingBudget);

        $submissions = ClipSubmission::whereIn('campaign_id', $campaignIds)->get();
        $totalSubmissions = $submissions->count();
        $approvedSubmissions = $submissions->whereIn('status', ['approved', 'approved_pending_payout', 'verified'])->count();
        $rejectedSubmissions = $submissions->where('status', 'rejected')->count();

        $totalObservedViews = (int) $submissions->sum('observed_views');
        $totalEligibleViews = (int) $submissions->sum('eligible_views');
        if ($totalEligibleViews === 0 && $totalObservedViews > 0) {
            $totalEligibleViews = $totalObservedViews;
        }

        $approvalRate = $totalSubmissions > 0 
            ? round(($approvedSubmissions / $totalSubmissions) * 100, 1) 
            : 0.0;

        $effectiveCpm = $totalEligibleViews > 0 
            ? round(($totalSpend / $totalEligibleViews) * 1000, 2) 
            : 0.00;

        $creatorsCount = $submissions->pluck('user_id')->unique()->count();

        return [
            'total_spend' => $totalSpend,
            'total_budget' => $totalBudget,
            'remaining_budget' => $remainingBudget,
            'total_views' => $totalEligibleViews,
            'total_submissions' => $totalSubmissions,
            'approved_submissions' => $approvedSubmissions,
            'rejected_submissions' => $rejectedSubmissions,
            'approval_rate' => $approvalRate,
            'effective_cpm' => $effectiveCpm,
            'creators_count' => $creatorsCount,
        ];
    }

    /**
     * Get platform distribution breakdown.
     */
    public static function getPlatformBreakdown(Brand $brand): Collection
    {
        $campaignIds = Campaign::where('brand_id', $brand->id)->pluck('id');

        return ClipSubmission::whereIn('campaign_id', $campaignIds)
            ->select('platform', DB::raw('count(*) as count'), DB::raw('sum(observed_views) as total_views'))
            ->groupBy('platform')
            ->orderByDesc('total_views')
            ->get();
    }

    /**
     * Get top performing campaigns.
     */
    public static function getTopCampaigns(Brand $brand, int $limit = 5): Collection
    {
        return Campaign::where('brand_id', $brand->id)
            ->withCount(['submissions as approved_clips_count' => fn ($q) => $q->whereIn('status', ['approved', 'approved_pending_payout', 'verified'])])
            ->withCount('submissions as total_clips_count')
            ->orderByDesc('verified_views')
            ->take($limit)
            ->get();
    }

    /**
     * Get top performing creators for this brand.
     */
    public static function getTopCreators(Brand $brand, int $limit = 5): Collection
    {
        $campaignIds = Campaign::where('brand_id', $brand->id)->pluck('id');

        return ClipSubmission::whereIn('campaign_id', $campaignIds)
            ->select('user_id', DB::raw('count(*) as total_clips'), DB::raw('sum(observed_views) as total_views'), DB::raw('sum(verified_reward) as total_rewards'))
            ->groupBy('user_id')
            ->with(['user.creatorProfile'])
            ->orderByDesc('total_views')
            ->take($limit)
            ->get();
    }
}
