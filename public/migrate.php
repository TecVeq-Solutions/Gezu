<?php

/**
 * GEZUX 1-Click Database Migration & Setup Utility
 * Run directly in browser to initialize all database tables and system seeds.
 */

ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);


define('LARAVEL_START', microtime(true));

$appPath = __DIR__;

// 1. Ensure framework storage directories exist
$dirs = [
    $appPath . '/storage/framework/views',
    $appPath . '/storage/framework/cache',
    $appPath . '/storage/framework/cache/data',
    $appPath . '/storage/framework/sessions',
    $appPath . '/storage/logs',
    $appPath . '/storage/app/public',
    $appPath . '/bootstrap/cache',
];
foreach ($dirs as $d) {
    if (!is_dir($d)) {
        @mkdir($d, 0775, true);
    }
}

// 2. Load Autoloader & Bootstrap Kernel
if (!file_exists($appPath . '/vendor/autoload.php')) {
    die('<div style="font-family:sans-serif;padding:30px;background:#fee2e2;color:#991b1b;border-radius:10px;">❌ Error: vendor/autoload.php not found.</div>');
}

require_once $appPath . '/vendor/autoload.php';
$app = require_once $appPath . '/bootstrap/app.php';

if (method_exists($app, 'usePublicPath')) {
    $app->usePublicPath($appPath);
}

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$output = '';
$error = '';
$success = false;

// Check if trigger button was clicked or run automatically
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['run'])) {
    try {
        $buffer = new \Symfony\Component\Console\Output\BufferedOutput();
        
        // 1. Run migrations
        $exitCode = $kernel->call('migrate', ['--force' => true], $buffer);
        $output .= $buffer->fetch();

        // 2. Run seeders
        $seedBuffer = new \Symfony\Component\Console\Output\BufferedOutput();
        $kernel->call('db:seed', ['--force' => true], $seedBuffer);
        $output .= "\n--- SEEDERS ---\n" . $seedBuffer->fetch();

        // 3. Ensure Default Admin Exists
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@gezux.com'],
            [
                'name' => 'GEZUX Super Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $output .= "\n✓ Super Admin Account Created: admin@gezux.com / password\n";
        $success = true;
    } catch (\Throwable $e) {
        $error = $e->getMessage() . "\n\n" . $e->getTraceAsString();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GEZUX — Database Setup & Migrations</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #0b0b10;
            color: #f3f4f6;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }
        .container {
            max-width: 680px;
            width: 100%;
            background: #13131a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.8);
        }
        h1 {
            font-size: 24px;
            margin: 0 0 8px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: #7c3aed;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 20px;
        }
        p {
            color: #9ca3af;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .db-info {
            background: #181824;
            border-left: 4px solid #7c3aed;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 13px;
        }
        .db-info div { margin-bottom: 6px; }
        .db-info strong { color: #d1d5db; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #7c3aed;
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            padding: 14px 28px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            width: 100%;
            box-sizing: border-box;
        }
        .btn:hover { background: #6d28d9; }
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
        }
        .alert-success { background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; color: #4ade80; }
        .alert-danger { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #f87171; }
        pre {
            background: #09090d;
            border: 1px solid rgba(255,255,255,0.06);
            padding: 16px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            color: #a78bfa;
            white-space: pre-wrap;
            max-height: 280px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><div class="logo-icon">G</div> GEZUX Database Setup</h1>
        <p>This utility initializes all MySQL database tables, roles, seeders, and creates the primary Super Admin account.</p>

        <div class="db-info">
            <div><strong>Active DB Connection:</strong> <?= htmlspecialchars(config('database.default')) ?></div>
            <div><strong>Host:</strong> <?= htmlspecialchars(config('database.connections.mysql.host')) ?></div>
            <div><strong>Database Name:</strong> <?= htmlspecialchars(config('database.connections.mysql.database')) ?></div>
            <div><strong>Username:</strong> <?= htmlspecialchars(config('database.connections.mysql.username')) ?></div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>✓ Success!</strong> All database tables and seeders have been created successfully.
            </div>
            <pre><?= htmlspecialchars($output) ?></pre>
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <a href="/admin/login" class="btn">Go to Admin Panel (/admin/login) &rarr;</a>
            </div>
            <p style="margin-top: 16px; font-size: 12px; color: #6b7280; text-align: center;">
                Security Note: Please delete <code>migrate.php</code> from <code>public_html/</code> after setup.
            </p>
        <?php elseif ($error): ?>
            <div class="alert alert-danger">
                <strong>Database Error:</strong> Please check your MySQL database credentials in <code>.env</code>.
            </div>
            <pre><?= htmlspecialchars($error) ?></pre>
            <form method="POST" style="margin-top: 16px;">
                <button type="submit" class="btn">Retry Database Migration</button>
            </form>
        <?php else: ?>
            <form method="POST">
                <button type="submit" class="btn">⚡ Run Database Migrations & Seeds</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
