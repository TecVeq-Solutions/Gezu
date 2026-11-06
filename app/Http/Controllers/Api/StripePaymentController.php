<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripePaymentController extends Controller
{
    /**
     * Create a Stripe Checkout session for funding a campaign.
     */
    public function createCheckoutSession(Request $request): JsonResponse
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'amount' => 'required|numeric|min:100',
        ]);

        $campaign = Campaign::findOrFail($request->campaign_id);

        // Generate simulated Stripe Checkout URL & Session ID
        $sessionId = 'cs_test_' . bin2hex(random_bytes(16));
        $checkoutUrl = url("/brand/campaigns/{$campaign->id}/fund/success?session_id={$sessionId}");

        return response()->json([
            'checkout_url' => $checkoutUrl,
            'session_id' => $sessionId,
            'campaign_id' => $campaign->id,
            'amount' => $request->amount,
            'currency' => $campaign->currency,
        ]);
    }

    /**
     * Handle Stripe Webhooks with Idempotency.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        $eventType = $payload['type'] ?? 'checkout.session.completed';

        Log::info("GEZUX Stripe Webhook Received: {$eventType}", $payload);

        if ($eventType === 'checkout.session.completed') {
            $campaignId = $payload['data']['object']['metadata']['campaign_id'] ?? null;
            if ($campaignId) {
                $campaign = Campaign::find($campaignId);
                if ($campaign) {
                    $campaign->update(['status' => 'active']);
                }
            }
        }

        return response()->json(['received' => true], 200);
    }
}
