<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// In single public_html root architecture, app root and public path are the same directory
$appPath = __DIR__;
$publicPath = __DIR__;

// 1. Ensure APP_KEY exists if .env is present, or self-heal immediately
$envFile = $appPath . '/.env';
if (file_exists($envFile)) {
    $envContent = @file_get_contents($envFile) ?: '';
    if (!preg_match('/^APP_KEY=(base64:[A-Za-z0-9+\/]{40,}|[A-Za-z0-9]{32})/m', $envContent)) {
        $generatedKey = 'base64:' . base64_encode(random_bytes(32));
        if (preg_match('/^APP_KEY=.*/m', $envContent)) {
            $envContent = preg_replace('/^APP_KEY=.*/m', 'APP_KEY=' . $generatedKey, $envContent);
        } else {
            $envContent .= "\nAPP_KEY=" . $generatedKey . "\n";
        }
        @file_put_contents($envFile, $envContent);
        putenv('APP_KEY=' . $generatedKey);
        $_ENV['APP_KEY'] = $generatedKey;
        $_SERVER['APP_KEY'] = $generatedKey;
    }
}

// Emergency Cache Purge Interceptor (Works even when route cache is stale or locked)
$reqUri = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($reqUri, 'clear-cache') !== false || isset($_GET['clear_cache']) || isset($_GET['purge_cache'])) {
    if (file_exists($appPath . '/clear_cache.php')) {
        require_once $appPath . '/clear_cache.php';
        exit;
    }
}

// 2. Check if installation is needed (Only locked once installation completes Step 6)
$isInstalled = (file_exists($appPath . '/storage/installed.lock') || file_exists($appPath . '/installed.lock'))
    && file_exists($envFile)
    && !file_exists($appPath . '/storage/installer_pending.flag');

if (!$isInstalled && file_exists(__DIR__ . '/installer/boot.php')) {
    require_once __DIR__ . '/installer/boot.php';
    exit;
}

// 2. Maintenance mode check
if (file_exists($maintenance = $appPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// 3. Composer Autoloader
if (!file_exists($appPath . '/vendor/autoload.php')) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>GEZUX — Missing Vendor</title><style>body{font-family:system-ui,-apple-system,sans-serif;background:#0b0b10;color:#f3f4f6;padding:40px 20px;display:flex;justify-content:center}.card{max-width:640px;background:#13131a;border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:32px;box-shadow:0 20px 40px rgba(0,0,0,0.6)}h1{font-size:20px;margin:0 0 12px;color:#f87171}p{color:#9ca3af;font-size:14px;line-height:1.6}code{background:#1e1e2d;color:#a78bfa;padding:2px 6px;border-radius:4px}</style></head><body><div class="card"><h1>Application Core Not Found</h1><p>Expected <code>vendor/autoload.php</code> at <code>' . htmlspecialchars($appPath) . '</code> was not found.<br><br>Please ensure the production ZIP was extracted directly inside <code>public_html/</code>.</p></div></body></html>';
    exit;
}

require $appPath . '/vendor/autoload.php';

// 4. Bootstrap Laravel Application with public path awareness
$app = require_once $appPath . '/bootstrap/app.php';

if (method_exists($app, 'usePublicPath')) {
    $app->usePublicPath($publicPath);
}

$app->handleRequest(Request::capture());
