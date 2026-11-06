<?php

namespace App\Services;

use App\Enums\NotificationEvent;
use App\Models\Campaign;
use App\Models\CampaignParticipation;
use App\Models\ClipSubmission;
use App\Models\User;
use Exception;

class ClipSubmissionService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Validate social media URL for specific supported platforms.
     */
    public static function validateSocialUrl(string $url, string $platform): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $urlLower = strtolower($url);
        $plat = strtolower(trim($platform));

        if (str_contains($plat, 'tiktok')) {
            return str_contains($urlLower, 'tiktok.com');
        }

        if (str_contains($plat, 'instagram')) {
            return str_contains($urlLower, 'instagram.com');
        }

        if (str_contains($plat, 'youtube')) {
            return str_contains($urlLower, 'youtube.com') || str_contains($urlLower, 'youtu.be');
        }

        if (str_contains($plat, 'facebook') || str_contains($plat, 'fb')) {
            return str_contains($urlLower, 'facebook.com') || str_contains($urlLower, 'fb.watch');
        }

        if (str_contains($plat, 'x') || str_contains($plat, 'twitter')) {
            return str_contains($urlLower, 'x.com') || str_contains($urlLower, 'twitter.com');
        }

        return true;
    }

    /**
     * Pre-submission validation engine.
     */
    public function validateSubmission(User $user, Campaign $campaign, array $data): array
    {
        $errors = [];

        // 1. Role & Account status
        if (!$user->isClipper()) {
            $errors[] = 'Only creators/clippers can submit clips.';
        }

        if ($user->status === 'suspended') {
            $errors[] = 'Your creator account is suspended.';
        }

        if (!$user->email_verified_at) {
            $errors[] = 'You must verify your email before submitting clips.';
        }

        // 2. Campaign status & budget
        if (!in_array($campaign->status, ['active', 'half_filled'])) {
            $errors[] = 'This campaign is no longer active.';
        }

        if ($campaign->remaining_budget <= 0) {
            $errors[] = 'Campaign bounty budget has been completely exhausted.';
        }

        if ($campaign->end_at && $campaign->end_at->isPast()) {
            $errors[] = 'This campaign has passed its submission deadline.';
        }

        // 3. Platform validation
        $platform = trim($data['platform'] ?? '');
        if (empty($platform)) {
            $errors[] = 'Social platform is required.';
        } else {
            $allowedPlatforms = $campaign->platforms->pluck('platform')->map(fn ($p) => strtolower($p))->toArray();
            if (!empty($allowedPlatforms) && !in_array(strtolower($platform), $allowedPlatforms, true)) {
                $errors[] = "Platform '{$platform}' is not accepted for this campaign. Allowed: " . implode(', ', $campaign->platforms->pluck('platform')->toArray());
            }
        }

        // 4. Social Account Verification Check
        $socialAccountId = $data['social_account_id'] ?? null;
        if ($socialAccountId) {
            $account = \App\Models\SocialAccount::where('user_id', $user->id)->where('id', $socialAccountId)->first();
            if (!$account) {
                $errors[] = 'Selected social channel is not linked to your account.';
            }
        } else {
            $hasAccount = \App\Models\SocialAccount::where('user_id', $user->id)
                ->where(function ($q) use ($platform) {
                    $q->where('platform', 'LIKE', '%' . $platform . '%')
                      ->orWhere('platform', strtolower($platform));
                })->exists();

            if (!$hasAccount) {
                $errors[] = "You must link your {$platform} account under 'Connected Accounts' before submitting clips.";
            }
        }

        // 5. URL or Video File Validation
        $postUrl = trim($data['post_url'] ?? '');
        $videoFilePath = trim($data['video_file_path'] ?? '');

        if (empty($postUrl) && empty($videoFilePath)) {
            $errors[] = 'Either a published post URL or an uploaded video file is required.';
        } elseif (!empty($postUrl) && !self::validateSocialUrl($postUrl, $platform)) {
            $errors[] = "Invalid URL format for {$platform}. Please submit a direct public link.";
        }

        // 6. Duplicate Submission Protection
        if (!empty($postUrl)) {
            $duplicateExists = ClipSubmission::where('post_url', $postUrl)->exists();
            if ($duplicateExists) {
                $errors[] = 'This social URL has already been submitted to GEZUX.';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Create and record a new clip submission with full validation and brand alerting.
     */
    public function createSubmission(User $user, Campaign $campaign, array $data): ClipSubmission
    {
        $validation = $this->validateSubmission($user, $campaign, $data);

        if (!$validation['valid']) {
            throw new Exception(implode(' ', $validation['errors']));
        }

        // Ensure participation record exists
        CampaignParticipation::firstOrCreate(
            ['campaign_id' => $campaign->id, 'user_id' => $user->id],
            ['status' => 'submitted', 'joined_at' => now()]
        );

        $observedViews = (int) ($data['observed_views'] ?? 0);
        $rewardRate = (float) $campaign->reward_rate_per_thousand;
        $estimatedReward = round(($observedViews / 1000) * $rewardRate, 2);

        $socialAccountId = $data['social_account_id'] ?? null;
        if (!$socialAccountId) {
            $platform = trim($data['platform'] ?? '');
            $socialAccount = \App\Models\SocialAccount::where('user_id', $user->id)
                ->where(function ($q) use ($platform) {
                    $q->where('platform', 'LIKE', '%' . $platform . '%')
                      ->orWhere('platform', strtolower($platform));
                })->first();
            $socialAccountId = $socialAccount?->id;
        }

        $submission = ClipSubmission::create([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'social_account_id' => $socialAccountId,
            'platform' => $data['platform'],
            'post_url' => !empty($data['post_url']) ? trim($data['post_url']) : null,
            'video_file_path' => $data['video_file_path'] ?? null,
            'external_post_id' => $data['external_post_id'] ?? null,
            'status' => 'under_review',
            'observed_views' => $observedViews,
            'observed_likes' => (int) ($data['observed_likes'] ?? 0),
            'observed_comments' => (int) ($data['observed_comments'] ?? 0),
            'observed_shares' => (int) ($data['observed_shares'] ?? 0),
            'eligible_views' => 0,
            'estimated_reward' => $estimatedReward,
            'verified_reward' => null,
            'fraud_risk_score' => 0,
        ]);

        // Notify Brand that a new clip submission was received
        if ($campaign->brand && $campaign->brand->user) {
            $this->notificationService->send(
                user: $campaign->brand->user,
                event: NotificationEvent::CLIP_SUBMITTED,
                title: 'New Clip Submission Received',
                body: "Creator {$user->name} submitted a clip on {$submission->platform} for '{$campaign->title}'.",
                actionUrl: "/brand/submissions/{$submission->id}",
                metadata: [
                    'submission_id' => $submission->id,
                    'campaign_id' => $campaign->id,
                    'clipper_name' => $user->name,
                    'platform' => $submission->platform,
                ]
            );
        }

        return $submission;
    }
}
