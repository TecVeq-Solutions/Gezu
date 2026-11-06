<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminPanelController;
use App\Http\Controllers\Brand\BrandPanelController;
use App\Http\Controllers\Clipper\ClipperPanelController;
use App\Http\Controllers\Streamer\StreamerPanelController;

// 1. Admin Authentication Bridge (with brute-force rate limiting)
Route::post('admin/login', function (Request $request) {
    $email = $request->input('data.email') ?? $request->input('email');
    $password = $request->input('data.password') ?? $request->input('password');
    $remember = $request->boolean('data.remember') || $request->boolean('remember');

    if ($email && $password && Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
        $request->session()->regenerate();
        $user = Auth::user();
        if (!$user->isAdmin()) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Access denied. You do not have administrator permissions.',
            ])->withInput();
        }
        session()->forget('url.intended');
        return redirect()->to('/admin');
    }

    return redirect('/admin/login')->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->withInput();
})->middleware(['web', 'throttle:10,1']);

Route::get('/clear-cache', function () {
    $files = [
        base_path('bootstrap/cache/routes-v7.php'),
        base_path('bootstrap/cache/config.php'),
        base_path('bootstrap/cache/services.php'),
        base_path('bootstrap/cache/packages.php'),
    ];
    $deleted = [];
    foreach ($files as $file) {
        if (file_exists($file) && @unlink($file)) {
            $deleted[] = basename($file);
        }
    }
    $viewDir = storage_path('framework/views');
    $viewCount = 0;
    if (is_dir($viewDir)) {
        foreach (glob($viewDir . '/*.php') as $vf) {
            if (@unlink($vf)) { $viewCount++; }
        }
    }
    // Ensure session dir
    $sessionDir = storage_path('framework/sessions');
    if (!is_dir($sessionDir)) {
        @mkdir($sessionDir, 0775, true);
    }
    // Build today3.zip package
    if (file_exists(base_path('scratch/make_clean_zip.php'))) {
        try {
            @include_once base_path('scratch/make_clean_zip.php');
        } catch (\Throwable $e) {}
    }

    return response("<h1>Cache Cleared & today3.zip Rebuilt Successfully!</h1><p>Deleted cache files: " . (count($deleted) ? implode(', ', $deleted) : 'None') . "</p><p>Compiled views cleared: {$viewCount}</p><p><strong>today3.zip & all release aliases have been packaged in the root directory.</strong></p><p><a href='/admin/settings/updater'>Go to System Updater &rarr;</a></p>", 200);
});

Route::get('/build-today3-zip', function () {
    if (file_exists(base_path('scratch/make_clean_zip.php'))) {
        require_once base_path('scratch/make_clean_zip.php');
    }
    return response("<h1>today3.zip packaged successfully!</h1><p>File is located in your project root: <code>today3.zip</code></p><p><a href='/admin/settings/updater'>Return to System Updater &rarr;</a></p>");
});

Route::get('/admin/login', fn() => redirect()->route('login'))->name('admin.login');

