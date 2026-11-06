<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\ClipSubmission;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ClipperDashboardService
{
    /**
     * Compute authentic telemetry and financial metrics for the Clipper.
     */
    public static function getMetrics(User $user): array
    {
        $wallet = $user->wallet;
        $profile = $user->creatorProfile;

        // Submissions aggregations
        $totalSubmissions = $user->submissions()->count();
        $approvedClips = $user->submissions()->whereIn('status', ['verified', 'approved'])->count();
        $pendingSubmissions = $user->submissions()->whereIn('status', ['pending', 'under_review'])->count();
        $rejectedSubmissions = $user->submissions()->where('status', 'rejected')->count();

        // View metrics (distinguish reported vs verified/eligible)
        $reportedViews = (int) $user->submissions()->sum('observed_views');
        $eligibleViews = (int) $user->submissions()->whereIn('status', ['verified', 'approved'])->sum('eligible_views');
        if ($eligibleViews === 0 && $profile && $profile->total_verified_views > 0) {
            $eligibleViews = (int) $profile->total_verified_views;
        }

        // Financial metrics (Real database values)
        $paidOut = (float) Payout::where('clipper_id', $user->id)
            ->where('status', 'PAID')
            ->sum('net_payout');

        $pendingPayouts = (float) Payout::where('clipper_id', $user->id)
            ->whereIn('status', ['PENDING_ADMIN_REVIEW', 'APPROVED_FOR_PAYOUT', 'PROCESSING'])
            ->sum('net_payout');

        $availableBalance = (float) ($wallet?->available_balance ?? 0.00);
        $walletPending = (float) ($wallet?->pending_balance ?? 0.00);

        $pendingEarnings = $pendingPayouts + $walletPending;
        $lifetimeEarnings = $paidOut + $availableBalance + (float) ($profile?->total_earnings ?? 0.00);

        return [
            'lifetime_earnings' => $lifetimeEarnings,
            'available_balance' => $availableBalance,
            'pending_earnings' => $pendingEarnings,
            'paid_out' => $paidOut,
            'verified_views' => $eligibleViews,
            'reported_views' => $reportedViews,
            'approved_clips' => $approvedClips,
            'pending_submissions' => $pendingSubmissions,
            'rejected_submissions' => $rejectedSubmissions,
            'total_submissions' => $totalSubmissions,
            'currency' => $wallet?->currency ?? 'USD',
        ];
    }

    /**
     * Compute authentic onboarding checklist state based on database records.
     */
    public static function getOnboardingStatus(User $user): array
    {
        $profile = $user->creatorProfile;
        $hasSocial = $user->socialAccounts()->exists();
        $hasWallet = $user->wallet !== null;
        $hasSubmission = $user->submissions()->exists();
        $hasApprovedClip = $user->submissions()->whereIn('status', ['verified', 'approved'])->exists();

        $steps = [
            [
                'id' => 'account_created',
                'title' => 'Account Created',
                'description' => 'Your GEZUX creator account is registered.',
                'completed' => true,
                'action_label' => null,
                'action_url' => null,
            ],
            [
                'id' => 'email_verified',
                'title' => 'Email Verification',
                'description' => $user->email_verified_at ? 'Email address confirmed.' : 'Verify your email to unlock all features.',
                'completed' => $user->email_verified_at !== null,
                'action_label' => 'Verify Email',
                'action_url' => '/verify-email',
            ],
            [
                'id' => 'profile_completed',
                'title' => 'Complete Creator Profile',
                'description' => ($profile && !empty($profile->bio)) ? 'Profile bio and details configured.' : 'Add your bio, avatar, and clipping niches.',
                'completed' => $profile !== null && !empty($profile->bio),
                'action_label' => 'Edit Profile',
                'action_url' => '/clipper/profile',
            ],
            [
                'id' => 'social_connected',
                'title' => 'Connect Social Account',
                'description' => $hasSocial ? 'Social accounts linked for clip verification.' : 'Connect TikTok, YouTube, or Instagram.',
                'completed' => $hasSocial,
                'action_label' => 'Connect Social',
                'action_url' => '/clipper/social-accounts',
            ],
            [
                'id' => 'payment_setup',
                'title' => 'Payment & Payout Setup',
                'description' => $hasWallet ? 'Payout destination ready.' : 'Set up Stripe Connect or payout destination.',
                'completed' => $hasWallet,
                'action_label' => 'Setup Payouts',
                'action_url' => '/clipper/payment-setup',
            ],
            [
                'id' => 'first_clip_submitted',
                'title' => 'Submit First Clip',
                'description' => $hasSubmission ? 'First clip submitted to marketplace.' : 'Find an active campaign and submit your clip.',
                'completed' => $hasSubmission,
                'action_label' => 'Discover Campaigns',
                'action_url' => '/clipper/campaigns',
            ],
        ];

        $completedCount = count(array_filter($steps, fn ($step) => $step['completed']));
        $totalCount = count($steps);

        return [
            'steps' => $steps,
            'completed_count' => $completedCount,
            'total_count' => $totalCount,
            'percentage' => round(($completedCount / $totalCount) * 100),
            'is_all_complete' => $completedCount === $totalCount,
        ];
    }

    /**
     * Compute real action items that require the creator's immediate attention.
     */
    public static function getNeedsAttention(User $user): array
    {
        $items = [];

        // 1. Email Verification
        if (!$user->email_verified_at) {
            $items[] = [
                'title' => 'Verify your email address',
                'description' => 'Unverified accounts cannot request payouts or receive campaign bounties.',
                'action_label' => 'Verify Now',
                'action_url' => '/verify-email',
                'severity' => 'warning',
            ];
        }

        // 2. Social Accounts missing
        if (!$user->socialAccounts()->exists()) {
            $items[] = [
                'title' => 'Connect your social accounts',
                'description' => 'You need at least one connected social account (TikTok, Instagram, YouTube) to submit clips.',
                'action_label' => 'Connect Accounts',
                'action_url' => '/clipper/social-accounts',
                'severity' => 'warning',
            ];
        }

        // 3. Rejected submissions requiring attention
        $rejectedCount = $user->submissions()->where('status', 'rejected')->count();
        if ($rejectedCount > 0) {
            $items[] = [
                'title' => "{$rejectedCount} submission(s) need changes",
                'description' => 'Review brand feedback and resubmit your clips to earn rewards.',
                'action_label' => 'View Submissions',
                'action_url' => '/clipper/submissions',
                'severity' => 'danger',
            ];
        }

        // 4. Incomplete Profile
        $profile = $user->creatorProfile;
        if (!$profile || empty($profile->bio)) {
            $items[] = [
                'title' => 'Complete your creator profile',
                'description' => 'Add your bio and clipping niches to improve campaign acceptance.',
                'action_label' => 'Edit Profile',
                'action_url' => '/clipper/profile',
                'severity' => 'info',
            ];
        }

        return $items;
    }

    /**
     * Fetch active campaigns available for discovery.
     */
    public static function getCampaignOpportunities(User $user, int $limit = 4): Collection
    {
        return Campaign::query()
            ->with(['brand', 'platforms', 'countries'])
            ->where('status', 'active')
            ->where('remaining_budget', '>', 0)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Fetch recent submissions submitted by this Clipper.
     */
    public static function getRecentSubmissions(User $user, int $limit = 5): Collection
    {
        return $user->submissions()
            ->with(['campaign.brand'])
            ->latest()
            ->take($limit)
            ->get();
    }
}
