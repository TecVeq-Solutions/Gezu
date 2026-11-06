<?php

namespace App\Http\Controllers\Streamer;

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
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StreamerPanelController extends Controller implements HasMiddleware
{
    /**
     * Assign Middleware ensuring Streamer or Impersonated Admin access.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = Auth::user();
                if (!$user) {
                    return redirect()->guest(route('login'));
                }
                if (!$user->isStreamer() && !session()->has('impersonated_by')) {
                    if ($user->isAdmin()) {
                        return redirect()->to('/admin');
                    }
                    abort(403, 'Unauthorized. Streamer access required.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Helper to extract YouTube video ID from various YouTube URL formats.
     */
    public static function extractYouTubeId(string $url): ?string
    {
        $pattern = '%^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com/(?:embed/|v/|shorts/|live/|watch\?v=|watch\?.+&v=))([\w-]{11})(?:\S+)?$%x';
        if (preg_match($pattern, trim($url), $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Get or create wallet for authenticated streamer.
     */
    protected function getWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['currency' => 'USD', 'available_balance' => 0.00]
        );
    }

    /**
     * Main Streamer Command Center Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $wallet = $this->getWallet($user);
        $pageTitle = 'Streamer Command Center — YouTube Promotion Hub';

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

        // Available brand sponsorship campaigns
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

        // Recent 6 YouTube submissions
        $recentSubmissions = ClipSubmission::with(['campaign.brand'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(6)
            ->get();

        // Top open sponsorship deals for streamer to promote
        $topBounties = Campaign::with('brand')
            ->whereIn('status', ['active', 'half_filled'])
            ->where('remaining_budget', '>', 0)
            ->orderBy('reward_rate_per_thousand', 'desc')
            ->take(4)
            ->get();

        // Chart Data (Observed YouTube views reach over last 6 months)
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

        return view('streamer.dashboard', compact(
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
     * Browse Active Brand Campaigns for Streamer Promotion
     */
    public function campaigns(Request $request)
    {
        $pageTitle = 'Brand Sponsorship Deals Marketplace';
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

        $campaigns = $query->paginate(12)->withQueryString();

        $categories = Campaign::whereIn('status', ['active', 'half_filled'])
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('streamer.campaigns.index', compact('pageTitle', 'campaigns', 'categories'));
    }

    /**
     * Campaign Briefing & Sponsorship Requirements
     */
    public function campaignDetail($id)
    {
        $campaign = Campaign::with(['brand', 'platforms', 'countries'])->findOrFail($id);

        if (!in_array($campaign->status, ['active', 'half_filled'])) {
            session()->flash('error', 'This sponsorship campaign is currently not active.');
            return redirect()->route('streamer.campaigns');
        }

        $pageTitle = 'Sponsorship Briefing — ' . $campaign->title;
        $user = Auth::user();

        // Check if streamer has already submitted to this campaign
        $userSubmissions = ClipSubmission::where('user_id', $user->id)
            ->where('campaign_id', $campaign->id)
            ->latest()
            ->get();

        return view('streamer.campaigns.show', compact('pageTitle', 'campaign', 'userSubmissions'));
    }

    /**
     * Streamer Submissions History & Status Tracker
     */
    public function submissions(Request $request)
    {
        $pageTitle = 'My YouTube Video Submissions & Payout Status';
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

        $activeCampaigns = Campaign::where('status', 'active')
            ->where('remaining_budget', '>', 0)
            ->orderBy('title')
            ->get(['id', 'title', 'reward_rate_per_thousand']);

        return view('streamer.submissions.index', compact('pageTitle', 'submissions', 'activeCampaigns'));
    }

    /**
     * Form to Submit YouTube Video Promotion Link
     */
    public function createSubmission(Request $request)
    {
        $pageTitle = 'Submit YouTube Video Promotion';
        $user = Auth::user();

        $campaigns = Campaign::whereIn('status', ['active', 'half_filled'])
            ->where('remaining_budget', '>', 0)
            ->orderBy('title')
            ->get();

        $selectedCampaignId = $request->query('campaign_id');
        $selectedCampaign = $selectedCampaignId ? Campaign::find($selectedCampaignId) : null;

        $youtubeAccount = SocialAccount::where('user_id', $user->id)
            ->where('platform', 'youtube')
            ->first();

        return view('streamer.submissions.create', compact('pageTitle', 'campaigns', 'selectedCampaign', 'youtubeAccount'));
    }

    /**
     * Process YouTube Video Submission
     */
    public function storeSubmission(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'post_url' => 'required|url',
            'channel_name' => 'nullable|string|max:255',
            'channel_handle' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $campaign = Campaign::findOrFail($validated['campaign_id']);

        if (!in_array($campaign->status, ['active', 'half_filled'])) {
            return back()->withErrors(['campaign_id' => 'This campaign is no longer accepting new submissions.'])->withInput();
        }

        if ($campaign->remaining_budget <= 0) {
            return back()->withErrors(['campaign_id' => 'This campaign has reached its total budget capacity.'])->withInput();
        }

        // Validate YouTube URL specifically
        $youtubeId = self::extractYouTubeId($validated['post_url']);
        if (!$youtubeId) {
            return back()->withErrors([
                'post_url' => 'Please provide a valid YouTube video or shorts URL (e.g., https://www.youtube.com/watch?v=... or https://youtu.be/...).'
            ])->withInput();
        }

        // Duplicate URL check
        if (ClipSubmission::where('post_url', $validated['post_url'])->exists()) {
            return back()->withErrors(['post_url' => 'This exact YouTube video link has already been submitted to GEZUX.'])->withInput();
        }

        // Manage linked YouTube social account
        $channelUsername = ltrim($validated['channel_handle'] ?? ('@' . Str::slug($user->name)), '@');
        if (empty($channelUsername)) {
            $channelUsername = Str::slug($user->name);
        }
        $connectedAccount = SocialAccount::firstOrCreate(
            [
                'user_id' => $user->id,
                'platform' => 'youtube',
            ],
            [
                'username' => $channelUsername,
                'handle' => '@' . $channelUsername,
                'account_name' => $validated['channel_name'] ?? ($user->name . ' YouTube'),
                'display_name' => $validated['channel_name'] ?? ($user->name . ' YouTube'),
                'is_verified' => true,
                'verified' => true,
                'status' => 'connected',
            ]
        );

        // Compute estimated reward from CPM
        $cpm = (float) ($campaign->reward_rate_per_thousand > 0 ? $campaign->reward_rate_per_thousand : 5.00);
        $observedViews = 1200; // Initial seed views for YouTube promotion
        $estimatedReward = round(($observedViews / 1000) * $cpm, 2);

        // Record participation
        CampaignParticipation::firstOrCreate(
            ['campaign_id' => $campaign->id, 'user_id' => $user->id],
            ['status' => 'submitted', 'joined_at' => now()]
        );

        // Create submission
        $submission = ClipSubmission::create([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'social_account_id' => $connectedAccount->id,
            'platform' => 'youtube',
            'post_url' => $validated['post_url'],
            'external_post_id' => $youtubeId,
            'status' => 'pending',
            'observed_views' => $observedViews,
            'observed_likes' => 0,
            'observed_comments' => 0,
            'observed_shares' => 0,
            'eligible_views' => $observedViews,
            'estimated_reward' => $estimatedReward,
            'verified_reward' => 0.00,
            'fraud_risk_score' => 5,
        ]);

        session()->flash('success', "YouTube promotion submitted successfully! Your video (ID: {$youtubeId}) is now under review by the brand team.");
        return redirect()->route('streamer.submissions');
    }

    /**
     * Streamer Wallet & Earnings
     */
    public function wallet()
    {
        $user = Auth::user();
        $wallet = $this->getWallet($user);
        $pageTitle = 'Streamer Wallet & Payouts';

        $withdrawals = Withdrawal::where('user_id', $user->id)->latest()->paginate(10);
        $payouts = Payout::with(['campaign.brand', 'submission'])
            ->where('clipper_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('streamer.wallet.index', compact('pageTitle', 'user', 'wallet', 'withdrawals', 'payouts'));
    }

    /**
     * Request Direct Bank Withdrawal
     */
    public function requestWithdrawal(Request $request)
    {
        $user = Auth::user();
        $wallet = $this->getWallet($user);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:20',
        ]);

        $amount = (float) $validated['amount'];

        if ($amount > (float) $wallet->available_balance) {
            return back()->withErrors(['amount' => 'Insufficient available balance. You have $' . number_format($wallet->available_balance, 2) . ' available.']);
        }

        if (empty($wallet->payout_bank_name) || empty($wallet->payout_account_number)) {
            session()->flash('error', 'Please configure your Direct Bank Transfer details before requesting a payout.');
            return redirect()->route('streamer.payout-settings');
        }

        // Deduct from available balance & transfer to locked/pending
        $wallet->decrement('available_balance', $amount);
        $wallet->increment('pending_balance', $amount);

        $destination = "Bank: {$wallet->payout_bank_name} | Acc: {$wallet->payout_account_number} ({$wallet->payout_account_holder}) | Phone: {$wallet->payout_phone_number}";

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => 'USD',
            'method' => 'Direct Bank Transfer',
            'destination_reference' => $destination,
            'status' => 'pending',
        ]);

        session()->flash('success', 'Withdrawal request of $' . number_format($amount, 2) . ' submitted! Admin review in progress.');
        return redirect()->route('streamer.wallet');
    }

    /**
     * Direct Bank Transfer Payout Settings
     */
    public function payoutSettings(Request $request)
    {
        $user = Auth::user();
        $wallet = $this->getWallet($user);
        $pageTitle = 'Direct Bank Transfer Payout Settings';

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'payout_bank_name' => 'required|string|max:150',
                'payout_account_number' => 'required|string|max:100',
                'payout_account_holder' => 'required|string|max:150',
                'payout_phone_number' => 'nullable|string|max:50',
            ]);

            $wallet->update($validated);

            session()->flash('success', 'Direct Bank Transfer details updated successfully.');
            return redirect()->route('streamer.payout-settings');
        }

        return view('streamer.payout_settings.index', compact('pageTitle', 'user', 'wallet'));
    }

    /**
     * Connected YouTube Channel Information
     */
    public function channels(Request $request)
    {
        $user = Auth::user();
        $pageTitle = 'Connected YouTube Channel & Socials';

        $youtubeAccount = SocialAccount::where('user_id', $user->id)
            ->where('platform', 'youtube')
            ->first();

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'channel_name' => 'required|string|max:200',
                'channel_handle' => 'required|string|max:100',
                'channel_url' => 'nullable|url',
                'subscribers_count' => 'nullable|integer|min:0',
            ]);

            $channelUsername = ltrim($validated['channel_handle'], '@');
            if (empty($channelUsername)) {
                $channelUsername = Str::slug($user->name);
            }
            SocialAccount::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'platform' => 'youtube',
                ],
                [
                    'username' => $channelUsername,
                    'handle' => '@' . $channelUsername,
                    'account_name' => $validated['channel_name'],
                    'display_name' => $validated['channel_name'],
                    'is_verified' => true,
                    'verified' => true,
                    'status' => 'connected',
                ]
            );

            session()->flash('success', 'YouTube Channel credentials linked successfully!');
            return redirect()->route('streamer.channels');
        }

        return view('streamer.channels.index', compact('pageTitle', 'user', 'youtubeAccount'));
    }

    /**
     * Streamer Profile Management
     */
    public function profile(Request $request)
    {
        $user = Auth::user();
        $profile = CreatorProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['display_name' => $user->name, 'username' => Str::slug($user->name) . rand(100, 999)]
        );
        $pageTitle = 'Streamer Profile Settings';

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'display_name' => 'required|string|max:255',
                'bio' => 'nullable|string|max:1000',
                'country' => 'nullable|string|max:5',
            ]);

            $user->update(['name' => $validated['name']]);
            $profile->update([
                'display_name' => $validated['display_name'],
                'bio' => $validated['bio'] ?? null,
                'country' => $validated['country'] ?? 'US',
            ]);

            session()->flash('success', 'Streamer profile updated successfully.');
            return redirect()->route('streamer.profile');
        }

        return view('streamer.profile.index', compact('pageTitle', 'user', 'profile'));
    }

    /**
     * Password Management
     */
    public function password(Request $request)
    {
        $user = Auth::user();
        $pageTitle = 'Change Password';

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'The provided current password does not match.']);
            }

            $user->update(['password' => Hash::make($validated['password'])]);

            session()->flash('success', 'Password updated successfully.');
            return redirect()->route('streamer.password');
        }

        return view('streamer.profile.password', compact('pageTitle', 'user'));
    }
}
