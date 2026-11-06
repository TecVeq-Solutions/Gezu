<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Exception;
use Throwable;

class BuildHostingerPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gezux:build-hostinger-package {--skip-build : Skip running npm build} {--output= : Custom output zip path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build a single public_html root Hostinger shared-hosting deployment package with fail-safe web installer';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('============================================================');
        $this->info('   GEZUX HOSTINGER SINGLE-ROOT DEPLOYMENT BUILDER');
        $this->info('============================================================');

        $basePath = base_path();
        $timestamp = date('Ymd-His');
        $laravelVersion = app()->version();
        $releaseVersion = "1.0.0-hostinger-{$timestamp}";
        $deploymentsDir = $basePath . '/deployments';
        $stagingDir = $basePath . '/storage/app/deployment_staging';

        if (!is_dir($deploymentsDir)) {
            @mkdir($deploymentsDir, 0755, true);
        }

        // 1. Audit Source Files
        $this->info("\n[1/8] Auditing Source Repository & Dependencies (Laravel {$laravelVersion})...");
        if (!file_exists($basePath . '/vendor/autoload.php')) {
            $this->error('BLOCKER: vendor/autoload.php not found. Run composer install first.');
            return 1;
        }

        if (!file_exists($basePath . '/installer/boot.php')) {
            $this->error('BLOCKER: installer/boot.php not found.');
            return 1;
        }

        // 2. Production Frontend Build
        $this->info("\n[2/8] Verifying Production Frontend Build (React/Inertia/Vite)...");
        if (!$this->option('skip-build')) {
            $this->line('Running npm run build...');
            $npmOutput = shell_exec('npm.cmd run build 2>&1') ?: shell_exec('npm run build 2>&1');
            $this->line(substr($npmOutput ?: 'Build completed.', 0, 300) . '...');
        }

        $viteManifest = $basePath . '/public/build/manifest.json';
        if (!file_exists($viteManifest)) {
            $this->error('BLOCKER: public/build/manifest.json missing. Frontend compilation failed.');
            return 1;
        }
        $this->info('✓ Vite production build verified.');

        // 3. Security Audit: Scan Built Assets for Secret Leaks
        $this->info("\n[3/8] Performing Security Audit & Secret Leak Scan...");
        $secretScanPassed = $this->scanPublicAssetsForSecrets($basePath . '/public/build');
        if (!$secretScanPassed) {
            $this->error('SECURITY BLOCKER: Potential secret detected in public assets. Build halted.');
            return 1;
        }
        $this->info('✓ Security audit passed: No private credentials detected in compiled frontend assets.');

        // 4. Assemble Single public_html Root Staging Directory
        $this->info("\n[4/7] Assembling Single public_html Root Directory Architecture...");
        $this->cleanDirectory($stagingDir);
        @mkdir($stagingDir, 0755, true);

