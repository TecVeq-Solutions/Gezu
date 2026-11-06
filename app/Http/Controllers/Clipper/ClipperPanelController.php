<?php

namespace App\Http\Controllers\Clipper;

use App\Http\Controllers\Controller;
use App\Models\AuthSetting;
use App\Models\Campaign;
use App\Models\CampaignParticipation;
use App\Models\ClipSubmission;
use App\Models\CreatorProfile;
use App\Models\Payout;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use App\Services\ClipSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClipperPanelController extends Controller implements HasMiddleware
{
    /**
     * Assign Middleware ensuring Clipper or Impersonated Admin access.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = Auth::user();
                if (!$user) {
                    return redirect()->guest(route('login'));
                }
                if (!$user->isClipper() && !session()->has('impersonated_by')) {
                    if ($user->isAdmin()) {
                        return redirect()->to('/admin');
                    }
                    abort(403, 'Unauthorized. Creator/Clipper access required.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Get or create wallet for authenticated clipper.
     */
    protected function getWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['currency' => 'USD', 'available_balance' => 0.00]
        );
    }

    /**
     * Main Clipper Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $wallet = $this->getWallet($user);
        $pageTitle = 'Creator Command Center — Dashboard';

        $submissions = ClipSubmission::where('user_id', $user->id)->get();
        $totalSubmissions = $submissions->count();
        $pendingClips = $submissions->whereIn('status', ['pending', 'under_review'])->count();
        $approvedClips = $submissions->whereIn('status', ['approved_by_brand', 'approved_pending_payout', 'verified', 'paid'])->count();
        $rejectedClips = $submissions->where('status', 'rejected')->count();

        $totalViews = (int) $submissions->sum('observed_views');
        $earnedFromClips = (float) $submissions->whereIn('status', ['approved_by_brand', 'approved_pending_payout', 'verified', 'paid'])->sum('verified_reward');
        if ($earnedFromClips <= 0) {
            $earnedFromClips = (float) $submissions->whereIn('status', ['approved_by_brand', 'approved_pending_payout', 'verified', 'paid'])->sum('estimated_reward');
        }
        $totalEarnings = $earnedFromClips + (float) $wallet->total_withdrawn;

        // Pending bank payouts in pipeline
        $pendingPayoutsAmount = (float) Payout::where('clipper_id', $user->id)
            ->whereIn('status', ['PENDING_ADMIN_REVIEW', 'pending', 'processing'])
            ->sum('net_payout');

        // Available bounties count
        $activeBountiesCount = Campaign::whereIn('status', ['active', 'half_filled'])
            ->where('remaining_budget', '>', 0)
            ->count();

        $widget = [
            'total_earnings' => $totalEarnings,
            'available_balance' => (float) $wallet->available_balance,
            'pending_payouts' => $pendingPayoutsAmount,
            'total_views' => $totalViews,
            'approved_clips' => $approvedClips,
            'pending_clips' => $pendingClips,
            'total_submissions' => $totalSubmissions,
            'active_bounties' => $activeBountiesCount,
        ];

        // Recent 6 submissions
        $recentSubmissions = ClipSubmission::with(['campaign.brand'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(6)
            ->get();

        // Top open bounties for creator to clip
        $topBounties = Campaign::with('brand')
            ->whereIn('status', ['active', 'half_filled'])
            ->where('remaining_budget', '>', 0)
            ->orderBy('reward_rate_per_thousand', 'desc')
            ->take(4)
            ->get();

        // Chart Data (Observed views grouped by month/week)
        $chartViews = [];
        $chartLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            $chartLabels[] = $monthStart->format('M Y');
            $chartViews[] = (int) ClipSubmission::where('user_id', $user->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('observed_views');
        }

        // Payout bank status
        $hasBankSetup = !empty($wallet->payout_bank_name) && !empty($wallet->payout_account_number);

        return view('clipper.dashboard', compact(
            'pageTitle',
            'user',
            'wallet',
            'widget',
            'recentSubmissions',
            'topBounties',
            'chartLabels',
            'chartViews',
            'hasBankSetup'
        ));
    }

    /**
     * Marketplace / Available Bounties
     */
    public function bounties(Request $request)
    {
        $pageTitle = 'Campaign Bounties Marketplace';
        $user = Auth::user();

        $query = Campaign::with(['brand', 'countries'])
            ->whereIn('status', ['active', 'half_filled'])
            ->where('remaining_budget', '>', 0);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('brand', function ($bq) use ($search) {
                      $bq->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $query->where('category', $request->input('category'));
        }

        if ($request->input('sort') === 'cpm_desc') {
            $query->orderBy('reward_rate_per_thousand', 'desc');
        } elseif ($request->input('sort') === 'budget_desc') {
            $query->orderBy('remaining_budget', 'desc');
        } else {
            $query->latest();
        }

        $bounties = $query->paginate(12)->withQueryString();

        $categories = Campaign::whereIn('status', ['active', 'half_filled'])
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('clipper.bounties.index', compact('pageTitle', 'bounties', 'categories'));
    }

    /**
     * Bounty Detail & Briefing
     */
    public function bountyDetail($id)
    {
        $campaign = Campaign::with(['brand', 'platforms', 'countries'])->findOrFail($id);

        if (!in_array($campaign->status, ['active', 'half_filled'])) {
            $notify[] = ['error', 'This campaign is currently not active or awaiting brand bounty funding.'];
            return redirect()->route('clipper.bounties')->withNotify($notify);
        }

        $pageTitle = 'Bounty Briefing — ' . $campaign->title;
        $user = Auth::user();

        // Check if creator has already submitted to this campaign
        $userSubmissions = ClipSubmission::where('user_id', $user->id)
            ->where('campaign_id', $campaign->id)
            ->latest()
            ->get();

        return view('clipper.bounties.show', compact('pageTitle', 'campaign', 'userSubmissions'));
    }

    /**
     * Creator Submissions Tracker
     */
    public function submissions(Request $request)
    {
        $pageTitle = 'My Clip Submissions & Verifications';
        $user = Auth::user();

        $query = ClipSubmission::with(['campaign.brand'])
            ->where('user_id', $user->id);

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->input('status');
            if ($status === 'approved') {
                $query->whereIn('status', ['approved_by_brand', 'approved_pending_payout', 'verified', 'paid']);
            } elseif ($status === 'pending') {
                $query->whereIn('status', ['pending', 'under_review']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('post_url', 'like', "%{$search}%")
                  ->orWhereHas('campaign', function ($cq) use ($search) {
                      $cq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $submissions = $query->latest()->paginate(15)->withQueryString();

        $activeCampaigns = Campaign::whereIn('status', ['active', 'half_filled'])
            ->where('remaining_budget', '>', 0)
            ->orderBy('title')
            ->get(['id', 'title', 'reward_rate_per_thousand']);

        return view('clipper.submissions.index', compact('pageTitle', 'submissions', 'activeCampaigns'));
    }

    /**
     * Submit New Clip Form
     */
    public function createSubmission(Request $request)
    {
        $pageTitle = 'Submit New Video Clip';
        $user = Auth::user();

        $campaigns = Campaign::whereIn('status', ['active', 'half_filled'])
            ->where('remaining_budget', '>', 0)
            ->orderBy('title')
            ->get();

        $preselectedCampaignId = $request->query('campaign_id');
        $requireSocial = \App\Models\AuthSetting::get('require_social_account_for_clips', '0') === '1';
        $connectedAccounts = SocialAccount::where('user_id', $user->id)->get()->keyBy(function($item) {
            return strtolower(explode(' ', $item->platform)[0]);
        });

        return view('clipper.submissions.create', compact('pageTitle', 'campaigns', 'preselectedCampaignId', 'requireSocial', 'connectedAccounts'));
    }

    /**
     * Process & Store Clip Submission
     */
    public function storeSubmission(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'platform' => 'required|string|in:TikTok,YouTube Shorts,Instagram Reels,X / Twitter,Facebook Reels',
            'post_url' => 'required|url',
            'channel_handle' => 'nullable|string|max:100',
        ]);

        $campaign = Campaign::with(['brand', 'platforms'])->findOrFail($validated['campaign_id']);

        if (!in_array($campaign->status, ['active', 'half_filled'])) {
            return back()->withErrors(['campaign_id' => 'This campaign is no longer active.'])->withInput();
        }

        if ($campaign->remaining_budget <= 0) {
            return back()->withErrors(['campaign_id' => 'This campaign has exhausted its reward budget.'])->withInput();
        }

        // Validate URL format for platform
        $urlLower = strtolower($validated['post_url']);
        $plat = strtolower($validated['platform']);
        $isValidUrl = true;

        if (str_contains($plat, 'tiktok') && !str_contains($urlLower, 'tiktok.com')) {
            $isValidUrl = false;
        } elseif (str_contains($plat, 'youtube') && !str_contains($urlLower, 'youtube.com') && !str_contains($urlLower, 'youtu.be')) {
            $isValidUrl = false;
        } elseif (str_contains($plat, 'instagram') && !str_contains($urlLower, 'instagram.com')) {
            $isValidUrl = false;
        }

        if (!$isValidUrl) {
            return back()->withErrors(['post_url' => "The link provided does not appear to be a valid {$validated['platform']} URL."])->withInput();
        }

        // Duplicate URL check
        if (ClipSubmission::where('post_url', $validated['post_url'])->exists()) {
            return back()->withErrors(['post_url' => 'This exact clip URL has already been submitted to TECVEQ.'])->withInput();
        }

        // Enforce Connected Social Account if required by Admin
        $requireSocial = \App\Models\AuthSetting::get('require_social_account_for_clips', '0') === '1';
        $platKeyword = strtolower(explode(' ', $validated['platform'])[0]);
        $connectedAccount = SocialAccount::where('user_id', $user->id)
            ->where(function($q) use ($validated, $platKeyword) {
                $q->where('platform', $validated['platform'])
                  ->orWhere('platform', 'like', '%' . $platKeyword . '%');
            })
            ->first();

        if ($requireSocial && (!$connectedAccount || empty($connectedAccount->handle))) {
            return back()->withErrors([
                'platform' => "Admin requires you to connect your verified {$validated['platform']} channel in your Creator Profile before submitting clips."
            ])->withInput();
        }

        if (!$connectedAccount) {
            $userHandle = $validated['channel_handle'] ?? ('@' . Str::slug($user->name));
            $cleanUser = ltrim($userHandle, '@');
            $connectedAccount = SocialAccount::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'platform' => $validated['platform'],
                ],
                [
                    'username' => $cleanUser ?: ('creator_' . $user->id),
                    'handle' => $userHandle,
                    'display_name' => $user->name,
                    'account_name' => $user->name,
                    'is_verified' => true,
                    'verified' => true,
                    'status' => 'connected',
                    'connected_at' => now(),
                ]
            );
        }
        $socialAccount = $connectedAccount;

        // Compute estimated reward from CPM
        $cpm = (float) ($campaign->reward_rate_per_thousand > 0 ? $campaign->reward_rate_per_thousand : 5.00);
        $observedViews = 1500; // Initial seed views observed
        $estimatedReward = round(($observedViews / 1000) * $cpm, 2);

        // Record participation
        CampaignParticipation::firstOrCreate(
            ['campaign_id' => $campaign->id, 'user_id' => $user->id],
            ['status' => 'submitted', 'joined_at' => now()]
        );

        // Create submission
        $clip = ClipSubmission::create([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'social_account_id' => $socialAccount->id,
            'platform' => $validated['platform'],
            'post_url' => $validated['post_url'],
            'status' => 'under_review',
            'observed_views' => $observedViews,
            'observed_likes' => 0,
            'observed_comments' => 0,
            'observed_shares' => 0,
            'eligible_views' => $observedViews,
            'estimated_reward' => $estimatedReward,
            'verified_reward' => 0.00,
        ]);

        return redirect()->route('clipper.submissions')->with('success', "Clip submitted successfully! The brand will review your video. Once approved, Admin will transfer your payout of \${$estimatedReward} directly to your bank account.");
    }

    /**
     * Wallet & Direct Bank Payout Ledger
     */
    public function wallet()
    {
        $pageTitle = 'Creator Wallet & Bank Payouts';
        $user = Auth::user();
        $wallet = $this->getWallet($user);

        // Total earnings
        $approvedSubmissions = ClipSubmission::where('user_id', $user->id)
            ->whereIn('status', ['approved_by_brand', 'approved_pending_payout', 'verified', 'paid'])
            ->get();

        $totalEarned = (float) $approvedSubmissions->sum('verified_reward');
        if ($totalEarned <= 0) {
            $totalEarned = (float) $approvedSubmissions->sum('estimated_reward');
        }

        // Itemized payouts
        $payouts = Payout::with(['campaign.brand'])
            ->where('clipper_id', $user->id)
            ->latest()
            ->paginate(15);

        // Wallet transactions
        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->latest()
            ->take(15)
            ->get();

        $hasBankSetup = !empty($wallet->payout_bank_name) && !empty($wallet->payout_account_number);

        return view('clipper.wallet.index', compact(
            'pageTitle',
            'user',
            'wallet',
            'totalEarned',
            'payouts',
            'transactions',
            'hasBankSetup'
        ));
    }

    /**
     * Request Manual Withdrawal to Configured Bank
     */
    public function requestWithdrawal(Request $request)
    {
        $user = Auth::user();
        $wallet = $this->getWallet($user);

        if (empty($wallet->payout_bank_name) || empty($wallet->payout_account_number)) {
            return back()->withErrors(['amount' => 'Please set up your direct bank account details in Payout Settings before requesting a withdrawal.']);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:5',
        ]);

        $amount = (float) $validated['amount'];

        if ($wallet->available_balance < $amount) {
            return back()->withErrors(['amount' => 'Requested amount exceeds your available balance ($' . number_format($wallet->available_balance, 2) . ').']);
        }

        $destination = "Bank: {$wallet->payout_bank_name} | Acc: {$wallet->payout_account_number} ({$wallet->payout_account_holder}) | Tel: {$wallet->payout_phone_number}";

        // Deduct from wallet balance
        $wallet->decrement('available_balance', $amount);
        $wallet->increment('total_withdrawn', $amount);

        // Log transaction
        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'withdrawal',
            'amount' => -$amount,
            'currency' => 'USD',
            'status' => 'completed',
            'description' => "Withdrawal requested to direct bank account ({$wallet->payout_bank_name})",
            'reference_id' => 'WDR_REQ_' . time(),
        ]);

        // Record Withdrawal
        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => 'USD',
            'method' => 'bank_transfer',
            'destination_reference' => $destination,
            'status' => 'processing',
        ]);

        // Queue direct Payout for admin review
        Payout::create([
            'clipper_id' => $user->id,
            'gross_earning' => $amount,
            'commission_rate' => 0.00,
            'commission_amount' => 0.00,
            'net_payout' => $amount,
            'currency' => 'USD',
            'status' => 'PENDING_ADMIN_REVIEW',
            'payment_method' => 'bank_transfer',
            'destination_account' => $destination,
            'approved_by_brand_at' => now(),
        ]);

        return back()->with('success', 'Withdrawal of $' . number_format($amount, 2) . ' submitted! Admin will transfer funds directly to your bank account.');
    }

    /**
     * Direct Bank Payout Settings (No Stripe Connect - Strictly Direct Bank)
     */
    public function payoutSettings(Request $request)
    {
        $pageTitle = 'Direct Bank Payout Settings';
        $user = Auth::user();
        $wallet = $this->getWallet($user);

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'payout_bank_name' => 'required|string|max:150',
                'payout_account_holder' => 'required|string|max:150',
                'payout_account_number' => 'required|string|max:100',
                'payout_phone_number' => 'required|string|max:50',
                'payout_routing_swift' => 'nullable|string|max:50',
            ]);

            $wallet->update([
                'payout_bank_name' => $validated['payout_bank_name'],
                'payout_account_holder' => $validated['payout_account_holder'],
                'payout_account_number' => $validated['payout_account_number'],
                'payout_phone_number' => $validated['payout_phone_number'],
                'payout_routing_swift' => $validated['payout_routing_swift'] ?? null,
            ]);

            return back()->with('success', 'Direct bank payout details updated successfully! All your approved earnings will be transferred directly to this account by the Admin.');
        }

        return view('clipper.payout_settings.index', compact('pageTitle', 'user', 'wallet'));
    }

    /**
     * Creator Profile & Social Channels
     */
    public function profile(Request $request)
    {
        $pageTitle = 'Creator Profile & Channels';
        $user = Auth::user();
        $creatorProfile = CreatorProfile::firstOrCreate(['user_id' => $user->id]);
        $socialAccounts = SocialAccount::where('user_id', $user->id)->get();

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'name' => 'required|string|max:150',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'bio' => 'nullable|string|max:1000',
                'tiktok_handle' => 'nullable|string|max:100',
                'youtube_handle' => 'nullable|string|max:100',
                'instagram_handle' => 'nullable|string|max:100',
                'password' => 'nullable|min:8|confirmed',
            ]);

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            $creatorProfile->update([
                'bio' => $validated['bio'] ?? $creatorProfile->bio,
            ]);

            // Save Social Channels
            if (!empty($validated['tiktok_handle'])) {
                $h = $validated['tiktok_handle'];
                SocialAccount::updateOrCreate(
                    ['user_id' => $user->id, 'platform' => 'TikTok'],
                    [
                        'username' => ltrim($h, '@'),
                        'handle' => '@' . ltrim($h, '@'),
                        'display_name' => $user->name,
                        'is_verified' => true,
                        'verified' => true,
                        'status' => 'connected',
                        'connected_at' => now(),
                    ]
                );
            }
            if (!empty($validated['youtube_handle'])) {
                $h = $validated['youtube_handle'];
                SocialAccount::updateOrCreate(
                    ['user_id' => $user->id, 'platform' => 'YouTube Shorts'],
                    [
                        'username' => ltrim($h, '@'),
                        'handle' => '@' . ltrim($h, '@'),
                        'display_name' => $user->name,
                        'is_verified' => true,
                        'verified' => true,
                        'status' => 'connected',
                        'connected_at' => now(),
                    ]
                );
            }
            if (!empty($validated['instagram_handle'])) {
                $h = $validated['instagram_handle'];
                SocialAccount::updateOrCreate(
                    ['user_id' => $user->id, 'platform' => 'Instagram Reels'],
                    [
                        'username' => ltrim($h, '@'),
                        'handle' => '@' . ltrim($h, '@'),
                        'display_name' => $user->name,
                        'is_verified' => true,
                        'verified' => true,
                        'status' => 'connected',
                        'connected_at' => now(),
                    ]
                );
            }

            return back()->with('success', 'Profile and social channels updated successfully!');
        }

        return view('clipper.profile.index', compact('pageTitle', 'user', 'creatorProfile', 'socialAccounts'));
    }

    /**
     * Creator Password Redirection
     */
    public function password()
    {
        return redirect()->to(url('/clipper/profile#password'));
    }

    /**
     * Creator Social Channels Hub (Stackposts style with Admin Gatekeeper)
     */
    public function channels(Request $request)
    {
        $pageTitle = 'Connected Social Channels & Verification';
        $user = Auth::user();

        $platformsConfig = [
            'tiktok' => ['name' => 'TikTok', 'icon' => 'fab fa-tiktok', 'color' => '#000000', 'desc' => 'Short-form vertical video clips'],
            'instagram' => ['name' => 'Instagram Reels', 'icon' => 'fab fa-instagram', 'color' => '#E1306C', 'desc' => 'Instagram Reels & Stories'],
            'youtube' => ['name' => 'YouTube Shorts', 'icon' => 'fab fa-youtube', 'color' => '#FF0000', 'desc' => 'YouTube Shorts & Channel Videos'],
            'facebook' => ['name' => 'Facebook Reels', 'icon' => 'fab fa-facebook', 'color' => '#1877F2', 'desc' => 'Facebook Reels & Pages'],
            'x' => ['name' => 'X (Twitter)', 'icon' => 'fab fa-x-twitter', 'color' => '#000000', 'desc' => 'X Video Posts & Clips'],
            'linkedin' => ['name' => 'LinkedIn', 'icon' => 'fab fa-linkedin', 'color' => '#0A66C2', 'desc' => 'Creator Video Posts'],
            'pinterest' => ['name' => 'Pinterest', 'icon' => 'fab fa-pinterest', 'color' => '#BD081C', 'desc' => 'Idea Pins & Videos'],
            'threads' => ['name' => 'Threads', 'icon' => 'fab fa-threads', 'color' => '#000000', 'desc' => 'Threads Short-Form Media'],
        ];

        // Check which platforms are enabled by Admin in AuthSetting
        foreach ($platformsConfig as $key => &$config) {
            $config['is_enabled_by_admin'] = AuthSetting::get("{$key}_enabled", '1') === '1';
            $config['account'] = SocialAccount::where('user_id', $user->id)
                ->where(function($q) use ($config, $key) {
                    $q->where('platform', 'like', "%{$config['name']}%")
                      ->orWhere('platform', 'like', "%{$key}%");
                })->first();
        }

        if ($request->isMethod('post')) {
            $platformKey = $request->input('platform_key');
            $handle = $request->input('handle');
            $action = $request->input('action'); // 'connect' or 'disconnect'

            if (!isset($platformsConfig[$platformKey])) {
                return back()->with('error', 'Invalid platform specified.');
            }

            if (!$platformsConfig[$platformKey]['is_enabled_by_admin']) {
                return back()->with('error', 'This platform is currently disabled by the Platform Administrator.');
            }

            if ($action === 'disconnect') {
                SocialAccount::where('user_id', $user->id)
                    ->where(function($q) use ($platformsConfig, $platformKey) {
                        $q->where('platform', 'like', "%{$platformsConfig[$platformKey]['name']}%")
                          ->orWhere('platform', 'like', "%{$platformKey}%");
                    })->delete();

                return back()->with('success', "{$platformsConfig[$platformKey]['name']} account disconnected.");
            }

            if (empty($handle)) {
                return back()->with('error', 'Please provide your account handle or profile URL.');
            }

            SocialAccount::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'platform' => $platformsConfig[$platformKey]['name'],
                ],
                [
                    'username' => '@' . ltrim($handle, '@'),
                    'handle' => '@' . ltrim($handle, '@'),
                    'display_name' => $handle,
                    'account_name' => $handle,
                    'verified' => true,
                    'is_verified' => true,
                    'connected_at' => now(),
                ]
            );

            return back()->with('success', "{$platformsConfig[$platformKey]['name']} connected and verified successfully!");
        }

        return view('clipper.channels.index', compact('pageTitle', 'platformsConfig', 'user'));
    }
}
