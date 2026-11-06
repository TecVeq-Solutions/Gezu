<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\ClipSubmission;
use App\Models\Payout;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BrandPanelController extends Controller implements HasMiddleware
{
    /**
     * Assign Middleware ensuring Brand or Impersonated Admin access.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = Auth::user();
                if (!$user) {
                    return redirect()->guest(route('login'));
                }
                if (!$user->isBrand() && !session()->has('impersonated_by')) {
                    if ($user->isAdmin()) {
                        return redirect()->to('/admin');
                    }
                    abort(403, 'Unauthorized. Brand advertiser access required.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Get or create Brand model for current user
     */
    protected function getBrand()
    {
        $user = Auth::user();
        $brand = Brand::where('user_id', $user->id)->first();
        if (!$brand) {
            $brandData = [
                'user_id' => $user->id,
                'company_name' => $user->name ?? 'Brand Studio',
                'company_slug' => Str::slug(($user->name ?? 'brand') . '-' . Str::random(5)),
            ];
            if (Schema::hasColumn('brands', 'contact_email')) {
                $brandData['contact_email'] = $user->email;
            }
            if (Schema::hasColumn('brands', 'status')) {
                $brandData['status'] = 'active';
            }
            $brand = Brand::create($brandData);
        }
        return $brand;
    }

    /**
     * Main Brand Dashboard
     */
    public function dashboard()
    {
        $brand = $this->getBrand();
        $user = Auth::user();
        $pageTitle = 'Brand Command Center — Dashboard';

        $campaignsQuery = Campaign::where('brand_id', $brand->id);
        $campaignIds = $campaignsQuery->pluck('id');

        $totalCampaigns = $campaignsQuery->count();
        $activeCampaigns = Campaign::where('brand_id', $brand->id)->where('status', 'active')->count();
        $totalEscrow = (float) Campaign::where('brand_id', $brand->id)->sum('total_budget');
        $remainingBudget = (float) Campaign::where('brand_id', $brand->id)->sum('remaining_budget');
        $totalSpent = max(0, $totalEscrow - $remainingBudget);

        $pendingClips = ClipSubmission::whereIn('campaign_id', $campaignIds)
            ->whereIn('status', ['pending', 'under_review'])
            ->count();

        $approvedClips = ClipSubmission::whereIn('campaign_id', $campaignIds)
            ->whereIn('status', ['approved_by_brand', 'verified', 'paid'])
            ->count();

        $totalViews = (int) ClipSubmission::whereIn('campaign_id', $campaignIds)->sum('observed_views');

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['currency' => 'USD', 'available_balance' => 0.00]);

        $widget = [
            'active_campaigns' => $activeCampaigns,
            'total_campaigns' => $totalCampaigns,
            'total_escrow' => $totalEscrow,
            'remaining_budget' => $remainingBudget,
            'total_spent' => $totalSpent,
            'pending_clips' => $pendingClips,
            'approved_clips' => $approvedClips,
            'total_views' => $totalViews,
            'wallet_balance' => (float) $wallet->available_balance,
        ];

        $recentCampaigns = Campaign::where('brand_id', $brand->id)->latest()->take(5)->get();
        $recentClips = ClipSubmission::with(['user', 'campaign'])
            ->whereIn('campaign_id', $campaignIds)
            ->latest()
            ->take(5)
            ->get();

        return view('brand.dashboard', compact('pageTitle', 'brand', 'widget', 'recentCampaigns', 'recentClips'));
    }

    /**
     * Manage Brand Campaigns
     */
    public function campaigns(Request $request)
    {
        $brand = $this->getBrand();
        $pageTitle = 'My Performance Campaigns';
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Campaign::where('brand_id', $brand->id)->with('submissions')->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('unfunded')) {
            session()->flash('warning', 'Campaign created and saved in Unfunded Draft status. Deposit budget via Stripe or Brand Wallet whenever you are ready to publish to marketplace.');
        }

        $campaigns = $query->paginate(12)->withQueryString();

        return view('brand.campaigns.index', compact('pageTitle', 'campaigns', 'status', 'search'));
    }

    /**
     * Get Curated Country Presets for Targeting
     */
    public static function getPresetCountries(): array
    {
        return [
            ['code' => 'US', 'name' => 'United States', 'flag' => '🇺🇸', 'flag_url' => 'https://flagcdn.com/24x18/us.png', 'default_cpm' => 3.50],
            ['code' => 'GB', 'name' => 'United Kingdom', 'flag' => '🇬🇧', 'flag_url' => 'https://flagcdn.com/24x18/gb.png', 'default_cpm' => 3.00],
            ['code' => 'CA', 'name' => 'Canada', 'flag' => '🇨🇦', 'flag_url' => 'https://flagcdn.com/24x18/ca.png', 'default_cpm' => 3.00],
            ['code' => 'AU', 'name' => 'Australia', 'flag' => '🇦🇺', 'flag_url' => 'https://flagcdn.com/24x18/au.png', 'default_cpm' => 3.00],
            ['code' => 'DE', 'name' => 'Germany', 'flag' => '🇩🇪', 'flag_url' => 'https://flagcdn.com/24x18/de.png', 'default_cpm' => 2.50],
            ['code' => 'FR', 'name' => 'France', 'flag' => '🇫🇷', 'flag_url' => 'https://flagcdn.com/24x18/fr.png', 'default_cpm' => 2.50],
            ['code' => 'AE', 'name' => 'United Arab Emirates', 'flag' => '🇦🇪', 'flag_url' => 'https://flagcdn.com/24x18/ae.png', 'default_cpm' => 2.50],
            ['code' => 'SA', 'name' => 'Saudi Arabia', 'flag' => '🇸🇦', 'flag_url' => 'https://flagcdn.com/24x18/sa.png', 'default_cpm' => 2.50],
            ['code' => 'PK', 'name' => 'Pakistan', 'flag' => '🇵🇰', 'flag_url' => 'https://flagcdn.com/24x18/pk.png', 'default_cpm' => 1.50],
            ['code' => 'IN', 'name' => 'India', 'flag' => '🇮🇳', 'flag_url' => 'https://flagcdn.com/24x18/in.png', 'default_cpm' => 1.50],
            ['code' => 'BR', 'name' => 'Brazil', 'flag' => '🇧🇷', 'flag_url' => 'https://flagcdn.com/24x18/br.png', 'default_cpm' => 1.80],
            ['code' => 'NL', 'name' => 'Netherlands', 'flag' => '🇳🇱', 'flag_url' => 'https://flagcdn.com/24x18/nl.png', 'default_cpm' => 2.50],
            ['code' => 'ES', 'name' => 'Spain', 'flag' => '🇪🇸', 'flag_url' => 'https://flagcdn.com/24x18/es.png', 'default_cpm' => 2.20],
            ['code' => 'IT', 'name' => 'Italy', 'flag' => '🇮🇹', 'flag_url' => 'https://flagcdn.com/24x18/it.png', 'default_cpm' => 2.20],
            ['code' => 'SG', 'name' => 'Singapore', 'flag' => '🇸🇬', 'flag_url' => 'https://flagcdn.com/24x18/sg.png', 'default_cpm' => 2.80],
            ['code' => 'ZA', 'name' => 'South Africa', 'flag' => '🇿🇦', 'flag_url' => 'https://flagcdn.com/24x18/za.png', 'default_cpm' => 1.80],
            ['code' => 'PH', 'name' => 'Philippines', 'flag' => '🇵🇭', 'flag_url' => 'https://flagcdn.com/24x18/ph.png', 'default_cpm' => 1.50],
            ['code' => 'MX', 'name' => 'Mexico', 'flag' => '🇲🇽', 'flag_url' => 'https://flagcdn.com/24x18/mx.png', 'default_cpm' => 1.80],
            ['code' => 'NG', 'name' => 'Nigeria', 'flag' => '🇳🇬', 'flag_url' => 'https://flagcdn.com/24x18/ng.png', 'default_cpm' => 1.50],
            ['code' => 'JP', 'name' => 'Japan', 'flag' => '🇯🇵', 'flag_url' => 'https://flagcdn.com/24x18/jp.png', 'default_cpm' => 2.80],
        ];
    }

    /**
     * Show Launch Campaign Form
     */
    public function createCampaign()
    {
        $brand = $this->getBrand();
        $user = Auth::user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['currency' => 'USD']);
        $pageTitle = 'Launch Performance Campaign';

        $categories = ['Technology & AI', 'Gaming & Esports', 'E-commerce & DTC', 'Crypto & Web3', 'Lifestyle & Fashion', 'Finance & SaaS', 'Health & Fitness'];
        $presetCountries = self::getPresetCountries();

        // Smart default budget: if user has wallet balance, default to that or $100
        $walletBal = (float) $wallet->available_balance;
        $defaultBudget = $walletBal > 0 ? min(100.0, $walletBal) : 100.0;

        return view('brand.campaigns.create', compact('pageTitle', 'brand', 'wallet', 'categories', 'presetCountries', 'defaultBudget'));
    }

    /**
     * Store and Fund Campaign
     */
    public function storeCampaign(Request $request)
    {
        $brand = $this->getBrand();
        $user = Auth::user();

        // Normalize asset_url if provided without protocol
        if ($request->filled('asset_url')) {
            $rawUrl = trim($request->input('asset_url'));
            if (!preg_match('#^https?://#i', $rawUrl)) {
                $request->merge(['asset_url' => 'https://' . $rawUrl]);
            }
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'rules' => 'nullable|string',
            'payout_model' => 'nullable|in:cpm,fixed_per_video',
            'reward_rate_per_thousand' => 'nullable|numeric|min:0.01',
            'reward_per_video' => 'nullable|numeric|min:0.01',
            'target_views_per_video' => 'nullable|integer|min:1',
            'total_budget' => 'required|numeric|min:1',
            'asset_url' => 'nullable|string|max:500',
            'video_file' => 'nullable|file|mimes:mp4,mov,webm,ogg,m4v,avi|max:102400',
            'allowed_platforms' => 'nullable|array',
            'expires_at' => 'nullable|date|after_or_equal:today',
            'funding_method' => 'required|in:stripe,wallet',
            'target_views' => 'nullable|integer|min:1',
            'country_targeting_type' => 'nullable|in:worldwide,single,multiple',
            'single_country_code' => 'nullable|string|max:10',
            'single_country_name' => 'nullable|string|max:100',
            'single_country_cpm' => 'nullable|numeric|min:0.01',
            'countries' => 'nullable|array',
        ]);

        $payoutModel = $validated['payout_model'] ?? 'cpm';
        $budget = (float) $validated['total_budget'];
        $rewardPerVideo = null;
        $targetViewsPerVideo = null;

        if ($payoutModel === 'fixed_per_video') {
            $rewardPerVideo = (float) ($validated['reward_per_video'] ?? 25.00);
            $targetViewsPerVideo = (int) ($validated['target_views_per_video'] ?? 80000);
            $cpm = 0.00;
            $estVideos = $rewardPerVideo > 0 ? (int) floor($budget / $rewardPerVideo) : 1;
            $targetViews = $request->filled('target_views') && (int)$request->input('target_views') > 0
                ? (int) $request->input('target_views')
                : (int) ($estVideos * $targetViewsPerVideo);
        } else {
            $cpm = (float) ($validated['reward_rate_per_thousand'] ?? 2.50);
            $targetViews = $request->filled('target_views') && (int)$request->input('target_views') > 0
                ? (int)$request->input('target_views')
                : ($cpm > 0 ? (int) floor(($budget / $cpm) * 1000) : 100000);
        }

        $slug = \Illuminate\Support\Str::slug($validated['title']) . '-' . \Illuminate\Support\Str::random(5);
        $videoUrl = $validated['asset_url'] ?? null;

        // Process direct video upload if provided
        if ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            $file = $request->file('video_file');
            $origName = $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = 'cmp_' . \Illuminate\Support\Str::slug($validated['title']) . '_' . time() . '.' . $ext;
            $targetDir = public_path('uploads/media/campaign_video');
            if (!\Illuminate\Support\Facades\File::exists($targetDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($targetDir, 0755, true);
            }
            $file->move($targetDir, $filename);
            $relPath = 'uploads/media/campaign_video/' . $filename;
            $videoUrl = asset($relPath);

            // Index into TECVEQ Media Library Organizer
            \App\Models\MediaItem::create([
                'user_id' => $user->id,
                'filename' => $filename,
                'original_name' => $origName,
                'path' => $relPath,
                'url' => $videoUrl,
                'mime_type' => $file->getClientMimeType() ?: 'video/mp4',
                'file_size' => @filesize(public_path($relPath)) ?: 0,
                'folder' => 'uploads/media/campaign_video',
                'category' => 'campaign_video',
                'dimensions' => null,
                'alt_text' => $validated['title'] . ' Source Footage',
            ]);
        }

        if (!$videoUrl) {
            $videoUrl = 'https://youtube.com/watch?v=sample-video-source';
        }

        $expiresAt = $validated['expires_at'] ?? now()->addDays(30);

        // Process Country Targeting & Multi-Country custom CPM rates
        $targetingType = $request->input('country_targeting_type', 'worldwide');
        $countryTargetingData = [
            'mode' => $targetingType,
            'countries' => [],
        ];
        $countriesToInsert = [];

        if ($targetingType === 'single') {
            $cCode = strtoupper(trim($request->input('single_country_code', 'US')));
            $cName = trim($request->input('single_country_name', 'United States'));
            $cCpm = (float)($request->input('single_country_cpm') ?: $cpm);
            if ($cCpm > 0) {
                $cpm = $cCpm;
                if (!$request->filled('target_views')) {
                    $targetViews = (int) floor(($budget / $cpm) * 1000);
                }
            }
            $cMultiplier = 1.00;
            $cViews = $targetViews;

            $countryTargetingData['countries'][] = [
                'code' => $cCode ?: 'US',
                'name' => $cName ?: 'United States',
                'cpm' => $cCpm,
                'multiplier' => $cMultiplier,
                'target_views' => $cViews,
            ];
            $countriesToInsert[] = [
                'country_code' => $cCode ?: 'US',
                'country_name' => $cName ?: 'United States',
                'reward_multiplier' => $cMultiplier,
                'cpm' => $cCpm,
                'target_views' => $cViews,
            ];
        } elseif ($targetingType === 'multiple' && $request->has('countries') && is_array($request->input('countries'))) {
            $totalCountryCpm = 0;
            $countryCount = 0;
            foreach ($request->input('countries') as $row) {
                if (empty($row['name']) && empty($row['code'])) continue;
                $cCode = strtoupper(trim($row['code'] ?? 'US'));
                $cName = trim($row['name'] ?? $cCode);
                $cCpm = isset($row['cpm']) && (float)$row['cpm'] > 0 ? (float)$row['cpm'] : $cpm;
                $totalCountryCpm += $cCpm;
                $countryCount++;
                $cViews = isset($row['target_views']) && (int)$row['target_views'] > 0 
                    ? (int)$row['target_views'] 
                    : ($cCpm > 0 ? (int) floor(($budget / $cCpm) * 1000) : 0);
                $cMultiplier = 1.00;

                $countryTargetingData['countries'][] = [
                    'code' => $cCode ?: 'US',
                    'name' => $cName,
                    'cpm' => $cCpm,
                    'multiplier' => $cMultiplier,
                    'target_views' => $cViews,
                ];
                $countriesToInsert[] = [
                    'country_code' => $cCode ?: 'US',
                    'country_name' => $cName ?: 'Target Country',
                    'reward_multiplier' => $cMultiplier,
                    'cpm' => $cCpm,
                    'target_views' => $cViews,
                ];
            }
            if ($countryCount > 0) {
                $cpm = round($totalCountryCpm / $countryCount, 2);
            }
        }

        Campaign::ensureSchema();

        $campaignData = [
            'brand_id' => $brand->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'category' => $validated['category'],
            'description' => $validated['description'],
            'rules' => $validated['rules'] ?? null,
            'payout_model' => $payoutModel,
            'reward_rate_per_thousand' => $cpm,
            'reward_per_video' => $rewardPerVideo,
            'target_views_per_video' => $targetViewsPerVideo,
            'total_budget' => $budget,
            'remaining_budget' => $budget,
            'target_views' => $targetViews,
            'source_video_url' => $videoUrl,
            'asset_url' => $validated['asset_url'] ?? $videoUrl,
            'allowed_platforms' => $validated['allowed_platforms'] ?? ['tiktok', 'instagram', 'youtube'],
            'requirements_json' => ['country_targeting' => $countryTargetingData],
            'expires_at' => $expiresAt,
            'start_at' => now(),
            'end_at' => $expiresAt,
            'currency' => 'USD',
            'status' => 'draft',
        ];

        // Safe defensive filter: if any column is somehow missing from MySQL schema, preserve it in requirements_json
        foreach (['rules', 'payout_model', 'reward_per_video', 'target_views_per_video', 'asset_url', 'allowed_platforms', 'expires_at'] as $col) {
            if (!Schema::hasColumn('campaigns', $col)) {
                if (isset($campaignData[$col])) {
                    $countryTargetingData[$col] = $campaignData[$col];
                    unset($campaignData[$col]);
                }
            }
        }
        $campaignData['requirements_json'] = ['country_targeting' => $countryTargetingData];

        $campaign = Campaign::create($campaignData);

        // Insert into campaign_countries relation
        if (!empty($countriesToInsert)) {
            foreach ($countriesToInsert as $cRow) {
                try {
                    $campaign->countries()->create($cRow);
                } catch (\Throwable $e) {
                    unset($cRow['cpm']);
                    $campaign->countries()->create($cRow);
                }
            }
        }

        if ($validated['funding_method'] === 'wallet') {
            $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['currency' => 'USD']);
            $currentBal = (float) $wallet->available_balance;
            if ($currentBal < $budget) {
                // Delete the draft campaign so we don't leave orphaned drafts
                $campaign->delete();
                return back()->withInput()->withErrors([
                    'funding_method' => "Insufficient Brand Wallet balance. Available: \$" . number_format($currentBal, 2) . ", but campaign budget requires \$" . number_format($budget, 2) . ". Please lower the budget to \$" . number_format($currentBal, 2) . " or choose Stripe Checkout."
                ]);
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($wallet, $budget, $campaign) {
                $wallet->decrement('available_balance', $budget);
                $campaign->update([
                    'status' => 'active',
                    'remaining_budget' => $budget,
                    'total_budget' => $budget,
                ]);

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'campaign_funding',
                    'amount' => $budget,
                    'currency' => 'USD',
                    'status' => 'completed',
                    'description' => "Budget allocated to campaign: {$campaign->title}",
                    'reference_id' => 'WALLET_CMP_' . $campaign->id,
                ]);
            });

            session()->flash('success', "Campaign funded from Brand Wallet! Your campaign is now ACTIVE on the marketplace.");
            return redirect()->route('brand.campaigns');
        }

        // Stripe Checkout Redirect
        $checkoutService = app(StripeCheckoutService::class);
        $url = $checkoutService->createCampaignCheckoutSession($campaign, $user);

        if ($url) {
            return redirect()->away($url, 303);
        }

        $errorMsg = session('stripe_error') ?: 'Campaign created as draft, but could not connect to Stripe. Please verify your Stripe API keys or click Fund Campaign.';
        session()->flash('error', $errorMsg);
        return redirect()->route('brand.campaigns');
    }

    /**
     * Re-attempt Stripe Checkout for an existing draft campaign
     */
    public function checkoutCampaign($id)
    {
        $brand = $this->getBrand();
        $user = Auth::user();
        $campaign = Campaign::where('brand_id', $brand->id)->findOrFail($id);

        if ($campaign->status === 'active') {
            session()->flash('info', 'This campaign is already active and funded.');
            return redirect()->route('brand.campaigns');
        }

        $checkoutService = app(StripeCheckoutService::class);
        $url = $checkoutService->createCampaignCheckoutSession($campaign, $user);

        if ($url) {
            return redirect()->away($url, 303);
        }

        $errorMsg = session('stripe_error') ?: 'Unable to initiate Stripe checkout. Please ensure Stripe API keys are configured.';
        session()->flash('error', $errorMsg);
        return redirect()->route('brand.campaigns');
    }

    /**
     * Handle Stripe Checkout Success Redirect for Campaign Deposit
     */
    public function depositSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');
        $campaignId = $request->query('campaign_id');

        if (!$sessionId) {
            session()->flash('error', 'Invalid Stripe payment callback session.');
            return redirect()->route('brand.campaigns');
        }

        $brand = $this->getBrand();
        $campaign = null;
        if ($campaignId) {
            $campaign = Campaign::where('brand_id', $brand->id)->find($campaignId);
        }

        $checkoutService = app(StripeCheckoutService::class);
        $result = $checkoutService->handleSuccessfulPayment($sessionId, $campaign);

        if (!empty($result['success'])) {
            session()->flash('success', 'Stripe payment confirmed! Campaign bounty deposited and campaign is now LIVE on GEZUX marketplace.');
        } else {
            session()->flash('warning', $result['message'] ?? 'Payment verification pending. Please check back shortly.');
        }

        return redirect()->route('brand.campaigns');
    }

    /**
     * Pause / Activate Campaign Toggle
     */
    public function toggleCampaignStatus($id)
    {
        $brand = $this->getBrand();
        $campaign = Campaign::where('brand_id', $brand->id)->findOrFail($id);

        if (in_array($campaign->status, ['completed', 'cancelled'])) {
            session()->flash('error', 'Completed campaigns cannot be toggled.');
            return back();
        }

        if ($campaign->status === 'draft') {
            session()->flash('error', 'Unfunded draft campaigns cannot be activated directly. Please deposit campaign budget via Stripe Checkout.');
            return redirect()->route('brand.campaigns.checkout', $campaign->id);
        }

        $newStatus = $campaign->status === 'active' ? 'paused' : 'active';
        $campaign->update(['status' => $newStatus]);

        session()->flash('success', "Campaign status changed to " . strtoupper($newStatus));
        return back();
    }

    /**
     * Show Edit Campaign Form for Brand
     */
    public function editCampaign($id)
    {
        $brand = $this->getBrand();
        $campaign = Campaign::where('brand_id', $brand->id)->findOrFail($id);
        $pageTitle = 'Edit Campaign — ' . $campaign->title;

        $presetCountries = self::getPresetCountries();
        $existingCountries = $campaign->countries()->get()->toArray();

        return view('brand.campaigns.edit', compact('pageTitle', 'campaign', 'brand', 'presetCountries', 'existingCountries'));

    }

    /**
     * Update Campaign Details for Brand
     */
    public function updateCampaign(Request $request, $id)
    {
        $brand = $this->getBrand();
        $campaign = Campaign::where('brand_id', $brand->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'source_video_url' => 'nullable|url',
            'asset_url' => 'nullable|url',
            'rules' => 'nullable|string',
            'payout_model' => 'nullable|in:cpm,fixed_per_video',
            'reward_per_video' => 'nullable|numeric|min:0.5',
            'target_views_per_video' => 'nullable|integer|min:100',
            'video_file' => 'nullable|file|mimes:mp4,mov,webm,ogg,m4v,avi|max:102400',
            'status' => 'required|in:active,paused,draft',
            'target_views' => 'nullable|integer|min:0',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        // Strict security: If campaign is in draft, it CANNOT be converted to active without funding
        if ($campaign->status === 'draft') {
            $validated['status'] = 'draft';
        }

        if ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            $file = $request->file('video_file');
            $origName = $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = 'cmp_' . \Illuminate\Support\Str::slug($validated['title']) . '_' . time() . '.' . $ext;
            $targetDir = public_path('uploads/media/campaign_video');
            if (!\Illuminate\Support\Facades\File::exists($targetDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($targetDir, 0755, true);
            }
            $file->move($targetDir, $filename);
            $relPath = 'uploads/media/campaign_video/' . $filename;
            $videoUrl = asset($relPath);

            // Catalog into Media Library Organizer
            \App\Models\MediaItem::create([
                'user_id' => Auth::id(),
                'filename' => $filename,
                'original_name' => $origName,
                'path' => $relPath,
                'url' => $videoUrl,
                'mime_type' => $file->getClientMimeType() ?: 'video/mp4',
                'file_size' => @filesize(public_path($relPath)) ?: 0,
                'folder' => 'uploads/media/campaign_video',
                'category' => 'campaign_video',
                'dimensions' => null,
                'alt_text' => $validated['title'] . ' Source Footage',
            ]);

            $validated['source_video_url'] = $videoUrl;
            $validated['asset_url'] = $videoUrl;
        }

        unset($validated['video_file']);

        Campaign::ensureSchema();

        $updateData = [];
        $req = $campaign->requirements_json ?? [];
        if (!isset($req['country_targeting'])) {
            $req['country_targeting'] = [];
        }

        foreach ($validated as $key => $val) {
            if (Schema::hasColumn('campaigns', $key)) {
                $updateData[$key] = $val;
            } else {
                $req['country_targeting'][$key] = $val;
            }
        }
        $updateData['requirements_json'] = $req;
        $campaign->update($updateData);

        return redirect()->route('brand.campaigns')->with('success', 'Campaign details updated successfully.');
    }

    /**
     * End Campaign & Refund Leftover Budget
     */
    public function endCampaign($id)
    {
        $brand = $this->getBrand();
        $user = Auth::user();
        $campaign = Campaign::where('brand_id', $brand->id)->findOrFail($id);

        if (in_array($campaign->status, ['completed', 'cancelled'])) {
            session()->flash('info', 'Campaign is already closed.');
            return back();
        }

        $unspent = (float) $campaign->remaining_budget;
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['currency' => 'USD']);

        if ($unspent > 0) {
            $wallet->increment('available_balance', $unspent);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'campaign_refund',
                'amount' => $unspent,
                'currency' => 'USD',
                'status' => 'completed',
                'description' => "Unspent budget refunded from campaign: {$campaign->title}",
                'reference_id' => 'REFUND_CMP_' . $campaign->id,
            ]);
        }

        $campaign->update([
            'status' => 'completed',
            'remaining_budget' => 0.00,
        ]);

        session()->flash('success', "Campaign concluded! Unspent budget of \$" . number_format($unspent, 2) . " has been refunded to your Brand Wallet.");
        return back();
    }

    /**
     * Delete Campaign Permanently & Refund Unspent Funds
     */
    public function deleteCampaign($id)
    {
        $brand = $this->getBrand();
        $user = Auth::user();
        $campaign = Campaign::where('brand_id', $brand->id)->findOrFail($id);

        $unspent = (float) $campaign->remaining_budget;
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['currency' => 'USD']);

        if ($unspent > 0) {
            $wallet->increment('available_balance', $unspent);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'campaign_refund',
                'amount' => $unspent,
                'currency' => 'USD',
                'status' => 'completed',
                'description' => "Unspent budget refunded upon deletion of campaign: {$campaign->title}",
                'reference_id' => 'DEL_REFUND_CMP_' . $campaign->id,
            ]);
        }

        // Clean associated records
        \App\Models\CampaignCountry::where('campaign_id', $campaign->id)->delete();
        ClipSubmission::where('campaign_id', $campaign->id)->delete();
        Payout::where('campaign_id', $campaign->id)->delete();

        $title = $campaign->title;
        $campaign->delete();

        session()->flash('success', "Campaign \"{$title}\" was successfully deleted." . ($unspent > 0 ? " Unspent budget of \$" . number_format($unspent, 2) . " has been refunded to your Brand Wallet." : ""));
        return redirect()->route('brand.campaigns');
    }

    /**
     * Manage Creator Clip Submissions
     */
    public function clips(Request $request)
    {
        $brand = $this->getBrand();
        $pageTitle = 'Creator Video Submissions';
        $status = $request->query('status');
        $search = $request->query('search');

        $campaignIds = Campaign::where('brand_id', $brand->id)->pluck('id');

        $query = ClipSubmission::with(['user.wallet', 'campaign'])
            ->whereIn('campaign_id', $campaignIds)
            ->latest();

        if ($status == 'pending') {
            $query->whereIn('status', ['pending', 'under_review']);
        } elseif ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('post_url', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('campaign', fn($c) => $c->where('title', 'like', "%{$search}%"));
            });
        }

        $submissions = $query->paginate(15)->withQueryString();

        return view('brand.clips.index', compact('pageTitle', 'submissions', 'status', 'search'));
    }

    /**
     * Approve Clip Submission
     */
    public function approveClip($id)
    {
        $brand = $this->getBrand();
        $clip = ClipSubmission::with(['campaign', 'user.wallet'])->findOrFail($id);

        if ($clip->campaign->brand_id !== $brand->id) {
            abort(403, 'Unauthorized.');
        }

        if (in_array($clip->status, ['approved_by_brand', 'verified', 'paid'])) {
            session()->flash('info', 'Clip is already approved.');
            return back();
        }

        if ($clip->campaign->isFixedPerVideo() && (float) $clip->campaign->reward_per_video > 0) {
            $reward = (float) $clip->campaign->reward_per_video;
        } else {
            $cpm = (float) ($clip->campaign->reward_rate_per_thousand > 0 ? $clip->campaign->reward_rate_per_thousand : 5.00);
            $reward = (float) ($clip->estimated_reward > 0 ? $clip->estimated_reward : $cpm);
        }

        if ($clip->campaign->remaining_budget >= $reward) {
            $clip->campaign->decrement('remaining_budget', $reward);
        }

        $clip->update([
            'status' => 'approved_by_brand',
            'verified_reward' => $reward,
            'verified_at' => now(),
        ]);

        $wallet = $clip->user?->wallet;
        $dest = $wallet 
            ? "Bank: {$wallet->payout_bank_name} | Acc: {$wallet->payout_account_number} ({$wallet->payout_account_holder}) | Phone: {$wallet->payout_phone_number}"
            : "Creator: " . ($clip->user->name ?? 'N/A');

        Payout::create([
            'submission_id' => $clip->id,
            'campaign_id' => $clip->campaign_id,
            'brand_id' => $brand->id,
            'clipper_id' => $clip->user_id,
            'gross_earning' => $reward,
            'commission_rate' => 0.00,
            'commission_amount' => 0.00,
            'net_payout' => $reward,
            'currency' => 'USD',
            'status' => 'PENDING_ADMIN_REVIEW',
            'payment_method' => 'bank_transfer',
            'destination_account' => $dest,
            'approved_by_brand_at' => now(),
        ]);

        session()->flash('success', "Clip approved! Bounty of \${$reward} has been deducted from your campaign budget and queued for direct bank transfer by Admin.");
        return back();
    }

    /**
     * Reject Clip Submission
     */
    public function rejectClip(Request $request, $id)
    {
        $brand = $this->getBrand();
        $clip = ClipSubmission::with('campaign')->findOrFail($id);

        if ($clip->campaign->brand_id !== $brand->id) {
            abort(403, 'Unauthorized.');
        }

        $reason = $request->input('reason', 'Submission does not follow brand voice or guidelines.');

        $clip->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        session()->flash('warning', 'Clip marked as rejected.');
        return back();
    }

    /**
     * Performance & Analytics
     */
    public function analytics()
    {
        $brand = $this->getBrand();
        $pageTitle = 'Campaign Performance & Creator Analytics';

        $campaigns = Campaign::where('brand_id', $brand->id)->with('submissions.user')->get();
        $campaignIds = $campaigns->pluck('id');

        $totalViews = (int) ClipSubmission::whereIn('campaign_id', $campaignIds)->sum('observed_views');
        $totalSubmissions = ClipSubmission::whereIn('campaign_id', $campaignIds)->count();
        $approvedSubmissions = ClipSubmission::whereIn('campaign_id', $campaignIds)->whereIn('status', ['approved_by_brand', 'verified', 'paid'])->count();

        $topCreators = User::whereHas('submissions', fn($q) => $q->whereIn('campaign_id', $campaignIds))
            ->withCount(['submissions as brand_views' => function($q) use ($campaignIds) {
                $q->whereIn('campaign_id', $campaignIds)->select(\DB::raw('COALESCE(SUM(observed_views), 0)'));
            }])
            ->orderByDesc('brand_views')
            ->take(8)
            ->get();

        return view('brand.analytics.index', compact('pageTitle', 'brand', 'campaigns', 'totalViews', 'totalSubmissions', 'approvedSubmissions', 'topCreators'));
    }

    /**
     * Finance, Budget & Billing
     */
    public function finance()
    {
        $brand = $this->getBrand();
        $user = Auth::user();
        $pageTitle = 'Brand Wallet & Transaction Ledger';

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['currency' => 'USD']);
        $transactions = WalletTransaction::where('wallet_id', $wallet->id)->latest()->paginate(15);

        $campaigns = Campaign::where('brand_id', $brand->id)->get();
        $totalBudgetAllocated = (float) $campaigns->sum('total_budget');
        $remainingBudget = (float) $campaigns->sum('remaining_budget');
        $spentOnCreators = max(0, $totalBudgetAllocated - $remainingBudget);

        return view('brand.finance.index', compact('pageTitle', 'brand', 'wallet', 'transactions', 'totalBudgetAllocated', 'remainingBudget', 'spentOnCreators'));
    }

    /**
     * Brand Profile & Settings
     */
    public function profile(Request $request)
    {
        $brand = $this->getBrand();
        $user = Auth::user();
        $pageTitle = 'Brand Profile & Organization Settings';

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'company_name' => 'required|string|max:150',
                'contact_email' => 'required|email|max:150',
                'website' => 'nullable|url|max:200',
                'industry' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'current_password' => 'nullable|string',
                'new_password' => 'nullable|string|min:8|confirmed',
            ]);

            $brandUpdateData = [
                'company_name' => $validated['company_name'],
                'website' => $validated['website'] ?? null,
                'industry' => $validated['industry'] ?? null,
                'description' => $validated['description'] ?? null,
            ];

            // Ensure contact_email column exists or self-heal dynamically
            if (!Schema::hasColumn('brands', 'contact_email')) {
                try {
                    Schema::table('brands', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->string('contact_email', 150)->nullable()->after('company_name');
                    });
                } catch (\Throwable $colEx) {
                    Log::warning('Self-healing contact_email column addition failed: ' . $colEx->getMessage());
                }
            }

            if (Schema::hasColumn('brands', 'contact_email')) {
                $brandUpdateData['contact_email'] = $validated['contact_email'];
            }

            $brand->update($brandUpdateData);

            $user->update(['name' => $validated['company_name']]);

            if (!empty($validated['new_password'])) {
                if (!Hash::check($validated['current_password'], $user->password)) {
                    session()->flash('error', 'Current password does not match records.');
                    return back();
                }
                $user->update(['password' => Hash::make($validated['new_password'])]);
            }

            session()->flash('success', 'Brand profile updated successfully.');
            return back();
        }

        return view('brand.profile.index', compact('pageTitle', 'brand', 'user'));
    }

    /**
     * Brand Password Redirection
     */
    public function password()
    {
        return redirect()->to(url('/brand/profile#password'));
    }

}
