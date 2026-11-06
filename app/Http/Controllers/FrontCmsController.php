<?php

namespace App\Http\Controllers;

use App\Models\CmsCategory;
use App\Models\CmsPage;
use App\Models\CmsPost;
use Illuminate\Http\Request;

class FrontCmsController extends Controller
{
    /**
     * Blog Articles Directory
     */
    public function blogIndex(Request $request)
    {
        $pageTitle = 'Official Platform Blog & Creator Insights';
        $query = CmsPost::where('status', 'published')->with(['category', 'author'])->latest();

        $selectedCategory = null;
        if ($request->filled('category')) {
            $selectedCategory = CmsCategory::where('slug', $request->category)->first();
            if ($selectedCategory) {
                $query->where('category_id', $selectedCategory->id);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(9)->withQueryString();
        $categories = CmsCategory::where('is_active', true)->withCount(['posts' => function($q) {
            $q->where('status', 'published');
        }])->get();

        $featuredPosts = CmsPost::where('status', 'published')
            ->where('is_featured', true)
            ->latest()
            ->take(3)
            ->get();

        return view('frontend.blog.index', compact('posts', 'categories', 'selectedCategory', 'featuredPosts', 'pageTitle'));
    }

    /**
     * Single Blog Article View
     */
    public function blogPost($slug)
    {
        $post = CmsPost::where('slug', $slug)
            ->where('status', 'published')
            ->with(['category', 'author'])
            ->firstOrFail();

        // Increment views safely
        $post->increment('views');

        $pageTitle = $post->meta_title ?: $post->title;
        $relatedPosts = CmsPost::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->when($post->category_id, function($q) use ($post) {
                $q->where('category_id', $post->category_id);
            })
            ->latest()
            ->take(3)
            ->get();

        return view('frontend.blog.show', compact('post', 'relatedPosts', 'pageTitle'));
    }

    /**
     * Dynamic CMS Page & Legal Policy View
     */
    public function pageView($slug)
    {
        $normalizedSlug = strtolower(trim($slug));

        // Alias mapping for common slugs
        $aliases = [
            'terms' => 'terms-of-service',
            'tos' => 'terms-of-service',
            'privacy' => 'privacy-policy',
            'creator-agreement' => 'clipper-agreement',
            'about' => 'about-us',
        ];
        if (isset($aliases[$normalizedSlug])) {
            $normalizedSlug = $aliases[$normalizedSlug];
        }

        $page = CmsPage::where('slug', $normalizedSlug)
            ->where('status', 'published')
            ->first();

        if (!$page) {
            $page = CmsPage::where('slug', $slug)->first();
        }

        if (!$page) {
            // Provide automatic graceful fallback for key legal and informational pages
            $defaults = [
                'terms-of-service' => [
                    'title' => 'Terms of Service',
                    'meta_title' => 'Terms of Service — GEZUX Platform Governance',
                    'excerpt' => 'Official platform usage terms, rights, and creator payout conditions.',
                    'content' => '<h2>GEZUX Platform Terms of Service</h2><p class="lead">Last updated: October 2026. Please review these terms carefully.</p><h3>1. Acceptance of Terms</h3><p>By registering an account as a Brand or Clipper on GEZUX, you agree to comply with all platform governance rules, campaign brief specifications, and verified view submission guidelines.</p><h3>2. Brand Campaign Pre-Funding & Budget</h3><p>Brands fund campaign bounty pools securely via Stripe or Brand Wallet. Once funded, campaigns become active on the marketplace and clippers can produce and submit short-form content. Leftover funds from ended campaigns are refundable.</p><h3>3. Clipper Submissions & Payouts</h3><p>Clippers earn verified bounties based on active campaign models (Performance CPM or Fixed Pay Per Video). Payouts are transferred directly to connected bank accounts once clips are reviewed and verified.</p><h3>4. Prohibited Content</h3><p>Copyright infringement, automated view fraud, botting, and deceptive practices will result in immediate account termination and forfeiture of unpaid rewards.</p>',
                ],
                'privacy-policy' => [
                    'title' => 'Privacy Policy',
                    'meta_title' => 'Privacy Policy — GEZUX Data Protection',
                    'excerpt' => 'How GEZUX collects, uses, and protects your personal and financial information.',
                    'content' => '<h2>GEZUX Privacy Policy</h2><p class="lead">Your privacy is fundamental to our marketplace operations.</p><h3>1. Information We Collect</h3><p>We collect essential account information (name, email, social media handles) to verify creator identity and direct bank payout details to fulfill payments. Payment card details are processed directly and securely by Stripe.</p><h3>2. How We Use Data</h3><p>We use your data strictly to administer marketplace campaigns, verify video submissions, execute creator payouts, and prevent fraudulent activities.</p><h3>3. Security & Retention</h3><p>All sensitive credentials and database records are encrypted using industry-standard protocols. We never sell your personal information to third parties.</p>',
                ],
                'clipper-agreement' => [
                    'title' => 'Clipper & Creator Agreement',
                    'meta_title' => 'Clipper Agreement — GEZUX Creator Terms',
                    'excerpt' => 'Rules of engagement, content guidelines, and payment terms for viral short-form clippers.',
                    'content' => '<h2>GEZUX Clipper & Creator Agreement</h2><p class="lead">Standard terms governing video clipping, fair-use compliance, and view verification.</p><h3>1. Content Authenticity</h3><p>Clippers agree to use provided brand footage respectfully and adhere strictly to creative guidelines, forbidden topics, and hashtag specifications outlined in each campaign brief.</p><h3>2. Verified View Tracking</h3><p>Views are audited through public social platform telemetry. Submissions found utilizing view bots or fake engagement will be rejected without appeal.</p><h3>3. Direct Bank Transfers</h3><p>Verified earnings are credited to the creator wallet and disbursed via direct bank transfer upon payout request.</p>',
                ],
                'brand-guidelines' => [
                    'title' => 'Brand & Advertiser Guidelines',
                    'meta_title' => 'Brand Guidelines — Campaign Standards & Quality Rules',
                    'excerpt' => 'Standards for launching viral creator bounty pools on GEZUX.',
                    'content' => '<h2>GEZUX Brand & Advertiser Guidelines</h2><p class="lead">Guidelines to maximize campaign ROI and viral creator participation.</p><h3>1. Campaign Brief Clarity</h3><p>Provide clear source video links, high-quality hooks, and distinct talking points. Transparent reward rates ($/1,000 views or fixed per-video bounties) attract top-tier clipping talent.</p><h3>2. Budget Pre-Funding</h3><p>All campaign bounties must be fully pre-funded via Stripe Checkout or Brand Wallet prior to marketplace publication to guarantee creator compensation.</p><h3>3. Prompt Clip Review</h3><p>Brands should review pending submissions in their dashboard within 48-72 hours to ensure rapid content amplification.</p>',
                ],
                'about-us' => [
                    'title' => 'About GEZUX',
                    'meta_title' => 'About GEZUX — The Decentralized Video Clipping Infrastructure',
                    'excerpt' => 'Powering the next generation of viral short-form distribution for top podcasts, founders, and brands.',
                    'content' => '<h2>About GEZUX</h2><p class="lead">The premier short-form clipping marketplace connecting visionary brands with viral video editors.</p><p>GEZUX revolutionizes how long-form media transforms into viral short-form vertical video across TikTok, YouTube Shorts, and Instagram Reels. Brands deposit bounties into guaranteed reward pools, and thousands of verified clippers compete to deliver authentic, high-retention reach.</p>',
                ],
            ];

            if (isset($defaults[$normalizedSlug])) {
                $d = $defaults[$normalizedSlug];
                $page = new CmsPage([
                    'title' => $d['title'],
                    'slug' => $normalizedSlug,
                    'type' => 'legal_terms',
                    'excerpt' => $d['excerpt'],
                    'content' => $d['content'],
                    'meta_title' => $d['meta_title'],
                    'meta_description' => $d['excerpt'],
                    'status' => 'published',
                ]);
                $page->id = 999;
                $page->created_at = now();
                $page->updated_at = now();
            } else {
                abort(404, 'The requested document or page does not exist.');
            }
        }

        $pageTitle = $page->meta_title ?: $page->title;

        return view('frontend.pages.show', compact('page', 'pageTitle'));
    }
}