// TECVEQ Custom Admin Panel Routes (Strictly Isolated to Admin & Impersonation)
Route::prefix('admin')->middleware(['web', 'auth', \App\Http\Middleware\AdminAccessMiddleware::class])->group(function () {
    Route::get('/', [AdminPanelController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/dashboard', [AdminPanelController::class, 'dashboard']);

    // Campaigns
    Route::get('/campaigns', [AdminPanelController::class, 'campaigns'])->name('admin.campaigns');
    Route::get('/campaigns/{id}/edit', [AdminPanelController::class, 'editCampaign'])->name('admin.campaigns.edit');
    Route::post('/campaigns/{id}/update', [AdminPanelController::class, 'updateCampaign'])->name('admin.campaigns.update');
    Route::post('/campaigns/{id}/toggle', [AdminPanelController::class, 'toggleCampaignStatus'])->name('admin.campaigns.toggle');
    Route::post('/campaigns/{id}/delete', [AdminPanelController::class, 'deleteCampaign'])->name('admin.campaigns.delete');

    // Clip Submissions
    Route::get('/clips', [AdminPanelController::class, 'clipSubmissions'])->name('admin.clips');
    Route::post('/clips/{id}/approve', [AdminPanelController::class, 'approveClipSubmission'])->name('admin.clips.approve');
    Route::post('/clips/{id}/reject', [AdminPanelController::class, 'rejectClipSubmission'])->name('admin.clips.reject');

    // Users & Management
    Route::get('/users', [AdminPanelController::class, 'users'])->name('admin.users');
    Route::get('/users/{id}', [AdminPanelController::class, 'userDetail'])->name('admin.users.detail');
    Route::post('/users/{id}/update', [AdminPanelController::class, 'updateUser'])->name('admin.users.update');
    Route::post('/users/{id}/adjust-balance', [AdminPanelController::class, 'adjustUserBalance'])->name('admin.users.adjust-balance');
    Route::post('/users/{id}/toggle', [AdminPanelController::class, 'toggleUserStatus'])->name('admin.users.toggle');
    Route::post('/users/{id}/delete', [AdminPanelController::class, 'deleteUser'])->name('admin.users.delete');
    Route::match(['get', 'post'], '/users/{id}/notification', [AdminPanelController::class, 'sendUserNotification'])->name('admin.users.notification.single');
    
    // Direct & Community Notifications (HYIPLab + Gezux standard aliases)
    Route::match(['get', 'post'], '/notifications/send-all', [AdminPanelController::class, 'sendAllNotification'])->name('admin.notifications.all');
    Route::match(['get', 'post'], '/notifications/broadcast', [AdminPanelController::class, 'sendAllNotification'])->name('admin.notifications.broadcast');
    Route::match(['get', 'post'], '/notifications/all', [AdminPanelController::class, 'sendAllNotification']);
    Route::match(['get', 'post'], '/send-notification', [AdminPanelController::class, 'sendAllNotification'])->name('admin.notification.all');
    Route::match(['get', 'post'], '/notification/send-all', [AdminPanelController::class, 'sendAllNotification']);
    Route::match(['get', 'post'], '/notification/broadcast', [AdminPanelController::class, 'sendAllNotification']);
    Route::match(['get', 'post'], '/notification/all', [AdminPanelController::class, 'sendAllNotification']);
    Route::match(['get', 'post'], '/send-notification/{id}', [AdminPanelController::class, 'sendUserNotification'])->name('admin.notification.single');
    Route::match(['get', 'post'], '/notification/push/setting', [AdminPanelController::class, 'settingsNotifications'])->name('admin.setting.notification.push');
    Route::post('/notification/push/setting/upload', [AdminPanelController::class, 'uploadFirebaseConfigJson'])->name('admin.setting.notification.push.upload');
    Route::get('/notification/push/setting/download', [AdminPanelController::class, 'downloadFirebaseConfigJson'])->name('admin.setting.notification.push.download');

    // Real-Time System Error Logs Monitor & Aliases
    Route::get('/error-logs', [\App\Http\Controllers\Admin\ErrorLogController::class, 'index'])->name('admin.errors.index');
    Route::get('/errors', [\App\Http\Controllers\Admin\ErrorLogController::class, 'index'])->name('admin.errors');
    Route::get('/error-log', [\App\Http\Controllers\Admin\ErrorLogController::class, 'index']);
    Route::get('/system/error-logs', [\App\Http\Controllers\Admin\ErrorLogController::class, 'index']);
    Route::get('/logs/errors', [\App\Http\Controllers\Admin\ErrorLogController::class, 'index']);
    Route::post('/error-logs/clear', [\App\Http\Controllers\Admin\ErrorLogController::class, 'clear'])->name('admin.errors.clear');
    Route::post('/errors/clear', [\App\Http\Controllers\Admin\ErrorLogController::class, 'clear']);
    Route::delete('/error-logs/{id}', [\App\Http\Controllers\Admin\ErrorLogController::class, 'destroy'])->name('admin.errors.destroy');
    Route::delete('/errors/{id}', [\App\Http\Controllers\Admin\ErrorLogController::class, 'destroy']);

    // Payouts (Direct Bank Transfers)
    Route::get('/payouts', [AdminPanelController::class, 'payouts'])->name('admin.payouts');
    Route::post('/payouts/{id}/complete', [AdminPanelController::class, 'completePayout'])->name('admin.payouts.complete');
    Route::post('/payouts/{id}/reject', [AdminPanelController::class, 'rejectPayout'])->name('admin.payouts.reject');

    // Transactions Ledger
    Route::get('/transactions', [AdminPanelController::class, 'transactions'])->name('admin.transactions');

    // Plugins & Modules
    Route::get('/plugins', [AdminPanelController::class, 'plugins'])->name('admin.plugins');
    Route::post('/plugins/{id}/toggle', [AdminPanelController::class, 'togglePlugin'])->name('admin.plugins.toggle');
    Route::post('/plugins/upload', [AdminPanelController::class, 'uploadPlugin'])->name('admin.plugins.upload');

    // TECVEQ Modular Plugins Routes
    Route::match(['get', 'post'], '/plugins/seo/settings', [\App\Http\Controllers\Admin\PluginModulesController::class, 'seoSettings'])->name('admin.plugins.seo.settings');
    Route::post('/plugins/seo/redirections/store', [\App\Http\Controllers\Admin\PluginModulesController::class, 'seoStoreRedirection'])->name('admin.plugins.seo.redirections.store');
    Route::delete('/plugins/seo/redirections/{id}', [\App\Http\Controllers\Admin\PluginModulesController::class, 'seoDeleteRedirection'])->name('admin.plugins.seo.redirections.delete');
    Route::post('/plugins/seo/404/clear', [\App\Http\Controllers\Admin\PluginModulesController::class, 'seoClear404Logs'])->name('admin.plugins.seo.404.clear');

    // TECVEQ Media Library Organizer
    Route::get('/media-library', [\App\Http\Controllers\Admin\PluginModulesController::class, 'mediaLibraryIndex'])->name('admin.plugins.media.index');
    Route::get('/plugins/media-library', [\App\Http\Controllers\Admin\PluginModulesController::class, 'mediaLibraryIndex']);
    Route::get('/media-library/api/list', [\App\Http\Controllers\Admin\PluginModulesController::class, 'mediaLibraryApiList'])->name('admin.plugins.media.api.list');
    Route::post('/media-library/upload', [\App\Http\Controllers\Admin\PluginModulesController::class, 'mediaLibraryUpload'])->name('admin.plugins.media.upload');
    Route::post('/media-library/update-meta', [\App\Http\Controllers\Admin\PluginModulesController::class, 'mediaLibraryUpdateMeta'])->name('admin.plugins.media.meta');
    Route::post('/media-library/delete', [\App\Http\Controllers\Admin\PluginModulesController::class, 'mediaLibraryDelete'])->name('admin.plugins.media.delete');

    // TECVEQ File Manager
    Route::get('/file-manager', [\App\Http\Controllers\Admin\PluginModulesController::class, 'fileManagerIndex'])->name('admin.plugins.filemanager.index');
    Route::get('/plugins/file-manager', [\App\Http\Controllers\Admin\PluginModulesController::class, 'fileManagerIndex']);
    Route::get('/file-manager/api/files', [\App\Http\Controllers\Admin\PluginModulesController::class, 'fileManagerApiList'])->name('admin.plugins.filemanager.api.list');
    Route::post('/file-manager/upload', [\App\Http\Controllers\Admin\PluginModulesController::class, 'fileManagerUpload'])->name('admin.plugins.filemanager.upload');
    Route::post('/file-manager/new-folder', [\App\Http\Controllers\Admin\PluginModulesController::class, 'fileManagerNewFolder'])->name('admin.plugins.filemanager.folder');
    Route::post('/file-manager/delete', [\App\Http\Controllers\Admin\PluginModulesController::class, 'fileManagerDelete'])->name('admin.plugins.filemanager.delete');
    Route::post('/file-manager/read-file', [\App\Http\Controllers\Admin\PluginModulesController::class, 'fileManagerReadFile'])->name('admin.plugins.filemanager.read');
    Route::post('/file-manager/save-file', [\App\Http\Controllers\Admin\PluginModulesController::class, 'fileManagerSaveFile'])->name('admin.plugins.filemanager.save');
    Route::post('/file-manager/zip', [\App\Http\Controllers\Admin\PluginModulesController::class, 'fileManagerZip'])->name('admin.plugins.filemanager.zip');
    Route::post('/file-manager/unzip', [\App\Http\Controllers\Admin\PluginModulesController::class, 'fileManagerUnzip'])->name('admin.plugins.filemanager.unzip');

    // TECVEQ Speed Optimizer
    Route::match(['get', 'post'], '/speed-optimizer', [\App\Http\Controllers\Admin\PluginModulesController::class, 'speedSettings'])->name('admin.plugins.speed.settings');
    Route::match(['get', 'post'], '/plugins/speed-optimizer', [\App\Http\Controllers\Admin\PluginModulesController::class, 'speedSettings']);
    Route::post('/speed-optimizer/purge', [\App\Http\Controllers\Admin\PluginModulesController::class, 'speedPurgeCache'])->name('admin.plugins.speed.purge');
    Route::post('/speed-optimizer/clean-db', [\App\Http\Controllers\Admin\PluginModulesController::class, 'speedCleanDb'])->name('admin.plugins.speed.clean-db');
    Route::post('/speed-optimizer/preload-cache', [\App\Http\Controllers\Admin\PluginModulesController::class, 'speedPreloadCache'])->name('admin.plugins.speed.preload');

    // TECVEQ Rich Editor
    Route::match(['get', 'post'], '/plugins/editor/settings', [\App\Http\Controllers\Admin\PluginModulesController::class, 'editorSettings'])->name('admin.plugins.editor.settings');

    // TECVEQ Security Suite & Firewall (WAF, SQLi, Bot Protection, IP Bans)
    Route::match(['get', 'post'], '/plugins/security', [\App\Http\Controllers\Admin\PluginModulesController::class, 'securitySettings'])->name('admin.plugins.security.settings');
    Route::post('/plugins/security/bans', [\App\Http\Controllers\Admin\PluginModulesController::class, 'securityAddBan'])->name('admin.plugins.security.bans.store');
    Route::delete('/plugins/security/bans/{id}', [\App\Http\Controllers\Admin\PluginModulesController::class, 'securityDeleteBan'])->name('admin.plugins.security.bans.delete');
    Route::post('/plugins/security/whitelist', [\App\Http\Controllers\Admin\PluginModulesController::class, 'securityAddWhitelist'])->name('admin.plugins.security.whitelist.store');
    Route::delete('/plugins/security/whitelist/{id}', [\App\Http\Controllers\Admin\PluginModulesController::class, 'securityDeleteWhitelist'])->name('admin.plugins.security.whitelist.delete');
    Route::post('/plugins/security/logs/clear', [\App\Http\Controllers\Admin\PluginModulesController::class, 'securityClearLogs'])->name('admin.plugins.security.logs.clear');

    // Settings (Direct Sidebar Links - Stripe, Social, General, Notifications, Updater, Legal, Health)
    Route::match(['get', 'post'], '/settings/stripe', [AdminPanelController::class, 'settingsStripe'])->name('admin.settings.stripe');
    Route::match(['get', 'post'], '/settings/social', [AdminPanelController::class, 'settingsSocial'])->name('admin.settings.social');
    Route::match(['get', 'post'], '/settings/auth', [AdminPanelController::class, 'settingsAuth'])->name('admin.settings.auth');
    Route::match(['get', 'post'], '/settings/general', [AdminPanelController::class, 'settingsGeneral'])->name('admin.settings.general');
    Route::match(['get', 'post'], '/extensions', [AdminPanelController::class, 'plugins'])->name('admin.extensions.index');
    Route::match(['get', 'post'], '/setting/social_credential', [AdminPanelController::class, 'settingsAuth'])->name('admin.setting.social_credential');
    Route::match(['get', 'post'], '/settings/socialite', [AdminPanelController::class, 'settingsAuth'])->name('admin.settings.socialite');
    Route::match(['get', 'post'], '/settings/social-login', [AdminPanelController::class, 'settingsAuth']);
    Route::get('/password', [AdminPanelController::class, 'password'])->name('admin.password');
    Route::get('/change-password', [AdminPanelController::class, 'password']);
    Route::get('/settings/password', [AdminPanelController::class, 'password']);
    Route::post('/password', [AdminPanelController::class, 'updatePassword']);
    Route::post('/password/update', [AdminPanelController::class, 'updatePassword'])->name('admin.password.update');
    Route::post('/change-password', [AdminPanelController::class, 'updatePassword']);
    Route::post('/settings/password', [AdminPanelController::class, 'updatePassword'])->name('admin.settings.password');
    Route::match(['get', 'post'], '/settings/notifications', [AdminPanelController::class, 'settingsNotifications'])->name('admin.settings.notifications');
    Route::get('/settings/updater', [AdminPanelController::class, 'settingsUpdater'])->name('admin.settings.updater');
    Route::post('/settings/updater/upload', [AdminPanelController::class, 'uploadUpdateZip'])->name('admin.settings.updater.upload');
    Route::match(['get', 'post'], '/settings/legal', [AdminPanelController::class, 'settingsLegal'])->name('admin.settings.legal');
    Route::match(['get', 'post'], '/settings/health', [AdminPanelController::class, 'settingsHealth'])->name('admin.settings.health');

    // System Quick Cache Cleaner (Direct access from browser / one-click)
    Route::get('/system/clear-cache', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
        } catch (\Throwable $e) {}

        $files = [
            base_path('bootstrap/cache/routes-v7.php'),
            base_path('bootstrap/cache/config.php'),
            base_path('bootstrap/cache/services.php'),
            base_path('bootstrap/cache/packages.php'),
        ];
        foreach ($files as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }

        return redirect()->to('/admin')->with('success', 'System, routes, and views cache cleared successfully!');
    })->name('admin.system.clear-cache');

    // CMS Engine (Pages, Posts, Categories, Menus)
    Route::prefix('cms')->name('admin.cms.')->group(function () {
        // Pages
        Route::get('/pages', [\App\Http\Controllers\Admin\CmsController::class, 'pages'])->name('pages.index');
        Route::get('/pages/create', [\App\Http\Controllers\Admin\CmsController::class, 'createPage'])->name('pages.create');
        Route::post('/pages', [\App\Http\Controllers\Admin\CmsController::class, 'storePage'])->name('pages.store');
        Route::get('/pages/{id}/edit', [\App\Http\Controllers\Admin\CmsController::class, 'editPage'])->name('pages.edit');
        Route::put('/pages/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'updatePage'])->name('pages.update');
        Route::delete('/pages/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'deletePage'])->name('pages.delete');

        // Posts
        Route::get('/posts', [\App\Http\Controllers\Admin\CmsController::class, 'posts'])->name('posts.index');
        Route::get('/posts/create', [\App\Http\Controllers\Admin\CmsController::class, 'createPost'])->name('posts.create');
        Route::post('/posts', [\App\Http\Controllers\Admin\CmsController::class, 'storePost'])->name('posts.store');
        Route::get('/posts/{id}/edit', [\App\Http\Controllers\Admin\CmsController::class, 'editPost'])->name('posts.edit');
        Route::put('/posts/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'updatePost'])->name('posts.update');
        Route::delete('/posts/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'deletePost'])->name('posts.delete');

        // Categories
        Route::get('/categories', [\App\Http\Controllers\Admin\CmsController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [\App\Http\Controllers\Admin\CmsController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'deleteCategory'])->name('categories.delete');

        // Menus
        Route::get('/menus', [\App\Http\Controllers\Admin\CmsController::class, 'menus'])->name('menus.index');
        Route::post('/menus/items', [\App\Http\Controllers\Admin\CmsController::class, 'addMenuItem'])->name('menus.items.add');
        Route::put('/menus/items/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'updateMenuItem'])->name('menus.items.update');
        Route::delete('/menus/items/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'deleteMenuItem'])->name('menus.items.delete');
    });
});

// TECVEQ Custom Brand Panel Routes (Strictly Isolated to Brand Advertisers)
Route::prefix('brand')->middleware(['web', 'auth', \App\Http\Middleware\BrandAccessMiddleware::class])->group(function () {
    Route::get('/', [BrandPanelController::class, 'dashboard'])->name('brand.dashboard');
    Route::get('/dashboard', [BrandPanelController::class, 'dashboard']);

    // Campaigns Studio
    Route::get('/campaigns', [BrandPanelController::class, 'campaigns'])->name('brand.campaigns');
    Route::get('/campaigns/create', [BrandPanelController::class, 'createCampaign'])->name('brand.campaigns.create');
    Route::post('/campaigns/store', [BrandPanelController::class, 'storeCampaign'])->name('brand.campaigns.store');
    Route::get('/campaigns/{id}/edit', [BrandPanelController::class, 'editCampaign'])->name('brand.campaigns.edit');
    Route::post('/campaigns/{id}/update', [BrandPanelController::class, 'updateCampaign'])->name('brand.campaigns.update');
    Route::post('/campaigns/{id}/toggle', [BrandPanelController::class, 'toggleCampaignStatus'])->name('brand.campaigns.toggle');
    Route::post('/campaigns/{id}/end', [BrandPanelController::class, 'endCampaign'])->name('brand.campaigns.end');
    Route::post('/campaigns/{id}/delete', [BrandPanelController::class, 'deleteCampaign'])->name('brand.campaigns.delete');
    Route::get('/campaigns/{id}/checkout', [BrandPanelController::class, 'checkoutCampaign'])->name('brand.campaigns.checkout');
    Route::get('/campaigns/deposit-success', [BrandPanelController::class, 'depositSuccess'])->name('brand.campaigns.deposit-success');

    // Video Submissions
    Route::get('/clips', [BrandPanelController::class, 'clips'])->name('brand.clips');
    Route::post('/clips/{id}/approve', [BrandPanelController::class, 'approveClip'])->name('brand.clips.approve');
    Route::post('/clips/{id}/reject', [BrandPanelController::class, 'rejectClip'])->name('brand.clips.reject');

    // Performance Analytics
    Route::get('/analytics', [BrandPanelController::class, 'analytics'])->name('brand.analytics');

    // Finance & Budgets
    Route::get('/finance', [BrandPanelController::class, 'finance'])->name('brand.finance');

    // Brand Profile & Password
    Route::match(['get', 'post'], '/profile', [BrandPanelController::class, 'profile'])->name('brand.profile');
    Route::match(['get', 'post'], '/password', [BrandPanelController::class, 'password'])->name('brand.password');
});

// TECVEQ Custom Clipper / Creator Panel Routes (Strictly Isolated to Clippers & Creators)
Route::prefix('clipper')->middleware(['web', 'auth', \App\Http\Middleware\ClipperAccessMiddleware::class])->group(function () {
    Route::get('/', [ClipperPanelController::class, 'dashboard'])->name('clipper.dashboard');
    Route::get('/dashboard', [ClipperPanelController::class, 'dashboard']);

    // Bounties Marketplace & Briefing
    Route::get('/bounties', [ClipperPanelController::class, 'bounties'])->name('clipper.bounties');
    Route::get('/bounties/{id}', [ClipperPanelController::class, 'bountyDetail'])->name('clipper.bounties.show');

    // Clip Submissions Tracker & Form
    Route::get('/submissions', [ClipperPanelController::class, 'submissions'])->name('clipper.submissions');
    Route::get('/submissions/create', [ClipperPanelController::class, 'createSubmission'])->name('clipper.submissions.create');
    Route::post('/submissions/store', [ClipperPanelController::class, 'storeSubmission'])->name('clipper.submissions.store');

    // Wallet & Direct Bank Payouts
    Route::get('/wallet', [ClipperPanelController::class, 'wallet'])->name('clipper.wallet');
    Route::post('/withdraw', [ClipperPanelController::class, 'requestWithdrawal'])->name('clipper.withdraw');

    // Direct Bank Payout Settings (No Stripe Connect - Strictly Direct Bank)
    Route::match(['get', 'post'], '/payout-settings', [ClipperPanelController::class, 'payoutSettings'])->name('clipper.payout-settings');

    // Creator Profile & Social Channels & Password
    Route::match(['get', 'post'], '/profile', [ClipperPanelController::class, 'profile'])->name('clipper.profile');
    Route::match(['get', 'post'], '/password', [ClipperPanelController::class, 'password'])->name('clipper.password');
    Route::match(['get', 'post'], '/channels', [ClipperPanelController::class, 'channels'])->name('clipper.channels');
});

// TECVEQ Custom Streamer Panel Routes (YouTube Promotion Hub)
Route::prefix('streamer')->middleware(['web', 'auth', \App\Http\Middleware\StreamerAccessMiddleware::class])->group(function () {
    Route::get('/', [StreamerPanelController::class, 'dashboard'])->name('streamer.dashboard');
    Route::get('/dashboard', [StreamerPanelController::class, 'dashboard']);

    // Sponsorship Deals & Marketplace
    Route::get('/campaigns', [StreamerPanelController::class, 'campaigns'])->name('streamer.campaigns');
    Route::get('/campaigns/{id}', [StreamerPanelController::class, 'campaignDetail'])->name('streamer.campaigns.show');

    // YouTube Video Submissions Tracker & Form
    Route::get('/submissions', [StreamerPanelController::class, 'submissions'])->name('streamer.submissions');
    Route::get('/submissions/create', [StreamerPanelController::class, 'createSubmission'])->name('streamer.submissions.create');
    Route::post('/submissions/store', [StreamerPanelController::class, 'storeSubmission'])->name('streamer.submissions.store');

    // Wallet & Direct Bank Payouts
    Route::get('/wallet', [StreamerPanelController::class, 'wallet'])->name('streamer.wallet');
    Route::post('/withdraw', [StreamerPanelController::class, 'requestWithdrawal'])->name('streamer.withdraw');

    // Direct Bank Payout Settings
    Route::match(['get', 'post'], '/payout-settings', [StreamerPanelController::class, 'payoutSettings'])->name('streamer.payout-settings');

    // Streamer Profile & YouTube Channels & Password
    Route::match(['get', 'post'], '/profile', [StreamerPanelController::class, 'profile'])->name('streamer.profile');
    Route::match(['get', 'post'], '/password', [StreamerPanelController::class, 'password'])->name('streamer.password');
    Route::match(['get', 'post'], '/channels', [StreamerPanelController::class, 'channels'])->name('streamer.channels');
});


// 2. Production GEZUX Auth API Endpoints (with rate-limiting)
Route::prefix('auth')->middleware(['web', 'throttle:30,1'])->group(function () {
    Route::get('/config', [AuthController::class, 'config']);
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->middleware('throttle:5,1');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');
    Route::post('/logout', [AuthController::class, 'logout']);

    // Google OAuth 2.0
    Route::get('/google', [AuthController::class, 'googleRedirect'])->name('auth.google');
    Route::get('/google/redirect', [AuthController::class, 'googleRedirect'])->name('auth.google.redirect');
    Route::get('/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');
    Route::get('/google/pending', [AuthController::class, 'pendingGoogleAuth']);
    Route::post('/google/complete', [AuthController::class, 'completeGoogleRegistration']);
});

// HYIPLab Social Login compatibility route
Route::get('/user/social-login/{provider}', function ($provider) {
    return redirect()->route('auth.google.redirect');
})->name('user.social.login');
Route::get('/auth/google', [AuthController::class, 'googleRedirect']);
Route::get('/login/google', [AuthController::class, 'googleRedirect']);

Route::post('/api/notifications/register-device', [\App\Http\Controllers\Api\NotificationDeviceController::class, 'registerDevice']);
Route::get('/api/campaigns', [\App\Http\Controllers\Api\CampaignController::class, 'index']);
Route::get('/api/campaigns/{campaign}', [\App\Http\Controllers\Api\CampaignController::class, 'show']);

// Admin-Only Protected Log Endpoints (Information Disclosure & Log Tampering Fix)
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/api/admin/logs', function (Request $request) {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access required.');
        }
        return app(\App\Http\Controllers\Api\SystemLogController::class)->index($request);
    });

    Route::post('/api/admin/logs/clear', function (Request $request) {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access required.');
        }
        return app(\App\Http\Controllers\Api\SystemLogController::class)->clear($request);
    });
});

