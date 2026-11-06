<?php

namespace App\Services;

use App\Models\ClipSubmission;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClipperAnalyticsService
{
    /**
     * Compute comprehensive performance telemetry for a clipper.
     */
    public static function getSummary(User $user, string $timeRange = 'all'): array
    {
        $query = ClipSubmission::where('user_id', $user->id);

        // Apply time range
        if ($timeRange === '7d') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($timeRange === '30d') {
            $query->where('created_at', '>=', now()->subDays(30));
        } elseif ($timeRange === '90d') {
            $query->where('created_at', '>=', now()->subDays(90));
        }

        $submissions = $query->get();
        $totalSubmissions = $submissions->count();
        $approvedSubmissions = $submissions->whereIn('status', ['approved', 'approved_pending_payout', 'verified'])->count();
        $rejectedSubmissions = $submissions->where('status', 'rejected')->count();

        $totalReportedViews = (int) $submissions->sum('observed_views');
        $totalEligibleViews = (int) $submissions->whereIn('status', ['approved', 'approved_pending_payout', 'verified'])->sum('eligible_views');
        if ($totalEligibleViews === 0 && $user->creatorProfile && $user->creatorProfile->total_verified_views > 0 && $timeRange === 'all') {
            $totalEligibleViews = (int) $user->creatorProfile->total_verified_views;
        }

        $totalEarnings = (float) Payout::where('clipper_id', $user->id)->sum('net_payout') + (float) ($user->wallet?->available_balance ?? 0.00);

        $approvalRate = $totalSubmissions > 0 
            ? round(($approvedSubmissions / $totalSubmissions) * 100, 1) 
            : 0.0;

        $avgViewsPerApprovedClip = $approvedSubmissions > 0 
            ? (int) round($totalEligibleViews / $approvedSubmissions) 
            : 0;

        $avgEarningsPerApprovedClip = $approvedSubmissions > 0 
            ? round($totalEarnings / $approvedSubmissions, 2) 
            : 0.00;

        return [
            'total_submissions' => $totalSubmissions,
            'approved_submissions' => $approvedSubmissions,
            'rejected_submissions' => $rejectedSubmissions,
            'approval_rate' => $approvalRate,
            'total_verified_views' => $totalEligibleViews,
            'total_reported_views' => $totalReportedViews,
            'total_net_earnings' => $totalEarnings,
            'avg_views_per_approved_clip' => $avgViewsPerApprovedClip,
            'avg_earnings_per_approved_clip' => $avgEarningsPerApprovedClip,
        ];
    }

    /**
     * Platform performance breakdown.
     */
    public static function getPlatformPerformance(User $user, string $timeRange = 'all'): Collection
    {
        $query = ClipSubmission::where('user_id', $user->id);

        if ($timeRange === '7d') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($timeRange === '30d') {
            $query->where('created_at', '>=', now()->subDays(30));
        } elseif ($timeRange === '90d') {
            $query->where('created_at', '>=', now()->subDays(90));
        }

        return $query->select(
            'platform',
            DB::raw('count(*) as total_clips'),
            DB::raw('sum(case when status in ("approved", "approved_pending_payout", "verified") then 1 else 0 end) as approved_clips'),
            DB::raw('sum(observed_views) as total_observed_views'),
            DB::raw('sum(eligible_views) as total_eligible_views'),
            DB::raw('sum(coalesce(verified_reward, estimated_reward, 0)) as total_rewards')
        )
        ->groupBy('platform')
        ->orderByDesc('total_eligible_views')
        ->get();
    }

    /**
     * Campaign-by-campaign performance.
     */
    public static function getCampaignBreakdown(User $user, int $limit = 5): Collection
    {
        return ClipSubmission::where('user_id', $user->id)
            ->with(['campaign.brand'])
            ->select(
                'campaign_id',
                DB::raw('count(*) as total_clips'),
                DB::raw('sum(case when status in ("approved", "approved_pending_payout", "verified") then 1 else 0 end) as approved_clips'),
                DB::raw('sum(observed_views) as total_views'),
                DB::raw('sum(eligible_views) as total_eligible_views'),
                DB::raw('sum(coalesce(verified_reward, estimated_reward, 0)) as total_earnings')
            )
            ->groupBy('campaign_id')
            ->orderByDesc('total_eligible_views')
            ->take($limit)
            ->get();
    }
}
