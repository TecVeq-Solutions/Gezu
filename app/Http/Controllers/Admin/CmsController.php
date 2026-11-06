<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsCategory;
use App\Models\CmsMenu;
use App\Models\CmsMenuItem;
use App\Models\CmsPage;
use App\Models\CmsPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CmsController extends Controller
{
    // ==========================================
    // 1. PAGES & LEGAL DOCUMENTS
    // ==========================================
    public function pages(Request $request)
    {
        $pageTitle = 'CMS Pages & Legal Agreements';
        self::ensureDefaultPages();

        $query = CmsPage::query()->latest();

        if ($request->filled('type')) {
            $query->where('page_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $pages = $query->paginate(20);
        $types = [
            'standard' => 'Website Pages',
            'legal_terms' => 'Terms of Service',
            'legal_privacy' => 'Privacy Policy',
            'legal_refund' => 'Refund Policy',
            'legal_disclaimer' => 'Disclaimer',
            'legal_clipper' => 'Clipper Agreement',
            'legal_brand' => 'Brand Guidelines',
            'about' => 'About Us',
            'contact' => 'Contact Us',
        ];

        return view('admin.cms.pages.index', compact('pages', 'pageTitle', 'types'));
    }

    public static function ensureDefaultPages()
    {
        $defaults = [
            [
                'title' => 'Home Page — Marketplace & Video Clipping Engine',
                'slug' => 'home',
                'page_type' => 'standard',
                'excerpt' => 'GEZUX is the premier short-form clipping marketplace connecting brands with skilled video editors for viral distribution.',
                'content' => '<h2>Welcome to GEZUX</h2><p class="lead">The next-generation video clipping performance marketplace.</p><p>We empower brands to scale viral reach on TikTok, YouTube Shorts, and Instagram Reels by collaborating with elite video clippers.</p>',
                'meta_title' => 'GEZUX — The Short-Form Video Clipping Marketplace',
                'meta_description' => 'Connect brands with skilled video clippers. Turn long-form YouTube & podcast content into viral shorts and earn cash bounties for verified views.',
                'meta_keywords' => 'video clipping, short form content, tiktok clippers, viral video marketing, brand bounties',
                'is_system' => true,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'page_type' => 'legal_terms',
                'excerpt' => 'Official platform usage terms, responsibilities, and dispute resolution guidelines.',
                'content' => '<h2>Platform Terms of Service</h2><p class="lead">Last revised: October 2026. Please review these terms carefully.</p><h3>1. Acceptance of Terms</h3><p>By registering on GEZUX as an Advertiser or Clipper, you agree to comply with all platform governance rules.</p><h3>2. View Verification & Anti-Fraud</h3><p>Views must originate organically from verified social channels. Artificial inflation or botting results in immediate termination.</p><h3>3. Payouts & Settlements</h3><p>Creator earnings are settled via direct wire transfers following review of performance telemetry.</p>',
                'meta_title' => 'Terms of Service — GEZUX Performance Clipping',
                'meta_description' => 'Read the official terms and conditions for utilizing the GEZUX platform for advertisers and clippers.',
                'meta_keywords' => 'terms of service, legal, platform rules, terms and conditions',
                'is_system' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'page_type' => 'legal_privacy',
                'excerpt' => 'Comprehensive privacy practices, data handling, and encryption safeguards.',
                'content' => '<h2>Global Privacy Policy</h2><p class="lead">We prioritize your data security and confidentiality across all platform interactions.</p><h3>1. Data We Collect</h3><p>We collect essential account details, payout wire information, and public social telemetry associated with submitted campaigns.</p><h3>2. Security & Compliance</h3><p>All sensitive information is encrypted using industry-standard AES-256 protocols. We never sell your personal data.</p>',
                'meta_title' => 'Privacy Policy — GEZUX Data Protection',
                'meta_description' => 'Our commitment to privacy, data protection, and user confidentiality across all platform services.',
                'meta_keywords' => 'privacy policy, data protection, gdpr, security',
                'is_system' => true,
            ],
            [
                'title' => 'Clipper Agreement & Code of Conduct',
                'slug' => 'clipper-agreement',
                'page_type' => 'legal_clipper',
                'excerpt' => 'Quality benchmarks, editing rules, and submission protocols for creators.',
                'content' => '<h2>Creator Code of Conduct & Clipping Guidelines</h2><p class="lead">Operational benchmarks for high-performing video clippers on GEZUX.</p><h3>1. Content Quality</h3><p>Clips must be rendered in vertical 1080x1920 resolution with accurate subtitles and synchronized audio.</p><h3>2. Submission Authenticity</h3><p>All submitted clips must originate from authorized brand source footage provided in active campaign briefs.</p>',
                'meta_title' => 'Clipper Agreement — Video Creator Code of Conduct',
                'meta_description' => 'Operational standards, formatting requirements, and code of conduct for video creators clipping on GEZUX.',
                'meta_keywords' => 'creator agreement, code of conduct, video editing standards',
                'is_system' => true,
            ],
            [
                'title' => 'Brand Campaign Guidelines',
                'slug' => 'brand-guidelines',
                'page_type' => 'legal_brand',
                'excerpt' => 'Guidelines for launching, funding, and managing performance bounty campaigns.',
                'content' => '<h2>Brand & Advertiser Campaign Guidelines</h2><p class="lead">Best practices for launching performance video bounty campaigns on GEZUX.</p><h3>1. Campaign Funding</h3><p>Brands deposit campaign bounties via Stripe. Bounties are exclusively disbursed upon verified view milestones.</p><h3>2. Creative Briefing</h3><p>Advertisers must supply clear brand guidelines, approved source links, and appropriate hashtag requirements.</p>',
                'meta_title' => 'Brand Guidelines — Campaign Launch & Funding Protocols',
                'meta_description' => 'Instructions for advertisers launching performance short-form clipping campaigns on GEZUX.',
                'meta_keywords' => 'brand guidelines, advertiser rules, campaign funding',
                'is_system' => true,
            ],
            [
                'title' => 'Refund & Cancellation Policy',
                'slug' => 'refund-policy',
                'page_type' => 'legal_refund',
                'excerpt' => 'Fair balance refund policies for brand campaign deposits and wallet accounts.',
                'content' => '<h2>Refund & Cancellation Policy</h2><p class="lead">Transparent campaign pool and wallet balance refund terms.</p><h3>1. Unallocated Campaign Balances</h3><p>If a campaign reaches expiry or is terminated with remaining budget, unspent funds remain in the brand platform balance and can be withdrawn or reallocated.</p><h3>2. Payout Disbursals</h3><p>Approved clipper view milestones are non-refundable once disbursed.</p>',
                'meta_title' => 'Refund & Cancellation Policy — GEZUX',
                'meta_description' => 'Read our fair campaign pool and wallet balance refund terms for advertisers and content creators.',
                'meta_keywords' => 'refund policy, cancellation, deposit return, campaign budget',
                'is_system' => true,
            ],
            [
                'title' => 'Earnings Disclaimer & Legal Disclosure',
                'slug' => 'disclaimer',
                'page_type' => 'legal_disclaimer',
                'excerpt' => 'Performance disclaimers, engagement metrics, and revenue disclosures.',
                'content' => '<h2>Earnings & Performance Disclaimer</h2><p class="lead">Important regulatory information regarding creator payouts and campaign results.</p><h3>1. No Guaranteed Earnings</h3><p>Creator revenue depends entirely on organic clip engagement, algorithm reach, and adherence to campaign verification rules. No baseline revenue is guaranteed.</p><h3>2. Platform Neutrality</h3><p>GEZUX connects commercial advertisers with independent freelance video editors.</p>',
                'meta_title' => 'Earnings Disclaimer — GEZUX Performance Disclosure',
                'meta_description' => 'Regulatory disclosures and earnings disclaimers for independent creators and video editors.',
                'meta_keywords' => 'earnings disclaimer, legal disclosure, creator earnings',
                'is_system' => true,
            ],
            [
                'title' => 'About Us — GEZUX Performance Clipping',
                'slug' => 'about-us',
                'page_type' => 'about',
                'excerpt' => 'Discover the mission, technology, and vision powering the decentralized short-form clipping revolution.',
                'content' => '<h2>About GEZUX</h2><p class="lead">Empowering dynamic brands and video editors through performance bounties.</p><p>We are building the future of short-form video advertising by connecting visionary companies with viral editors across the globe.</p>',
                'meta_title' => 'About GEZUX — The Performance Clipping Platform',
                'meta_description' => 'Discover GEZUX, the premier platform connecting brands with short-form clippers for viral distribution.',
                'meta_keywords' => 'about gezux, company, video clipping platform',
                'is_system' => true,
            ],
            [
                'title' => 'How It Works — Guide For Brands & Clippers',
                'slug' => 'how-it-works',
                'page_type' => 'standard',
                'excerpt' => 'Step-by-step walkthrough of creating campaigns, clipping video highlights, and getting paid.',
                'content' => '<h2>How GEZUX Works</h2><p class="lead">A frictionless workflow for advertisers and video creators.</p><h3>For Brands</h3><p>Upload your video footage, set a CPM reward rate, and fund your bounty pool with Stripe.</p><h3>For Clippers</h3><p>Pick a campaign, create engaging vertical shorts, publish to social channels, and submit the link to earn cash per 1,000 verified views.</p>',
                'meta_title' => 'How It Works — GEZUX Creator & Brand Guide',
                'meta_description' => 'Learn how to launch video bounty campaigns or edit clips to earn cash on GEZUX.',
                'meta_keywords' => 'how it works, video clipping guide, creator guide',
                'is_system' => true,
            ],
            [
                'title' => 'Contact Platform Support',
                'slug' => 'contact-us',
                'page_type' => 'contact',
                'excerpt' => 'Reach our dedicated support team for advertiser inquiries, creator payouts, and platform assistance.',
                'content' => '<h2>Contact GEZUX Support</h2><p class="lead">We are here to assist with campaigns, creator verification, and payout inquiries.</p><p>Email us at: <strong>support@gezux.com</strong></p>',
                'meta_title' => 'Contact Us — GEZUX Support & Helpdesk',
                'meta_description' => 'Need help? Contact the GEZUX team for brand campaign assistance or creator inquiries.',
                'meta_keywords' => 'contact support, helpdesk, customer service',
                'is_system' => true,
            ],
        ];

        foreach ($defaults as $data) {
            try {
                if (!CmsPage::where('slug', $data['slug'])->exists()) {
                    CmsPage::create(array_merge($data, [
                        'status' => 'published',
                        'custom_schema' => '{"@context": "https://schema.org", "@type": "WebPage", "name": "' . addslashes($data['title']) . '", "description": "' . addslashes($data['meta_description']) . '"}',
                    ]));
                }
            } catch (\Throwable $e) {}
        }
    }

    public function createPage()
    {
        $pageTitle = 'Create New CMS / Legal Page';
        return view('admin.cms.pages.create', compact('pageTitle'));
    }

    public function storePage(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug',
            'page_type' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|in:published,draft',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $slug = $request->filled('slug') 
            ? Str::slug($request->slug) 
            : Str::slug($request->title);

        // Ensure unique
        $originalSlug = $slug;
        $count = 1;
        while (CmsPage::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        CmsPage::create([
            'title' => $request->title,
            'slug' => $slug,
            'page_type' => $request->page_type,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'status' => $request->status,
            'meta_title' => $request->meta_title ?: $request->title,
            'meta_description' => $request->meta_description ?: Str::limit(strip_tags($request->excerpt ?: $request->content), 160),
            'meta_keywords' => $request->meta_keywords,
            'custom_schema' => $request->custom_schema,
        ]);

        $notify[] = ['success', 'Page created successfully!'];
        return redirect()->route('admin.cms.pages.index')->withNotify($notify);
    }

    public function editPage($id)
    {
        $page = CmsPage::findOrFail($id);
        $pageTitle = "Edit Page — {$page->title}";
        return view('admin.cms.pages.edit', compact('page', 'pageTitle'));
    }

    public function updatePage(Request $request, $id)
    {
        $page = CmsPage::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => "required|string|max:255|unique:cms_pages,slug,{$id}",
            'page_type' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|in:published,draft',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => Str::slug($request->slug),
            'page_type' => $request->page_type,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'status' => $request->status,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'custom_schema' => $request->custom_schema,
        ]);

        $notify[] = ['success', 'Page updated successfully!'];
        return back()->withNotify($notify);
    }

    public function deletePage($id)
    {
        $page = CmsPage::findOrFail($id);
        if ($page->is_system) {
            $notify[] = ['error', 'System legal pages cannot be deleted to maintain platform compliance. You may set status to Draft.'];
            return back()->withNotify($notify);
        }

        $page->delete();
        $notify[] = ['success', 'Page deleted successfully!'];
        return back()->withNotify($notify);
    }

    // ==========================================
    // 2. BLOG POSTS
    // ==========================================
    public function posts(Request $request)
    {
        $pageTitle = 'Blog Posts Management';
        $query = CmsPost::with(['category', 'author'])->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(15);
        $categories = CmsCategory::where('is_active', true)->get();

        return view('admin.cms.posts.index', compact('posts', 'pageTitle', 'categories'));
    }

    public function createPost()
    {
        $pageTitle = 'Create New Blog Post';
        $categories = CmsCategory::where('is_active', true)->get();
        return view('admin.cms.posts.create', compact('pageTitle', 'categories'));
    }

    public function storePost(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_posts,slug',
            'category_id' => 'nullable|exists:cms_categories,id',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:600',
            'status' => 'required|in:published,draft',
            'featured_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $slug = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;
        while (CmsPost::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $featuredImage = $request->featured_image_url ?: '/assets/admin/images/blog/blog-1.jpg';
        if ($request->hasFile('featured_image_file')) {
            $path = $request->file('featured_image_file')->store('cms/posts', 'public');
            $featuredImage = '/storage/' . $path;
        }

        CmsPost::create([
            'user_id' => Auth::id() ?: 4,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => $slug,
            'excerpt' => $request->excerpt ?: Str::limit(strip_tags($request->content), 200),
            'content' => $request->content,
            'featured_image' => $featuredImage,
            'is_featured' => $request->has('is_featured'),
            'status' => $request->status,
            'meta_title' => $request->meta_title ?: $request->title,
            'meta_description' => $request->meta_description ?: Str::limit(strip_tags($request->excerpt ?: $request->content), 160),
            'meta_keywords' => $request->meta_keywords,
            'custom_schema' => $request->custom_schema,
            'published_at' => $request->status === 'published' ? now() : null,
        ]);

        $notify[] = ['success', 'Blog article published successfully!'];
        return redirect()->route('admin.cms.posts.index')->withNotify($notify);
    }

    public function editPost($id)
    {
        $post = CmsPost::findOrFail($id);
        $pageTitle = "Edit Post — {$post->title}";
        $categories = CmsCategory::where('is_active', true)->get();
        return view('admin.cms.posts.edit', compact('post', 'pageTitle', 'categories'));
    }

    public function updatePost(Request $request, $id)
    {
        $post = CmsPost::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => "required|string|max:255|unique:cms_posts,slug,{$id}",
            'category_id' => 'nullable|exists:cms_categories,id',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:600',
            'status' => 'required|in:published,draft',
            'featured_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $featuredImage = $post->featured_image;
        if ($request->filled('featured_image_url')) {
            $featuredImage = $request->featured_image_url;
        }
        if ($request->hasFile('featured_image_file')) {
            $path = $request->file('featured_image_file')->store('cms/posts', 'public');
            $featuredImage = '/storage/' . $path;
        }

        $post->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->slug),
            'excerpt' => $request->excerpt ?: Str::limit(strip_tags($request->content), 200),
            'content' => $request->content,
            'featured_image' => $featuredImage,
            'is_featured' => $request->has('is_featured'),
            'status' => $request->status,
            'meta_title' => $request->meta_title ?: $request->title,
            'meta_description' => $request->meta_description ?: Str::limit(strip_tags($request->excerpt ?: $request->content), 160),
            'meta_keywords' => $request->meta_keywords,
            'custom_schema' => $request->custom_schema,
            'published_at' => ($request->status === 'published' && !$post->published_at) ? now() : $post->published_at,
        ]);

        $notify[] = ['success', 'Blog article updated successfully!'];
        return back()->withNotify($notify);
    }

    public function deletePost($id)
    {
        $post = CmsPost::findOrFail($id);
        $post->delete();

        $notify[] = ['success', 'Blog post removed successfully!'];
        return back()->withNotify($notify);
    }

    // ==========================================
    // 3. BLOG CATEGORIES
    // ==========================================
    public function categories()
    {
        $pageTitle = 'Blog Categories Management';
        $categories = CmsCategory::withCount('posts')->latest()->get();
        return view('admin.cms.categories.index', compact('categories', 'pageTitle'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $slug = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->name);

        CmsCategory::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'icon' => $request->icon ?: 'las la-folder',
            'is_active' => true,
        ]);

        $notify[] = ['success', 'Category added successfully!'];
        return back()->withNotify($notify);
    }

    public function updateCategory(Request $request, $id)
    {
        $category = CmsCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => "required|string|max:255|unique:cms_categories,slug,{$id}",
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->slug),
            'description' => $request->description,
            'icon' => $request->icon ?: 'las la-folder',
            'is_active' => $request->has('is_active'),
        ]);

        $notify[] = ['success', 'Category updated successfully!'];
        return back()->withNotify($notify);
    }

    public function deleteCategory($id)
    {
        $category = CmsCategory::findOrFail($id);
        $category->delete();

        $notify[] = ['success', 'Category deleted successfully!'];
        return back()->withNotify($notify);
    }

    // ==========================================
    // 4. MENU BUILDER
    // ==========================================
    public function menus(Request $request)
    {
        $pageTitle = 'Navigation Menu Builder';
        $menus = CmsMenu::with('items')->get();

        $activeMenuSlug = $request->get('menu', 'header');
        $activeMenu = CmsMenu::where('slug', $activeMenuSlug)->with('items')->first();

        if (!$activeMenu && $menus->isNotEmpty()) {
            $activeMenu = $menus->first();
        }

        $pages = CmsPage::where('status', 'published')->get();

        return view('admin.cms.menus.index', compact('menus', 'activeMenu', 'pages', 'pageTitle'));
    }

    public function addMenuItem(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:cms_menus,id',
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'target' => 'required|in:_self,_blank',
            'page_id' => 'nullable|exists:cms_pages,id',
        ]);

        $highestOrder = CmsMenuItem::where('menu_id', $request->menu_id)->max('order') ?? 0;

        CmsMenuItem::create([
            'menu_id' => $request->menu_id,
            'title' => $request->title,
            'url' => $request->url,
            'target' => $request->target,
            'order' => $highestOrder + 1,
            'page_id' => $request->page_id,
        ]);

        $notify[] = ['success', 'Menu link added successfully!'];
        return back()->withNotify($notify);
    }

    public function updateMenuItem(Request $request, $id)
    {
        $item = CmsMenuItem::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'target' => 'required|in:_self,_blank',
            'order' => 'required|integer',
        ]);

        $item->update([
            'title' => $request->title,
            'url' => $request->url,
            'target' => $request->target,
            'order' => $request->order,
        ]);

        $notify[] = ['success', 'Menu link updated!'];
        return back()->withNotify($notify);
    }

    public function deleteMenuItem($id)
    {
        $item = CmsMenuItem::findOrFail($id);
        $item->delete();

        $notify[] = ['success', 'Menu link removed!'];
        return back()->withNotify($notify);
    }
}