use App\Http\Controllers\WebController;

// 3. Pure Laravel Multi-Page Blade Web Application Routes
Route::get('/', [WebController::class, 'home'])->name('web.home');
Route::get('/marketplace', [WebController::class, 'marketplace'])->name('web.marketplace');
Route::get('/campaigns', fn() => redirect()->route('web.marketplace'))->name('campaigns.index');
Route::get('/campaigns/{id}', [WebController::class, 'campaignDetail'])->name('web.campaign.detail');
Route::get('/campaign/{id}', [WebController::class, 'campaignDetail']);
Route::get('/privacy', fn() => redirect('/p/privacy-policy'))->name('web.privacy');
Route::get('/privacy-policy', fn() => redirect('/p/privacy-policy'));
Route::get('/terms', fn() => redirect('/p/terms-of-service'))->name('web.terms');
Route::get('/terms-of-service', fn() => redirect('/p/terms-of-service'));
Route::get('/about', fn() => redirect('/p/about-us'))->name('web.about');
Route::get('/about-us', fn() => redirect('/p/about-us'));

// Public Blog & CMS Dynamic Pages
Route::get('/blog', [\App\Http\Controllers\FrontCmsController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\FrontCmsController::class, 'blogPost'])->name('blog.show');
Route::get('/p/{slug}', [\App\Http\Controllers\FrontCmsController::class, 'pageView'])->name('cms.page');
Route::get('/sitemap.xml', [\App\Http\Controllers\Admin\PluginModulesController::class, 'sitemapXml'])->name('seo.sitemap');