        // Copy Application Core (app, bootstrap, config, database, resources, routes, vendor)
        $coreDirs = ['app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'vendor'];
        foreach ($coreDirs as $dir) {
            if (is_dir($basePath . '/' . $dir)) {
                $this->copyDirectory($basePath . '/' . $dir, $stagingDir . '/' . $dir);
            }
        }

        @copy($basePath . '/artisan', $stagingDir . '/artisan');
        @copy($basePath . '/composer.json', $stagingDir . '/composer.json');
        if (file_exists($basePath . '/composer.lock')) {
            @copy($basePath . '/composer.lock', $stagingDir . '/composer.lock');
        }

        // Storage structure
        @mkdir($stagingDir . '/storage/app/public', 0755, true);
        @mkdir($stagingDir . '/storage/framework/cache', 0755, true);
        @mkdir($stagingDir . '/storage/framework/sessions', 0755, true);
        @mkdir($stagingDir . '/storage/framework/views', 0755, true);
        @mkdir($stagingDir . '/storage/logs', 0755, true);
        @mkdir($stagingDir . '/bootstrap/cache', 0755, true);

        // Copy Public Assets & Public Directory Files into Staging Root
        $this->copyDirectory($basePath . '/public/build', $stagingDir . '/build');
        if (is_dir($basePath . '/public/css')) {
            $this->copyDirectory($basePath . '/public/css', $stagingDir . '/css');
        }
        if (is_dir($basePath . '/public/js')) {
            $this->copyDirectory($basePath . '/public/js', $stagingDir . '/js');
        }
        if (is_dir($basePath . '/public/images')) {
            $this->copyDirectory($basePath . '/public/images', $stagingDir . '/images');
        }

        $publicFiles = [
            'favicon.ico',
            'favicon.jpg',
            'robots.txt',
            'manifest.webmanifest',
            'firebase-messaging-sw.js',
        ];

        foreach ($publicFiles as $pf) {
            if (file_exists($basePath . '/public/' . $pf)) {
                @copy($basePath . '/public/' . $pf, $stagingDir . '/' . $pf);
            }
        }

        // Copy Standalone Web Installer
        $this->copyDirectory($basePath . '/installer', $stagingDir . '/installer');

        // Copy Root Bootstrap & Hardened Single-Root .htaccess
        @copy($basePath . '/public/index.php', $stagingDir . '/index.php');
        @copy($basePath . '/public/.htaccess', $stagingDir . '/.htaccess');

        // Bundle production-ready .env with APP_DEBUG=false & MySQL defaults
        $envSource = file_exists($basePath . '/.env') ? $basePath . '/.env' : $basePath . '/.env.example';
        $envContent = file_get_contents($envSource);
        $envContent = preg_replace('/^APP_DEBUG=.*$/m', 'APP_DEBUG=false', $envContent);
        $envContent = preg_replace('/^APP_ENV=.*$/m', 'APP_ENV=production', $envContent);
        if (!str_contains($envContent, 'DB_CONNECTION=')) {
            $envContent .= "\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\n";
        }
        file_put_contents($stagingDir . '/.env', $envContent);

        // Set installer pending flag
        file_put_contents($stagingDir . '/storage/installer_pending.flag', date('Y-m-d H:i:s'));

        // 5. Generate Manifests & Metadata
        $this->info("\n[5/7] Generating Release Manifests & Metadata...");
        $manifest = [
            'app_name' => 'GEZUX',
            'version' => $releaseVersion,
            'laravel_version' => $laravelVersion,
            'build_timestamp' => date('c'),
            'target' => 'Hostinger Shared Hosting (Single public_html Root)',
            'php_required' => '>= 8.3.0',
            'bundled_env' => file_exists($basePath . '/.env') ? 'real_env_included' : 'example_env',
            'frontend_build' => 'compiled_vite_production',
            'installer' => 'gezux_standalone_failsafe_installer',
            'modules' => [
                'admin_panel' => '/admin',
                'brand_panel' => '/brand',
                'clipper_panel' => '/clipper',
                'marketplace' => true,
                'payout_ledger' => true,
                'system_updater' => true,
                'plugin_manager' => true,
            ],
        ];

        file_put_contents($stagingDir . '/storage/deployment-manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
        file_put_contents($stagingDir . '/storage/release-version.txt', $releaseVersion);

        // 6. Build Deployment ZIP Archive
        $this->info("\n[6/7] Compressing Single-Root Deployment Package into Standard PKZIP Archive...");
        $zipFilename = $this->option('output') ?: "{$deploymentsDir}/gezux-hostinger-production-{$timestamp}.zip";
        $latestZipFilename = "{$deploymentsDir}/gezux-hostinger-production.zip";

        if (file_exists($zipFilename)) @unlink($zipFilename);

        $zip = new ZipArchive();
        if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error("Failed to create zip at {$zipFilename}");
            return 1;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($stagingDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $stagingReal = realpath($stagingDir);
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $rel = substr($filePath, strlen($stagingReal) + 1);
                $zip->addFile($filePath, str_replace('\\', '/', $rel));
            }
        }

        $zip->close();
        @copy($zipFilename, $latestZipFilename);

        $zipSizeMb = round(filesize($zipFilename) / (1024 * 1024), 2);
        $this->info("✓ Primary Package Generated: {$zipFilename} ({$zipSizeMb} MB)");
        $this->info("✓ Latest Package: {$latestZipFilename}");

        // Clean staging
        $this->cleanDirectory($stagingDir);

        // 7. Verify ZIP Integrity & Single-Root Structure
        $this->info("\n[7/7] Verifying ZIP File Integrity & Single-Root Structure...");
        $testZip = new ZipArchive();
        if ($testZip->open($zipFilename) === true) {
            $requiredInZip = [
                'index.php',
                '.htaccess',
                'installer/boot.php',
                'build/manifest.json',
                'artisan',
                'vendor/autoload.php',
                'bootstrap/app.php',
                '.env',
                'app/Http/Controllers/Controller.php',
                'routes/web.php',
                'config/app.php',
            ];

            foreach ($requiredInZip as $item) {
                if ($testZip->locateName($item) === false && $testZip->locateName('./' . $item) === false) {
                    $this->error("INTEGRITY FAILURE: Missing {$item} inside ZIP root.");
                    $testZip->close();
                    return 1;
                }
            }

            $forbiddenPrefixes = [
                'public_html/',
                'gezux-app/',
            ];

            for ($i = 0; $i < $testZip->numFiles; $i++) {
                $name = $testZip->getNameIndex($i);
                foreach ($forbiddenPrefixes as $prefix) {
                    if (str_starts_with($name, $prefix)) {
                        $this->error("ARCHITECTURE FAILURE: Found invalid nested folder {$name} inside ZIP.");
                        $testZip->close();
                        return 1;
                    }
                }
            }

            $testZip->close();
            $this->info('✓ ZIP Archive single-root structure validated successfully.');
        }

        // Finalize Report
        $this->info("\n============================================================");
        $this->info('   GEZUX HOSTINGER SINGLE-ROOT PACKAGE COMPLETED');
        $this->info('============================================================');
        $this->line("Package File:    {$zipFilename}");
        $this->line("Latest Package:  {$latestZipFilename}");
        $this->line("Version:         {$releaseVersion}");
        $this->line("Size:            {$zipSizeMb} MB");
        $this->line("Target:          Hostinger Shared Hosting (Direct public_html Root)");
        $this->line("Architecture:    Single Root (public_html with hardened .htaccess)");
        $this->line("Bundled .env:    YES (MySQL ready with APP_DEBUG=false)");
        $this->line("Web Installer:   STANDALONE FAIL-SAFE READY");
        $this->info("============================================================\n");

        return 0;
    }


