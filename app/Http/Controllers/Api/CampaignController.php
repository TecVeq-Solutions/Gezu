<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCampaignRequest;
use App\Models\Campaign;
use App\Models\Brand;
use App\Services\CampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    protected CampaignService $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    /**
     * List all public active campaigns.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Campaign::with(['countries', 'platforms', 'brand'])->where('status', 'active');

        if ($request->has('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        try {
            $campaigns = $query->orderByDesc('reward_rate_per_thousand')->get();
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'success',
                'data' => [],
                'message' => 'Database tables initializing'
            ]);
        }

        $formatted = $campaigns->map(function ($c) {
            $daysRemaining = $c->end_at ? max(0, (int) now()->diffInDays($c->end_at, false)) : 14;
            return [
                'id' => (string) $c->id,
                'title' => $c->title,
                'slug' => $c->slug ?? 'campaign-' . $c->id,
                'brandName' => $c->brand->company_name ?? 'GEZUX Sponsor',
                'brandLogo' => $c->brand->logo ?? '/images/gezux-favicon.jpg',
                'category' => $c->category ?? 'Technology & AI',
                'description' => $c->description ?? '',
                'sourceVideoUrl' => $c->source_video_url ?? '',
                'sourceDuration' => $c->source_duration ?? '10m',
                'thumbnail' => $c->thumbnail ?? 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=800&q=80',
                'totalBudget' => (float) $c->total_budget,
                'remainingBudget' => (float) $c->remaining_budget,
                'rewardRatePerThousand' => (float) $c->reward_rate_per_thousand,
                'currency' => $c->currency ?? 'USD',
                'targetViews' => (int) $c->target_views,
                'verifiedViews' => (int) $c->verified_views,
                'status' => $c->status,
                'platforms' => $c->platforms->pluck('platform')->toArray() ?: ['TikTok', 'Instagram', 'YouTube Shorts'],
                'countries' => $c->countries->map(fn($country) => [
                    'countryCode' => $country->country_code,
                    'countryName' => $country->country_name,
                    'rewardMultiplier' => (float) $country->reward_multiplier,
                    'targetViews' => (int) $country->target_views,
                ])->toArray(),
                'requirements' => [
                    'aspectRatio' => '9:16',
                    'duration' => '30–60s',
                    'requiredHashtags' => ['#gezux', '#clips'],
                    'requiredMentions' => ['@gezux'],
                    'prohibitedContent' => ['No explicit content', 'No unauthorized audio'],
                    'brandMentionRequired' => true,
                ],
                'deadline' => $c->end_at ? $c->end_at->format('M d, Y') : now()->addDays(14)->format('M d, Y'),
                'daysRemaining' => $daysRemaining,
                'totalSubmissions' => (int) ($c->submissions_count ?? 0),
                'createdAt' => $c->created_at ? $c->created_at->toIso8601String() : now()->toIso8601String(),
            ];
        });

        return response()->json([
            'data' => $formatted,
            'total' => $formatted->count(),
        ]);
    }

    /**
     * Store and initialize a new campaign for authenticated brand.
     */
    public function store(StoreCampaignRequest $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        if (!$user->isBrand()) {
            return response()->json(['error' => 'Only registered Brand accounts can create campaigns.'], 403);
        }

        $brand = $user->brand ?: Brand::firstOrCreate(
            ['user_id' => $user->id],
            ['company_name' => $user->name, 'company_slug' => \Illuminate\Support\Str::slug($user->name) . '-' . $user->id, 'verification_status' => 'verified']
        );

        $campaign = $this->campaignService->createCampaign($brand, $request->validated());

        return response()->json([
            'message' => 'Campaign created successfully.',
            'campaign' => $campaign->load(['countries', 'platforms']),
        ], 201);
    }

    /**
     * Show campaign details.
     */
    public function show(Campaign $campaign): JsonResponse
    {
        return response()->json($campaign->load(['countries', 'platforms', 'brand']));
    }
}