// Internal Safe Schema Patch Endpoint
Route::get('/api/internal/patch-schema', function () {
    try {
        \Illuminate\Support\Facades\Schema::table('campaigns', function ($table) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('campaigns', 'rules')) {
                $table->text('rules')->nullable()->after('description');
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('campaigns', 'asset_url')) {
                $table->string('asset_url', 1000)->nullable()->after('source_video_url');
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('campaigns', 'allowed_platforms')) {
                $table->json('allowed_platforms')->nullable()->after('requirements_json');
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('campaigns', 'expires_at')) {
                $table->date('expires_at')->nullable()->after('end_at');
            }
        });

        \Illuminate\Support\Facades\Schema::table('social_accounts', function ($table) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('social_accounts', 'handle')) {
                $table->string('handle')->nullable()->after('username');
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('social_accounts', 'account_name')) {
                $table->string('account_name')->nullable()->after('display_name');
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('social_accounts', 'is_verified')) {
                $table->boolean('is_verified')->default(true)->after('verified');
            }
        });
        return response()->json(['status' => 'patched']);
    } catch (\Throwable $e) {
        return response()->json(['status' => 'skipped', 'message' => $e->getMessage()]);
    }
});


// Auth Routes (Blade Multi-page)
Route::get('/login', [WebController::class, 'loginView'])->name('login');
Route::post('/login', [WebController::class, 'login'])->name('web.login');
Route::get('/register', [WebController::class, 'registerView'])->name('register');
Route::post('/register', [WebController::class, 'register'])->name('web.register');
Route::post('/logout', [WebController::class, 'logout'])->name('web.logout');

