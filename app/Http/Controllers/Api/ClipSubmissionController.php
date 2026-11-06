<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitClipRequest;
use App\Models\ClipSubmission;
use App\Models\Campaign;
use App\Services\ViewVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClipSubmissionController extends Controller
{
    protected ViewVerificationService $verificationService;

    public function __construct(ViewVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    /**
     * Submit a new clip post for verification.
     */
    public function store(SubmitClipRequest $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $data = $request->validated();
        $campaign = Campaign::findOrFail($data['campaign_id']);

        try {
            $service = app(\App\Services\ClipSubmissionService::class);
            $submission = $service->createSubmission($user, $campaign, [
                'platform' => $data['platform'],
                'post_url' => $data['post_url'],
                'external_post_id' => $data['external_post_id'] ?? null,
                'observed_views' => 1000,
            ]);

            return response()->json([
                'message' => 'Clip submission registered successfully.',
                'submission' => $submission->fresh(['campaign']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * List authenticated user's submissions.
     */
    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $submissions = ClipSubmission::with('campaign')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get();

        return response()->json($submissions);
    }
}