    /**
     * High-speed multi-threaded copy with exclusion filters.
     */
    protected function copyDirectory(string $src, string $dst): void
    {
        @mkdir($dst, 0755, true);

        // Windows High-Speed Multi-Threaded Robocopy Engine
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $srcWin = str_replace('/', '\\', realpath($src));
            $dstWin = str_replace('/', '\\', $dst);
            $cmd = "robocopy \"{$srcWin}\" \"{$dstWin}\" /E /NFL /NDL /NJH /NJS /nc /ns /np /MT:32 /XD .git .github tests Tests doc docs phpunit mockery fakerphp";
            exec($cmd);
            return;
        }

        // Standard Recursive Copy for Linux / macOS
        $excludedNames = [
            '.git',
            '.github',
            '.gitignore',
            'tests',
            'Tests',
            'doc',
            'docs',
            'phpunit',
            'mockery',
            'fakerphp',
            'laravel/pint',
            'laravel/sail',
            'spatie/laravel-ignition',
            'spatie/flare-client-php',
            'spatie/backtrace',
            'nunomaduro/collision',
        ];

        $dir = opendir($src);
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $srcPath = $src . '/' . $file;
                $dstPath = $dst . '/' . $file;

                $skip = false;
                foreach ($excludedNames as $exc) {
                    if ($file === $exc || str_contains($srcPath, '/' . $exc . '/')) {
                        $skip = true;
                        break;
                    }
                }

                if ($skip) continue;

                if (is_dir($srcPath)) {
                    $this->copyDirectory($srcPath, $dstPath);
                } else {
                    @copy($srcPath, $dstPath);
                }
            }
        }
        closedir($dir);
    }


    /**
     * Scan compiled JS assets for forbidden server secrets.
     */
    protected function scanPublicAssetsForSecrets(string $dir): bool
    {
        if (!is_dir($dir)) return true;

        $forbiddenKeywords = [
            'sk_live_',
            'sk_test_',
            'DB_PASSWORD',
            'MAIL_PASSWORD',
            'FIREBASE_CREDENTIALS',
            'private_key_id',
        ];

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'js') {
                $content = file_get_contents($file->getPathname());
                foreach ($forbiddenKeywords as $keyword) {
                    if (str_contains($content, $keyword)) {
                        $this->error("Secret keyword '{$keyword}' detected in: " . $file->getFilename());
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Recursively delete directory contents.
     */
    protected function cleanDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            @$todo($fileinfo->getRealPath());
        }

        @rmdir($dir);
    }
}