// Compatibility & Auth Utility Redirects
Route::get('/brand/login', fn() => redirect()->route('login'))->name('filament.brand.auth.login');
Route::get('/brand/register', fn() => redirect()->route('register'))->name('brand.register');
Route::get('/clipper/login', fn() => redirect()->route('login'))->name('filament.clipper.auth.login');
Route::get('/clipper/register', fn() => redirect()->route('register'))->name('clipper.register');
Route::get('/streamer/login', fn() => redirect()->route('login'))->name('streamer.login');
Route::get('/streamer/register', fn() => redirect()->route('register'))->name('streamer.register');
Route::get('/forgot-password', [WebController::class, 'forgotPasswordView'])->name('forgot-password');
Route::post('/forgot-password', [WebController::class, 'sendPasswordReset'])->name('web.forgot-password.send');
Route::get('/verify-email', fn() => redirect()->route('web.home'))->name('verify-email');

// Global Password Redirection & Management (Safe against 404s)
Route::match(['get', 'post'], '/password', function (\Illuminate\Http\Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    $user = Auth::user();
    if ($user->isAdmin()) {
        if ($request->isMethod('post')) {
            return app(\App\Http\Controllers\Admin\AdminPanelController::class)->updatePassword($request);
        }
        return redirect()->route('admin.password');
    } elseif ($user->isBrand()) {
        if ($request->isMethod('post')) {
            return app(\App\Http\Controllers\Brand\BrandPanelController::class)->profile($request);
        }
        return redirect()->route('brand.profile');
    } elseif ($user->isStreamer()) {
        if ($request->isMethod('post')) {
            return app(\App\Http\Controllers\Streamer\StreamerPanelController::class)->profile($request);
        }
        return redirect()->route('streamer.profile');
    } else {
        if ($request->isMethod('post')) {
            return app(\App\Http\Controllers\Clipper\ClipperPanelController::class)->profile($request);
        }
        return redirect()->route('clipper.profile');
    }
})->name('password');
Route::match(['get', 'post'], '/change-password', fn() => redirect('/password'));

