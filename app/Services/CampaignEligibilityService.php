<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignParticipation;
use App\Models\SavedCampaign;
use App\Models\User;
use Exception;

class CampaignEligibilityService
{
    /**
     * Evaluate server-side eligibility for a Clipper on a specific Campaign.
     */
    public static function checkEligibility(User $user, Campaign $campaign): array
    {
        $reasons = [];
        $checklist = [];

        // 1. Role verification
        $isClipper = $user->isClipper();
        $checklist['is_clipper'] = $isClipper;
        if (!$isClipper) {
            $reasons[] = 'Only registered creators/clippers can participate in campaigns.';
        }

        // 2. Account Status
        $isActive = $user->status !== 'suspended';
        $checklist['is_active'] = $isActive;
        if (!$isActive) {
            $reasons[] = 'Your account is currently restricted or suspended.';
        }

        // 3. Email verification
        $emailVerified = $user->email_verified_at !== null;
        $checklist['email_verified'] = $emailVerified;
        if (!$emailVerified) {
            $reasons[] = 'Email verification is required before joining campaigns.';
        }

        // 4. Campaign status & capacity
        $isCampaignActive = $campaign->status === 'active';
        $checklist['campaign_active'] = $isCampaignActive;
        if (!$isCampaignActive) {
            $reasons[] = 'This campaign is currently not active.';
        }

        $hasBudget = $campaign->remaining_budget > 0;
        $checklist['has_budget'] = $hasBudget;
        if (!$hasBudget) {
            $reasons[] = 'Campaign bounty budget has been fully allocated.';
        }

        // 5. Geographic / Country requirements
        $campaignCountries = $campaign->countries;
        $userCountry = strtoupper($user->creatorProfile?->country ?? 'US');
        $countryEligible = true;

        if ($campaignCountries->isNotEmpty()) {
            $allowedCodes = $campaignCountries->pluck('country_code')->map(fn ($c) => strtoupper($c))->toArray();
            $countryEligible = in_array($userCountry, $allowedCodes, true);
            if (!$countryEligible) {
                $reasons[] = "This campaign is restricted to creators in: " . implode(', ', $allowedCodes) . " (Your profile country: {$userCountry}).";
            }
        }
        $checklist['country_eligible'] = $countryEligible;

        // 6. Social platform requirements
        $campaignPlatforms = $campaign->platforms->pluck('platform')->map(fn ($p) => strtolower($p))->toArray();
        $userConnectedPlatforms = $user->socialAccounts->pluck('platform')->map(fn ($p) => strtolower($p))->toArray();
        $platformEligible = true;

        if (!empty($campaignPlatforms)) {
            // Check if user has at least one connected platform that matches campaign
            $hasMatchingPlatform = count(array_intersect($campaignPlatforms, $userConnectedPlatforms)) > 0;
            if (!$hasMatchingPlatform && !empty($userConnectedPlatforms)) {
                $reasons[] = "Campaign requires one of: " . implode(', ', $campaign->platforms->pluck('platform')->toArray()) . ". Connect the required platform first.";
                $platformEligible = false;
            }
        }
        $checklist['platform_eligible'] = $platformEligible;

        // Participation state
        $participation = CampaignParticipation::where('campaign_id', $campaign->id)
            ->where('user_id', $user->id)
            ->first();

        $participationStatus = $participation ? $participation->status : 'not_joined';

        // Check if clipper has submissions for this campaign
        $submissionCount = $user->submissions()->where('campaign_id', $campaign->id)->count();

        // Check if campaign is saved
        $isSaved = SavedCampaign::where('campaign_id', $campaign->id)
            ->where('user_id', $user->id)
            ->exists();

        $isEligible = empty($reasons);

        return [
            'eligible' => $isEligible,
            'reasons' => $reasons,
            'checklist' => $checklist,
            'participation_status' => $participationStatus,
            'is_joined' => $participation !== null,
            'submission_count' => $submissionCount,
            'is_saved' => $isSaved,
        ];
    }

    /**
     * Join a campaign with full server-side validation.
     */
    public static function joinCampaign(User $user, Campaign $campaign): CampaignParticipation
    {
        $check = self::checkEligibility($user, $campaign);

        if (!$check['eligible']) {
            throw new Exception(implode(' ', $check['reasons']));
        }

        return CampaignParticipation::firstOrCreate(
            [
                'campaign_id' => $campaign->id,
                'user_id' => $user->id,
            ],
            [
                'status' => 'joined',
                'joined_at' => now(),
            ]
        );
    }

    /**
     * Toggle bookmarking/saving for a campaign.
     */
    public static function toggleSaveCampaign(User $user, Campaign $campaign): bool
    {
        $existing = SavedCampaign::where('campaign_id', $campaign->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        SavedCampaign::create([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
        ]);

        return true;
    }
}
