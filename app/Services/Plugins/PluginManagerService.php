<?php

namespace App\Services\Plugins;

use App\Models\InstalledPlugin;
use App\Models\PluginMigration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use Exception;

class PluginManagerService
{
    protected string $pluginsDir;

    public function __construct()
    {
        $this->pluginsDir = base_path('plugins');
        $this->ensurePluginsDirectory();
    }

    protected function ensurePluginsDirectory(): void
    {
        if (!File::exists($this->pluginsDir)) {
            File::makeDirectory($this->pluginsDir, 0755, true, true);
        }
    }

    /**
     * Inspect and validate approved plugin bundle ZIP.
     */
    public function inspectPluginBundle(string $zipFilePath): array
    {
        if (!File::exists($zipFilePath)) {
            throw new Exception("Plugin bundle ZIP archive does not exist.");
        }

        $sha256 = hash_file('sha256', $zipFilePath);
        $fileSize = File::size($zipFilePath);

        $zip = new ZipArchive();
        $status = $zip->open($zipFilePath);
        if ($status !== true) {
            throw new Exception("Invalid or corrupted plugin ZIP archive (Error: {$status}).");
        }

        $manifestContent = null;
        $fileCount = $zip->numFiles;
        $forbiddenCorePaths = [
            'app/Models/',
            'app/Http/Controllers/',
            'config/app.php',
            'config/auth.php',
            'routes/web.php',
            'routes/api.php',
            '.env',
        ];

        for ($i = 0; $i < $fileCount; $i++) {
            $entryName = $zip->getNameIndex($i);

            // Zip Slip protection
            if (
                str_contains($entryName, '../') || 
                str_contains($entryName, '..\\') ||
                str_starts_with($entryName, '/') || 
                str_starts_with($entryName, '\\')
            ) {
                $zip->close();
                throw new Exception("Security Violation: Path traversal in plugin bundle ('{$entryName}').");
            }

            // Core collision check
            foreach ($forbiddenCorePaths as $forbidden) {
                if (str_starts_with($entryName, $forbidden) || $entryName === $forbidden) {
                    $zip->close();
                    throw new Exception("Security Violation: Plugin bundle attempts to overwrite protected core path '{$entryName}'.");
                }
            }

            if ($entryName === 'gezux-plugin.json') {
                $manifestContent = $zip->getFromIndex($i);
            }
        }

        $zip->close();

        if (!$manifestContent) {
            throw new Exception("Missing required plugin manifest ('gezux-plugin.json').");
        }

        $manifest = json_decode($manifestContent, true);
        if (!$manifest || !isset($manifest['plugin_id']) || !isset($manifest['name']) || !isset($manifest['version'])) {
            throw new Exception("Invalid 'gezux-plugin.json' manifest structure.");
        }

        // Check compatibility
        $minPhp = $manifest['php'] ?? '8.3';
        if (version_compare(PHP_VERSION, str_replace(['^', '~', '>='], '', $minPhp), '<')) {
            throw new Exception("Incompatible PHP version: Plugin requires {$minPhp}, server has " . PHP_VERSION);
        }

        return [
            'valid' => true,
            'manifest' => $manifest,
            'sha256' => $sha256,
            'size' => $fileSize,
            'files_count' => $fileCount,
        ];
    }

    /**
     * Install approved plugin bundle into plugins directory.
     */
    public function installPluginBundle(string $zipFilePath, ?int $userId = null): InstalledPlugin
    {
        $inspection = $this->inspectPluginBundle($zipFilePath);
        $manifest = $inspection['manifest'];
        $pluginId = $manifest['plugin_id'];

        $targetDir = "{$this->pluginsDir}/{$pluginId}";
        if (File::exists($targetDir)) {
            File::deleteDirectory($targetDir);
        }
        File::makeDirectory($targetDir, 0755, true, true);

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) === true) {
            $fileCount = $zip->numFiles;
            for ($i = 0; $i < $fileCount; $i++) {
                $entryName = $zip->getNameIndex($i);

                if (
                    str_contains($entryName, '../') || 
                    str_contains($entryName, '..\\') ||
                    $entryName === '.env'
                ) {
                    continue;
                }

                $destFile = "{$targetDir}/{$entryName}";
                if (str_ends_with($entryName, '/')) {
                    File::makeDirectory($destFile, 0755, true, true);
                } else {
                    $dir = dirname($destFile);
                    if (!File::exists($dir)) {
                        File::makeDirectory($dir, 0755, true, true);
                    }
                    file_put_contents($destFile, $zip->getFromIndex($i));
                }
            }
            $zip->close();
        }

        // Run plugin migrations if declared
        if ($manifest['requires_migrations'] ?? false) {
            $migrationDir = "{$targetDir}/database/migrations";
            if (File::isDirectory($migrationDir)) {
                Artisan::call('migrate', [
                    '--path' => "plugins/{$pluginId}/database/migrations",
                    '--force' => true,
                ]);

                PluginMigration::create([
                    'plugin_id' => $pluginId,
                    'migration' => "plugins/{$pluginId}/database/migrations",
                    'version' => $manifest['version'],
                    'executed_at' => now(),
                ]);
            }
        }

        $plugin = InstalledPlugin::updateOrCreate(
            ['plugin_id' => $pluginId],
            [
                'name' => $manifest['name'],
                'version' => $manifest['version'],
                'provider' => $manifest['provider'] ?? 'GEZUX Approved',
                'author' => $manifest['author'] ?? 'GEZUX Ecosystem',
                'type' => $manifest['type'] ?? 'filament',
                'status' => ($manifest['enabled_by_default'] ?? true) ? 'ENABLED' : 'INSTALLED',
                'source' => 'bundle_upload',
                'manifest' => $manifest,
                'panels' => $manifest['panels'] ?? ['admin'],
                'installed_by' => $userId,
                'installed_at' => now(),
            ]
        );

        Artisan::call('optimize:clear');
        return $plugin;
    }

    /**
     * Enable or Disable an installed plugin safely.
     */
    public function togglePlugin(InstalledPlugin $plugin, bool $enable): bool
    {
        $plugin->update([
            'status' => $enable ? 'ENABLED' : 'DISABLED',
            'disabled_at' => $enable ? null : now(),
        ]);

        Artisan::call('optimize:clear');
        return true;
    }
}