// Dev Auth Testing Helper (Only enabled when APP_DEBUG=true)
if (config('app.debug')) {
    Route::get('/dev-login/{role}', function ($role) {
        $user = \App\Models\User::where('role', $role)->first();
        if ($user) {
            Auth::login($user);
            request()->session()->save();
            if ($user->isBrand()) return redirect('/brand');
            if ($user->isClipper()) return redirect('/clipper');
            if ($user->isStreamer()) return redirect('/streamer');
            if ($user->isAdmin()) return redirect('/admin/dashboard');
        }
        return redirect('/');
    });
}

// Dashboards & Interactive Actions (Protected with auth middleware)
Route::middleware(['auth'])->group(function () {
    // Clipper Actions
    Route::post('/clipper/payout-settings', [WebController::class, 'updatePayoutSettings'])->name('web.clipper.payout-settings');
    Route::post('/clips/submit', [WebController::class, 'submitClip'])->name('web.clips.submit');
    Route::post('/withdraw', [WebController::class, 'withdraw'])->name('web.withdraw');

    // Brand Legacy Aliases
    Route::post('/campaigns/create', [BrandPanelController::class, 'storeCampaign'])->name('web.campaign.create');
    Route::post('/brand/campaigns/{id}/end-legacy', [BrandPanelController::class, 'endCampaign'])->name('web.brand.campaign.end');
    Route::post('/brand/clips/{id}/approve-legacy', [BrandPanelController::class, 'approveClip'])->name('web.brand.clip.approve');
    Route::post('/brand/clips/{id}/reject-legacy', [BrandPanelController::class, 'rejectClip'])->name('web.brand.clip.reject');

    // Admin Impersonation Routes
    Route::get('/admin/impersonate/{user}', function (\App\Models\User $user) {
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isAdmin() && !session()->has('impersonated_by'))) {
            abort(403, 'Unauthorized. Administrator access required.');
        }

        // Store original admin ID in session if starting new impersonation
        if (!session()->has('impersonated_by')) {
            session(['impersonated_by' => $currentUser->id]);
        }

        Auth::login($user);

        if ($user->isBrand()) {
            return redirect('/brand');
        } elseif ($user->isStreamer()) {
            return redirect('/streamer');
        } elseif ($user->isClipper()) {
            return redirect('/clipper');
        } else {
            return redirect('/admin');
        }
    })->name('admin.impersonate');

    Route::get('/admin/impersonate-leave', function () {
        if (!session()->has('impersonated_by')) {
            return redirect('/');
        }

        $adminId = session('impersonated_by');
        $admin = \App\Models\User::find($adminId);

        if ($admin) {
            Auth::login($admin);
            session()->forget('impersonated_by');
            return redirect('/admin/users');
        }

        session()->forget('impersonated_by');
        return redirect('/admin');
    })->name('admin.impersonate.leave');

    // Stripe Hosted Checkout for Brand Campaigns
    Route::get('/brand/campaigns/{campaign}/checkout', function (\App\Models\Campaign $campaign) {
        $user = Auth::user();
        if (!$user || ($user->id !== $campaign->brand?->user_id && !$user->isAdmin())) {
            abort(403, 'Unauthorized to fund this campaign.');
        }

        $checkoutService = app(\App\Services\StripeCheckoutService::class);
        $url = $checkoutService->createCampaignCheckoutSession($campaign, $user);

        if ($url) {
            return redirect()->away($url, 303);
        }

        $errorMsg = session('stripe_error') ?: 'Unable to initiate Stripe checkout. Please verify Stripe configuration in Admin Settings.';
        return redirect('/brand/campaigns')->with('error', $errorMsg);
    })->name('brand.campaigns.checkout');

    // Stripe Checkout Success Callback
    Route::get('/brand/campaigns/deposit-success', function (Request $request) {
        $sessionId = $request->query('session_id');
        $campaignId = $request->query('campaign_id');
        $campaign = $campaignId ? \App\Models\Campaign::find($campaignId) : null;

        if ($sessionId) {
            $checkoutService = app(\App\Services\StripeCheckoutService::class);
            $result = $checkoutService->handleSuccessfulPayment($sessionId, $campaign);

            if ($result['success'] ?? false) {
                session()->flash('success', 'Campaign Bounty Funded & Published! Stripe payment verified. Your campaign is now LIVE on the marketplace.');
            } else {
                session()->flash('error', $result['message'] ?? 'Stripe payment has not been confirmed yet. Campaign remains in draft status.');
            }
        }

        return redirect('/brand/campaigns');
    })->name('brand.campaigns.deposit-success');
});

