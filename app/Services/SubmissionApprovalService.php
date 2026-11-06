<?php

namespace App\Services;

use App\Enums\NotificationEvent;
use App\Models\Brand;
use App\Models\ClipSubmission;
use App\Models\Payout;
use Exception;
use Illuminate\Support\Facades\DB;

class SubmissionApprovalService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Brand approves a clip submission.
     * CRITICAL RULE: Brand approval only validates work quality.
     * It DOES NOT execute an automatic payout to the clipper.
     * It creates a PENDING_ADMIN_REVIEW payout task and notifies admin staff.
     */
    public function approveSubmissionByBrand(Brand $brand, ClipSubmission $submission): Payout
    {
        // 1. Authorize: Submission must belong to a campaign owned by this brand
        if ($submission->campaign->brand_id !== $brand->id) {
            throw new Exception('Unauthorized: This submission does not belong to your brand campaign.');
        }

        // 2. Validate current state
        if (in_array($submission->status, ['rejected'])) {
            throw new Exception('Cannot approve a rejected submission.');
        }

        // 3. Atomic Database Transaction
        $payout = DB::transaction(function () use ($brand, $submission) {
            $campaign = $submission->campaign;
            $cpm = (float) ($campaign->reward_rate_per_thousand ?? 1.00);
            $views = (int) ($submission->observed_views > 0 ? $submission->observed_views : $submission->eligible_views);
            
            // Calculate Gross Earning from Verified CPM
            $grossEarning = round(($views / 1000) * $cpm, 2);
            if ($grossEarning <= 0 && $views > 0) {
                $grossEarning = 1.00; // Minimum floor for verified view milestones
            }

            // Commission Rules (e.g. 10% Platform Commission)
            $commissionRate = 10.00;
            $commissionAmount = round($grossEarning * ($commissionRate / 100), 2);
            $netPayout = max(0.00, $grossEarning - $commissionAmount);

            // Update Submission Status to Brand Approved / Payout Pending
            $submission->update([
                'status' => 'approved_pending_payout',
                'eligible_views' => $views,
                'verified_reward' => $grossEarning,
                'verified_at' => now(),
            ]);

            // Create Payout in PENDING_ADMIN_REVIEW state (NO MONEY MOVEMENT)
            $payoutRecord = Payout::create([
                'submission_id' => $submission->id,
                'campaign_id' => $campaign->id,
                'brand_id' => $brand->id,
                'clipper_id' => $submission->user_id,
                'gross_earning' => $grossEarning,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'other_deductions' => 0.00,
                'net_payout' => $netPayout,
                'currency' => $campaign->currency ?? 'USD',
                'status' => 'PENDING_ADMIN_REVIEW',
                'payment_method' => 'stripe_connect',
                'destination_account' => 'acct_pending_review',
                'approved_by_brand_at' => now(),
            ]);

            return $payoutRecord;
        });

        // 4. Dispatch Multi-Channel Admin & Clipper Notifications After Transaction Commits
        $clipper = $submission->user;
        
        // Notify Admins: Action required to review & manually dispatch payout
        $this->notificationService->notifyAdmins(
            event: NotificationEvent::PAYOUT_REVIEW_REQUIRED,
            title: 'Clip Approved — Payout Review Required',
            body: "Brand {$brand->company_name} approved submission #{$submission->id} for {$clipper->name}. Gross: \${$payout->gross_earning}, Net: \${$payout->net_payout}. Review and manually execute payout.",
            actionUrl: '/admin/withdrawals',
            metadata: [
                'payout_id' => $payout->id,
                'submission_id' => $submission->id,
                'brand_name' => $brand->company_name,
                'clipper_name' => $clipper->name,
                'gross_earning' => $payout->gross_earning,
                'net_payout' => $payout->net_payout,
                'status' => 'PENDING_ADMIN_REVIEW',
            ]
        );

        // Notify Clipper: Submission approved, payout is in admin queue
        $this->notificationService->send(
            user: $clipper,
            event: NotificationEvent::BRAND_APPROVED_SUBMISSION,
            title: 'Clip Approved by Brand!',
            body: "Your submission for campaign '{$submission->campaign->title}' was approved by {$brand->company_name}. Payout of \${$payout->net_payout} is queued for admin review.",
            actionUrl: '/dashboard/earnings',
            metadata: [
                'submission_id' => $submission->id,
                'campaign_id' => $submission->campaign_id,
                'gross_earning' => $payout->gross_earning,
                'net_payout' => $payout->net_payout,
            ]
        );

        return $payout;
    }

    /**
     * Brand rejects a clip submission.
     */
    public function rejectSubmissionByBrand(Brand $brand, ClipSubmission $submission, string $reason): void
    {
        if ($submission->campaign->brand_id !== $brand->id) {
            throw new Exception('Unauthorized: This submission does not belong to your brand campaign.');
        }

        $submission->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        $clipper = $submission->user;
        $this->notificationService->send(
            user: $clipper,
            event: NotificationEvent::BRAND_REJECTED_SUBMISSION,
            title: 'Clip Submission Needs Correction',
            body: "Your submission for '{$submission->campaign->title}' was not approved by {$brand->company_name}. Reason: {$reason}",
            actionUrl: '/dashboard/submissions',
            metadata: [
                'submission_id' => $submission->id,
                'reason' => $reason,
            ]
        );
    }
}
