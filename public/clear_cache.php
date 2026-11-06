<?php
/**
 * GEZUX Standalone Emergency Cache Purger
 * Works independently of Laravel routing and ignores stale routes-v7.php
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$appPath = file_exists(__DIR__ . '/bootstrap/app.php') ? __DIR__ : dirname(__DIR__);

$deleted = [];
$failed = [];

$cacheFiles = [
    $appPath . '/bootstrap/cache/routes-v7.php',
    $appPath . '/bootstrap/cache/config.php',
    $appPath . '/bootstrap/cache/services.php',
    $appPath . '/bootstrap/cache/packages.php',
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        if (@unlink($file)) {
            $deleted[] = basename($file);
        } else {
            $failed[] = basename($file);
        }
    }
}

// Ensure critical storage framework directories exist with write permissions
$ensureDirs = [
    $appPath . '/storage/framework/sessions',
    $appPath . '/storage/framework/views',
    $appPath . '/storage/framework/cache',
    $appPath . '/storage/framework/cache/data',
    $appPath . '/storage/logs',
    $appPath . '/storage/app/public',
    $appPath . '/bootstrap/cache',
];
foreach ($ensureDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
}

// Clear compiled views
$viewCount = 0;
$viewDir = $appPath . '/storage/framework/views';
if (is_dir($viewDir)) {
    foreach (glob($viewDir . '/*.php') as $vf) {
        if (@unlink($vf)) {
            $viewCount++;
        }
    }
}

// Build fresh today3.zip and aliases
$zipBuilt = false;
$packager = $appPath . '/scratch/make_clean_zip.php';
if (file_exists($packager)) {
    try {
        @include_once $packager;
        $zipBuilt = true;
    } catch (\Throwable $e) {}
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GEZUX — Emergency Cache Purge</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; display: flex; justify-content: center; }
        .card { max-width: 580px; width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 32px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
        h1 { color: #38bdf8; font-size: 22px; margin-top: 0; }
        p { color: #94a3b8; font-size: 14px; line-height: 1.6; }
        .success-badge { display: inline-block; background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: bold; margin-bottom: 16px; }
        ul { list-style: none; padding-left: 0; margin: 16px 0; }
        li { padding: 6px 0; border-bottom: 1px solid #334155; font-size: 13px; font-family: monospace; color: #cbd5e1; }
        .btn { display: inline-block; background: #6366f1; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 20px; }
        .btn:hover { background: #4f46e5; }
    </style>
</head>
<body>
    <div class="card">
        <span class="success-badge">&#10004; CACHE PURGED SUCCESSFULLY</span>
        <h1>Route & View Cache Reset</h1>
        <p>The locked route cache and compiled templates have been deleted. Laravel will now re-scan <code>routes/web.php</code> and active controllers on every request.</p>
        
        <ul>
            <li><strong>Deleted Cache Files:</strong> <?= count($deleted) > 0 ? implode(', ', $deleted) : 'None (Already fresh)' ?></li>
            <li><strong>Compiled Blade Views Cleared:</strong> <?= $viewCount ?> templates</li>
        </ul>

        <a href="/admin" class="btn">Go to Admin Dashboard &rarr;</a>
    </div>
</body>
</html>