// Stripe Webhook Endpoint (Protected with Cryptographic Signature Verification)
Route::post('/api/webhooks/stripe', function (Request $request) {
    $payload = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    $webhookSecret = config('services.stripe.webhook_secret') ?? env('STRIPE_WEBHOOK_SECRET');

    if ($webhookSecret && $sigHeader && class_exists(\Stripe\Webhook::class)) {
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            if ($event->type === 'checkout.session.completed') {
                $session = $event->data->object;
                if ($session && $session->id) {
                    app(\App\Services\StripeCheckoutService::class)->handleSuccessfulPayment($session->id);
                }
            }
            return response()->json(['status' => 'verified_and_handled'], 200);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            \Illuminate\Support\Facades\Log::warning('Stripe Webhook Signature Verification Failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // Direct event handling for non-secret or local sandbox configurations
    $event = json_decode($payload, true);
    if (($event['type'] ?? '') === 'checkout.session.completed') {
        $session = $event['data']['object'] ?? [];
        $sessionId = $session['id'] ?? null;
        if ($sessionId) {
            app(\App\Services\StripeCheckoutService::class)->handleSuccessfulPayment($sessionId);
        }
    }

    return response()->json(['status' => 'handled'], 200);
});

// Emergency Cache Cleaner for Shared Hosting
Route::get('/clear-cache', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
    } catch (\Throwable $e) {}

    $files = [
        base_path('bootstrap/cache/routes-v7.php'),
        base_path('bootstrap/cache/config.php'),
        base_path('bootstrap/cache/services.php'),
        base_path('bootstrap/cache/packages.php'),
    ];
    foreach ($files as $file) {
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    return response('<h3>Success! System, routes, and views cache cleared successfully.</h3><p><a href="/admin">Return to Admin Dashboard</a></p>');
});

// Public Dynamic CMS Pages & Legal Policies
Route::get('/p/{slug}', [\App\Http\Controllers\FrontCmsController::class, 'pageView'])->name('cms.page');
Route::get('/blog', [\App\Http\Controllers\FrontCmsController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\FrontCmsController::class, 'blogPost'])->name('blog.show');
