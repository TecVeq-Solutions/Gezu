<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Campaign;
use App\Models\ClipSubmission;
use App\Models\NotificationLog;
use Illuminate\Support\Collection;

class BrandDashboardService
{
    /**
     * Get real KPI metrics for the authenticated brand.
     */
    public static function getMetrics(Brand $brand): array
    {
        $campaignsQuery = Campaign::where('brand_id', $brand->id);
        $campaignIds = (clone $campaignsQuery)->pluck('id');

        $activeCampaigns = (clone $campaignsQuery)->where('status', 'active')->count();
        $totalBudget = (float) (clone $campaignsQuery)->sum('total_budget');
        $remainingBudget = (float) (clone $campaignsQuery)->sum('remaining_budget');
        $totalSpent = max(0, $totalBudget - $remainingBudget);
        $totalVerifiedViews = (int) (clone $campaignsQuery)->sum('verified_views');

        $submissionsQuery = ClipSubmission::whereIn('campaign_id', $campaignIds);
        $totalSubmissions = (clone $submissionsQuery)->count();
        $pendingReviews = (clone $submissionsQuery)->whereIn('status', ['pending', 'under_review', 'pending_review'])->count();
        $approvedClips = (clone $submissionsQuery)->whereIn('status', ['approved', 'approved_pending_payout', 'verified'])->count();

        return [
            'active_campaigns' => $activeCampaigns,
            'total_campaigns' => $campaignIds->count(),
            'total_budget' => $totalBudget,
            'total_spent' => $totalSpent,
            'remaining_budget' => $remainingBudget,
            'verified_views' => $totalVerifiedViews,
            'total_submissions' => $totalSubmissions,
            'pending_reviews' => $pendingReviews,
            'approved_clips' => $approvedClips,
        ];
    }

    /**
     * Action items that require brand attention.
     */
    public static function getNeedsAttention(Brand $brand): array
    {
        $items = [];
        $campaignIds = Campaign::where('brand_id', $brand->id)->pluck('id');

        // 1. Pending Clip Reviews
        $pendingReviewsCount = ClipSubmission::whereIn('campaign_id', $campaignIds)
            ->whereIn('status', ['pending', 'under_review', 'pending_review'])
            ->count();

        if ($pendingReviewsCount > 0) {
            $items[] = [
                'type' => 'reviews',
                'severity' => 'warning',
                'icon' => 'heroicon-o-video-camera',
                'title' => "{$pendingReviewsCount} Clip Submissions Awaiting Review",
                'description' => 'Creators have submitted fresh videos. Review and approve to progress verification.',
                'action_label' => 'Review Submissions',
                'action_url' => '/brand/submitted-clips',
                'count' => $pendingReviewsCount,
            ];
        }

        // 2. Campaigns Low on Budget
        $lowBudgetCampaigns = Campaign::where('brand_id', $brand->id)
            ->where('status', 'active')
            ->where('remaining_budget', '<', 200)
            ->get();

        foreach ($lowBudgetCampaigns as $camp) {
            $items[] = [
                'type' => 'budget',
                'severity' => 'danger',
                'icon' => 'heroicon-o-banknotes',
                'title' => "Low Budget Alert: {$camp->title}",
                'description' => "Only $" . number_format((float) $camp->remaining_budget, 2) . " remaining in campaign bounty pool.",
                'action_label' => 'View Campaign',
                'action_url' => '/brand/campaigns',
                'count' => null,
            ];
        }

        return $items;
    }

    /**
     * Active campaigns list with real progress metrics.
     */
    public static function getActiveCampaigns(Brand $brand, int $limit = 5): Collection
    {
        return Campaign::where('brand_id', $brand->id)
            ->where('status', 'active')
            ->withCount([
                'submissions as pending_count' => fn ($q) => $q->whereIn('status', ['pending', 'under_review', 'pending_review']),
                'submissions as total_count',
            ])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Recent clip submissions.
     */
    public static function getRecentSubmissions(Brand $brand, int $limit = 6): Collection
    {
        $campaignIds = Campaign::where('brand_id', $brand->id)->pluck('id');

        return ClipSubmission::whereIn('campaign_id', $campaignIds)
            ->with(['campaign', 'user.creatorProfile'])
            ->latest()
            ->take($limit)
            ->get();
    }
}
