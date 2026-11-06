<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Brand;
use App\Models\CreatorProfile;
use App\Models\Campaign;
use App\Models\ClipSubmission;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use App\Models\Payout;

class WebController extends Controller
{
    /**
     * Home Landing Page
     */
    public function home()
    {
        $campaigns = Campaign::with('brand')
            ->whereIn('status', ['active', 'half_filled'])
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        $stats = [
            'totalPaidOut' => '$420,890',
            'verifiedViews' => '142.8M',
            'activeBounties' => \Illuminate\Support\Facades\Cache::remember('home_active_bounties', 120, fn() => Campaign::whereIn('status', ['active', 'half_filled'])->count()) ?: 18,
            'activeCreators' => \Illuminate\Support\Facades\Cache::remember('home_active_creators', 120, fn() => User::where('role', 'clipper')->count()) ?: 1240,
        ];

        return view('pages.home', compact('campaigns', 'stats'));
    }

    /**
     * Campaign Marketplace Catalog
     */
    public function marketplace()
    {
        $campaigns = Campaign::with('brand')
            ->whereIn('status', ['active', 'half_filled'])
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.marketplace', compact('campaigns'));
    }

    /**
     * Single Campaign Details
     */
    public function campaignDetail($id)
    {
        $campaign = Campaign::with(['brand', 'countries'])->findOrFail($id);

        if (!in_array($campaign->status, ['active', 'half_filled', 'completed'])) {
            $canPreview = false;
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->isAdmin() || ($campaign->brand && $campaign->brand->user_id == $user->id) || session()->has('impersonated_by')) {
                    $canPreview = true;
                }
            }
            if (!$canPreview) {
                abort(404, 'This campaign is currently in draft status and awaiting bounty deposit.');
            }
        }

        return view('pages.campaign-detail', compact('campaign'));
    }

    /**
     * Privacy Policy Page
     */
    public function privacy()
    {
        $page = \App\Models\CmsPage::where('slug', 'privacy-policy')->where('status', 'published')->first();
        if ($page) {
            $pageTitle = $page->meta_title ?: $page->title;
            return view('frontend.pages.show', compact('page', 'pageTitle'));
        }

        $title = \App\Models\AuthSetting::get('privacy_policy_title', 'GEZUX Privacy Policy');
        $content = \App\Models\AuthSetting::get('privacy_policy_content', "Welcome to GEZUX. We prioritize the security of your data.\n\nWe collect account information and bank details for manual creator payouts. We never sell your personal data.");
        $updatedAt = \App\Models\AuthSetting::get('privacy_policy_updated_at', date('Y-m-d'));

        return view('pages.legal.privacy', compact('title', 'content', 'updatedAt'));
    }

    /**
     * Terms of Service Page
     */
    public function terms()
    {
        $page = \App\Models\CmsPage::where('slug', 'terms-of-service')->where('status', 'published')->first();
        if ($page) {
            $pageTitle = $page->meta_title ?: $page->title;
            return view('frontend.pages.show', compact('page', 'pageTitle'));
        }

        $title = \App\Models\AuthSetting::get('terms_of_service_title', 'GEZUX Terms of Service');
        $content = \App\Models\AuthSetting::get('terms_of_service_content', "By using GEZUX, you agree to fair-use video clipping and verified view payouts.\n\nBrands fund campaigns via Stripe. Clippers receive direct bank transfers upon clip verification.");
        $updatedAt = \App\Models\AuthSetting::get('terms_of_service_updated_at', date('Y-m-d'));

        return view('pages.legal.terms', compact('title', 'content', 'updatedAt'));
    }

    /**
     * Auth Login View
     */
    public function loginView()
    {
        if (Auth::check()) {
            return redirect(Auth::user()->getRedirectUrl());
        }
        return view('pages.auth.login');
    }

    /**
     * Auth Login Action
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Clean stale intended URL to prevent cross-portal redirect issues
            session()->forget('url.intended');

            if ($user->isAdmin()) {
                return redirect()->to('/admin');
            }
            if ($user->isBrand()) {
                return redirect()->to('/brand');
            }
            if ($user->isStreamer()) {
                return redirect()->to('/streamer');
            }
            if ($user->isClipper()) {
                return redirect()->to('/clipper');
            }
            return redirect()->to($user->getRedirectUrl());
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Auth Register View
     */
    public function registerView()
    {
        if (Auth::check()) {
            return redirect(Auth::user()->getRedirectUrl());
        }
        return view('pages.auth.register');
    }

    /**
     * Auth Register Action
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:clipper,brand,streamer',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Wallet
        Wallet::create([
            'user_id' => $user->id,
            'currency' => 'USD',
            'available_balance' => 0.00,
            'pending_balance' => 0.00,
            'locked_balance' => 0.00,
        ]);

        if ($user->role === 'clipper' || $user->role === 'streamer') {
            CreatorProfile::create([
                'user_id' => $user->id,
                'display_name' => $user->name,
                'username' => Str::slug($user->name) . rand(100, 999),
            ]);
        } elseif ($user->role === 'brand') {
            Brand::create([
                'user_id' => $user->id,
                'company_name' => $user->name,
                'company_slug' => Str::slug($user->name) . rand(100, 999),
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        session()->flash('success', 'Account created successfully! Welcome to GEZUX.');
        return redirect($user->getRedirectUrl());
    }

    /**
     * Logout Action
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Clipper Studio Dashboard
     */
    public function clipperDashboard()
    {
        $user = Auth::user();

        if ($user->role === 'brand') {
            return redirect()->route('web.brand.dashboard');
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['currency' => 'USD', 'available_balance' => 0.00]
        );

        $submissions = ClipSubmission::with(['campaign.brand'])
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        $totalViews = (int) $submissions->sum('observed_views');
        $approvedSubmissions = $submissions->whereIn('status', ['approved_by_brand', 'verified']);
        $totalEarned = (float) $approvedSubmissions->sum('verified_reward') + (float) $wallet->total_withdrawn;

        $availableCampaigns = Campaign::with('brand')
            ->whereIn('status', ['active', 'half_filled'])
            ->where('remaining_budget', '>', 0)
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        return view('pages.dashboards.clipper', compact('user', 'wallet', 'submissions', 'totalViews', 'totalEarned', 'availableCampaigns'));
    }

    /**
     * Update Clipper Payout Bank / Phone Details
     */
    public function updatePayoutSettings(Request $request)
    {
        $validated = $request->validate([
            'payout_bank_name' => 'required|string|max:150',
            'payout_account_holder' => 'required|string|max:150',
            'payout_account_number' => 'required|string|max:100',
            'payout_phone_number' => 'required|string|max:50',
            'payout_routing_swift' => 'nullable|string|max:50',
        ]);

        $wallet = Wallet::firstOrCreate(
            ['user_id' => Auth::id()],
            ['currency' => 'USD', 'available_balance' => 0.00]
        );

        $wallet->update([
            'payout_bank_name' => $validated['payout_bank_name'],
            'payout_account_holder' => $validated['payout_account_holder'],
            'payout_account_number' => $validated['payout_account_number'],
            'payout_phone_number' => $validated['payout_phone_number'],
            'payout_routing_swift' => $validated['payout_routing_swift'] ?? null,
        ]);

        session()->flash('success', 'Payout destination details updated! Admin will use these bank / mobile account details to send payments directly to you.');
        return back();
    }

    /**
     * Brand Operations Dashboard
     */
    public function brandDashboard()
    {
        $user = Auth::user();

        if ($user->role === 'clipper') {
            return redirect()->route('web.clipper.dashboard');
        }

        $brand = $user->brand ?: Brand::firstOrCreate(['user_id' => $user->id], [
            'company_name' => $user->name ?: 'Brand Advertiser',
            'company_slug' => Str::slug($user->name ?: 'Brand') . '-' . $user->id,
        ]);

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['currency' => 'USD', 'available_balance' => 0.00]
        );

        $campaigns = Campaign::withCount('submissions')
            ->where('brand_id', $brand->id)
            ->orderBy('id', 'desc')
            ->get();

        $totalCampaignBudget = (float) $campaigns->sum('total_budget');
        $totalRemainingBudget = (float) $campaigns->sum('remaining_budget');
        $totalViewsDelivered = (int) $campaigns->sum('verified_views');

        $campaignIds = $campaigns->pluck('id');
        $incomingSubmissions = ClipSubmission::with(['campaign', 'user.wallet'])
            ->whereIn('campaign_id', $campaignIds)
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.dashboards.brand', compact('brand', 'wallet', 'campaigns', 'totalCampaignBudget', 'totalRemainingBudget', 'totalViewsDelivered', 'incomingSubmissions'));
    }

    /**
     * Submit Clip Action
     */
    public function submitClip(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isClipper()) {
            return back()->withErrors(['msg' => 'Only registered creators/clippers can submit clips.']);
        }

        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'platform' => 'required|string',
            'clip_url' => 'required|url',
        ]);

        $campaign = Campaign::with(['brand.user', 'platforms'])->findOrFail($validated['campaign_id']);

        try {
            $service = app(\App\Services\ClipSubmissionService::class);
            $service->createSubmission($user, $campaign, [
                'platform' => $validated['platform'],
                'post_url' => $validated['clip_url'],
                'observed_views' => 1500,
            ]);

            session()->flash('success', 'Clip successfully submitted! The brand will review your video. Once approved, Admin will transfer your payout directly to your bank account.');
            return back();
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    /**
     * Brand Approves Clip Action -> Moves to Admin Pending Payout
     */
    public function approveClip($id)
    {
        $user = Auth::user();
        $brand = $user->brand;
        if (!$brand) {
            return back()->withErrors(['msg' => 'Brand profile not found.']);
        }

        $clip = ClipSubmission::with(['campaign', 'user.wallet'])->findOrFail($id);

        if ($clip->campaign->brand_id !== $brand->id) {
            abort(403, 'Unauthorized');
        }

        if (in_array($clip->status, ['approved_by_brand', 'verified'])) {
            return back()->with('info', 'Clip is already approved.');
        }

        $cpm = (float) ($clip->campaign->reward_rate_per_thousand > 0 ? $clip->campaign->reward_rate_per_thousand : 5.00);
        $reward = (float) ($clip->estimated_reward > 0 ? $clip->estimated_reward : $cpm);

        // Deduct from campaign remaining budget if sufficient
        if ($clip->campaign->remaining_budget >= $reward) {
            $clip->campaign->decrement('remaining_budget', $reward);
        }

        $clip->update([
            'status' => 'approved_by_brand',
            'verified_reward' => $reward,
            'verified_at' => now(),
        ]);

        $clipperWallet = $clip->user ? $clip->user->wallet : null;
        $destinationAccount = '';
        if ($clipperWallet) {
            $destinationAccount = "Bank: " . ($clipperWallet->payout_bank_name ?: 'N/A') .
                " | Acc: " . ($clipperWallet->payout_account_number ?: 'N/A') .
                " (" . ($clipperWallet->payout_account_holder ?: $clip->user->name) . ")" .
                ($clipperWallet->payout_phone_number ? " | Tel: " . $clipperWallet->payout_phone_number : "");
        } else {
            $destinationAccount = "Clipper Name: " . ($clip->user ? $clip->user->name : 'Unknown');
        }

        // Create Payout in PENDING_ADMIN_REVIEW
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
            'destination_account' => $destinationAccount,
            'approved_by_brand_at' => now(),
        ]);

        session()->flash('success', "Clip approved! Payout of \${$reward} is now queued for Admin verification. Admin will transfer the funds directly to Clipper's bank account.");
        return back();
    }

    /**
     * Brand Rejects Clip Action
     */
    public function rejectClip(Request $request, $id)
    {
        $user = Auth::user();
        $brand = $user->brand;
        if (!$brand) {
            return back()->withErrors(['msg' => 'Brand profile not found.']);
        }

        $clip = ClipSubmission::with('campaign')->findOrFail($id);

        if ($clip->campaign->brand_id !== $brand->id) {
            abort(403, 'Unauthorized');
        }

        $clip->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('reason', 'Clip did not meet campaign guidelines.'),
        ]);

        session()->flash('info', 'Clip submission marked as rejected.');
        return back();
    }

    /**
     * Brand Ends / Closes Campaign -> Leftover Budget Refunded to Brand Wallet
     */
    public function endCampaign($id)
    {
        $user = Auth::user();
        $brand = $user->brand;
        if (!$brand) {
            return back()->withErrors(['msg' => 'Brand profile not found.']);
        }

        $campaign = Campaign::where('brand_id', $brand->id)->findOrFail($id);

        if (in_array($campaign->status, ['completed', 'cancelled'])) {
            return back()->with('info', 'Campaign is already closed.');
        }

        $unspentBudget = (float) $campaign->remaining_budget;

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['currency' => 'USD', 'available_balance' => 0.00]
        );

        if ($unspentBudget > 0) {
            $wallet->increment('available_balance', $unspentBudget);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'campaign_refund',
                'amount' => $unspentBudget,
                'currency' => 'USD',
                'status' => 'completed',
                'description' => "Unspent budget returned from campaign: {$campaign->title}",
                'reference_id' => 'REFUND_CMP_' . $campaign->id,
            ]);
        }

        $campaign->update([
            'status' => 'completed',
            'remaining_budget' => 0.00,
        ]);

        session()->flash('success', "Campaign ended! Unspent budget of \$" . number_format($unspentBudget, 2) . " has been credited back to your Brand Wallet.");
        return back();
    }

    /**
     * Create Brand Campaign Action (Stripe or Brand Wallet Funding)
     */
    public function createCampaign(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'total_budget' => 'required|numeric|min:50',
            'reward_rate_per_thousand' => 'required|numeric|min:0.5',
            'description' => 'required|string',
            'category' => 'nullable|string|max:100',
            'source_video_url' => 'nullable|url',
            'payment_method' => 'required|in:stripe,wallet',
        ]);

        $user = Auth::user();
        $brand = $user->brand ?: Brand::firstOrCreate(['user_id' => $user->id], [
            'company_name' => $user->name ?: 'Advertiser Studio',
            'company_slug' => Str::slug($user->name ?: 'Advertiser') . '-' . $user->id,
        ]);

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['currency' => 'USD', 'available_balance' => 0.00]
        );

        $budget = (float) $validated['total_budget'];

        if ($validated['payment_method'] === 'wallet') {
            if ($wallet->available_balance < $budget) {
                return back()->withErrors([
                    'total_budget' => "Insufficient wallet balance (\$" . number_format($wallet->available_balance, 2) . "). Please choose 'Stripe Card Payment' to fund this campaign."
                ])->withInput();
            }

            // Deduct from wallet
            $wallet->decrement('available_balance', $budget);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'campaign_funding',
                'amount' => -$budget,
                'currency' => 'USD',
                'status' => 'completed',
                'description' => "Budget allocated to campaign: {$validated['title']}",
                'reference_id' => 'WALLET_CMP_' . time(),
            ]);

            $campaign = Campaign::create([
                'brand_id' => $brand->id,
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']) . '-' . time(),
                'description' => $validated['description'],
                'category' => $validated['category'] ?? 'Technology & AI',
                'status' => 'active',
                'content_type' => 'video',
                'source_video_url' => $validated['source_video_url'] ?? 'https://youtube.com/shorts/sample',
                'total_budget' => $budget,
                'remaining_budget' => $budget,
                'reward_rate_per_thousand' => $validated['reward_rate_per_thousand'],
                'target_views' => (int) (($budget / $validated['reward_rate_per_thousand']) * 1000),
                'verified_views' => 0,
            ]);

            session()->flash('success', "Campaign published successfully! Budget of \$" . number_format($budget, 2) . " allocated from Brand Wallet.");
            return redirect()->route('web.brand.dashboard');
        } else {
            // Stripe Hosted Checkout Flow:
            // Create campaign in pending_payment status, then redirect to Stripe checkout
            $campaign = Campaign::create([
                'brand_id' => $brand->id,
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']) . '-' . time(),
                'description' => $validated['description'],
                'category' => $validated['category'] ?? 'Technology & AI',
                'status' => 'pending_payment',
                'content_type' => 'video',
                'source_video_url' => $validated['source_video_url'] ?? 'https://youtube.com/shorts/sample',
                'total_budget' => $budget,
                'remaining_budget' => $budget,
                'reward_rate_per_thousand' => $validated['reward_rate_per_thousand'],
                'target_views' => (int) (($budget / $validated['reward_rate_per_thousand']) * 1000),
                'verified_views' => 0,
            ]);

            $checkoutService = app(\App\Services\StripeCheckoutService::class);
            $checkoutUrl = $checkoutService->createCampaignCheckoutSession($campaign, $user);

            if ($checkoutUrl) {
                return redirect()->away($checkoutUrl, 303);
            }

            session()->flash('error', 'Stripe checkout could not be initiated. Please check your Stripe API configuration.');
            return redirect()->route('web.brand.dashboard');
        }
    }

    /**
     * Withdraw Action (Optional manual request)
     */
    public function withdraw(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:5',
            'payout_method' => 'required|string',
            'recipient_account' => 'required|string',
        ]);

        $userId = Auth::id();
        $wallet = Wallet::where('user_id', $userId)->first();

        if (!$wallet || $wallet->available_balance < $validated['amount']) {
            return back()->withErrors(['amount' => 'Insufficient available balance.']);
        }

        Withdrawal::create([
            'user_id' => $userId,
            'amount' => $validated['amount'],
            'currency' => 'USD',
            'method' => $validated['payout_method'],
            'destination_reference' => $validated['recipient_account'],
            'status' => 'processing',
        ]);

        $wallet->decrement('available_balance', $validated['amount']);
        $wallet->increment('total_withdrawn', $validated['amount']);

        session()->flash('success', 'Withdrawal request for $' . number_format($validated['amount'], 2) . ' submitted for processing.');
        return back();
    }

    /**
     * Show Forgot Password View
     */
    public function forgotPasswordView()
    {
        return view('pages.auth.forgot-password');
    }

    /**
     * Process Forgot Password Request
     */
    public function sendPasswordReset(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', trim(strtolower($validated['email'])))->first();
        if ($user) {
            // Safe password reset dispatch
            session()->flash('success', 'Password reset instructions have been sent to ' . htmlspecialchars($validated['email']) . ' if associated with an active account.');
        } else {
            // Anti-enumeration: Return identical message
            session()->flash('success', 'If an account exists for ' . htmlspecialchars($validated['email']) . ', password reset instructions have been dispatched.');
        }

        return back();
    }
}

