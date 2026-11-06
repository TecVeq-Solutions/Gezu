<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthSetting;
use App\Models\InstalledPlugin;
use App\Models\MediaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use ZipArchive;

class PluginModulesController extends Controller
{
    /* =========================================================================
       1. TECVEQ SEO PLUGIN (Rank Math Equivalent)
       ========================================================================= */

    public function seoSettings(Request $request)
    {
        $pageTitle = 'TECVEQ SEO Plugin — Architecture & Optimization Suite';

        if ($request->isMethod('post')) {
            $fields = [
                'seo_default_meta_title',
                'seo_default_meta_description',
                'seo_default_canonical_base',
                'seo_schema_type',
                'seo_sitemap_frequency',
                'seo_google_verification_code',
                'seo_bing_verification_code',
                'seo_baidu_verification_code',
                'seo_yandex_verification_code',
                'seo_pinterest_verification_code',
                'seo_norton_verification_code',
                'seo_twitter_handle',
                'seo_social_default_image',
                'seo_custom_header_code',
                'seo_custom_body_code',
                'seo_custom_footer_code',
                'seo_custom_meta_tags',
            ];

            foreach ($fields as $field) {
                AuthSetting::set($field, $request->input($field, ''));
            }

            AuthSetting::set('seo_robots_index', $request->boolean('seo_robots_index', true));
            AuthSetting::set('seo_robots_follow', $request->boolean('seo_robots_follow', true));
            AuthSetting::set('seo_sitemap_enabled', $request->boolean('seo_sitemap_enabled', true));
            AuthSetting::set('seo_breadcrumbs_enabled', $request->boolean('seo_breadcrumbs_enabled', true));
            AuthSetting::set('seo_redirections_enabled', $request->boolean('seo_redirections_enabled', true));
            AuthSetting::set('seo_404_monitor_enabled', $request->boolean('seo_404_monitor_enabled', true));

            if ($request->hasFile('seo_social_image_file')) {
                $file = $request->file('seo_social_image_file');
                $filename = 'seo_social_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/branding'), $filename);
                AuthSetting::set('seo_social_default_image', 'uploads/branding/' . $filename);
            }

            session()->flash('success', 'TECVEQ SEO Plugin configuration, webmaster tags, and schema rules saved successfully.');
            return back();
        }

        $settings = [
            'seo_default_meta_title' => AuthSetting::get('seo_default_meta_title', '%title% | %sitename%'),
            'seo_default_meta_description' => AuthSetting::get('seo_default_meta_description', 'High performance viral content clipping network connecting verified brands and video creators.'),
            'seo_default_canonical_base' => AuthSetting::get('seo_default_canonical_base', url('/')),
            'seo_robots_index' => (bool) AuthSetting::get('seo_robots_index', true),
            'seo_robots_follow' => (bool) AuthSetting::get('seo_robots_follow', true),
            'seo_schema_type' => AuthSetting::get('seo_schema_type', 'Organization'),
            'seo_sitemap_enabled' => (bool) AuthSetting::get('seo_sitemap_enabled', true),
            'seo_sitemap_frequency' => AuthSetting::get('seo_sitemap_frequency', 'weekly'),
            'seo_breadcrumbs_enabled' => (bool) AuthSetting::get('seo_breadcrumbs_enabled', true),
            'seo_google_verification_code' => AuthSetting::get('seo_google_verification_code', ''),
            'seo_bing_verification_code' => AuthSetting::get('seo_bing_verification_code', ''),
            'seo_baidu_verification_code' => AuthSetting::get('seo_baidu_verification_code', ''),
            'seo_yandex_verification_code' => AuthSetting::get('seo_yandex_verification_code', ''),
            'seo_pinterest_verification_code' => AuthSetting::get('seo_pinterest_verification_code', ''),
            'seo_norton_verification_code' => AuthSetting::get('seo_norton_verification_code', ''),
            'seo_twitter_handle' => AuthSetting::get('seo_twitter_handle', '@tecveq'),
            'seo_social_default_image' => AuthSetting::get('seo_social_default_image', ''),
            'seo_custom_header_code' => AuthSetting::get('seo_custom_header_code', ''),
            'seo_custom_body_code' => AuthSetting::get('seo_custom_body_code', ''),
            'seo_custom_footer_code' => AuthSetting::get('seo_custom_footer_code', ''),
            'seo_custom_meta_tags' => AuthSetting::get('seo_custom_meta_tags', ''),
            'seo_redirections_enabled' => (bool) AuthSetting::get('seo_redirections_enabled', true),
            'seo_404_monitor_enabled' => (bool) AuthSetting::get('seo_404_monitor_enabled', true),
        ];

        // Telemetry count for sitemap
        $postCount = DB::table('cms_posts')->where('status', 'published')->count();
        $pageCount = DB::table('cms_pages')->where('status', 'published')->count();

        // Redirections & 404 Monitor Data
        $redirections = DB::table('seo_redirections')->orderByDesc('id')->get();
        $logs404 = DB::table('seo_404_logs')->orderByDesc('last_occurred_at')->limit(50)->get();

        return view('admin.plugins.seo.settings', compact('pageTitle', 'settings', 'postCount', 'pageCount', 'redirections', 'logs404'));
    }

    public function seoStoreRedirection(Request $request)
    {
        $validated = $request->validate([
            'source_url' => 'required|string|max:500',
            'target_url' => 'required|string|max:500',
            'status_code' => 'required|in:301,302,307',
        ]);

        $source = '/' . ltrim($validated['source_url'], '/');

        DB::table('seo_redirections')->updateOrInsert(
            ['source_url' => $source],
            [
                'target_url' => $validated['target_url'],
                'status_code' => (int) $validated['status_code'],
                'is_active' => 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        // Remove from 404 logs if it was logged there
        DB::table('seo_404_logs')->where('uri', $source)->delete();

        session()->flash('success', "Redirection rule created: {$source} -> {$validated['target_url']} ({$validated['status_code']})");
        return back();
    }

    public function seoDeleteRedirection($id)
    {
        DB::table('seo_redirections')->where('id', $id)->delete();
        session()->flash('success', 'Redirection rule deleted.');
        return back();
    }

    public function seoClear404Logs()
    {
        DB::table('seo_404_logs')->truncate();
        session()->flash('success', 'All 404 error monitor logs have been cleared.');
        return back();
    }

    /**
     * XML Sitemap Generator (Rank Math Style)
     */
    public function sitemapXml()
    {
        $enabled = (bool) AuthSetting::get('seo_sitemap_enabled', true);
        if (!$enabled) {
            abort(404, 'Sitemap is currently disabled in TECVEQ SEO Plugin settings.');
        }

        $frequency = AuthSetting::get('seo_sitemap_frequency', 'weekly');
        $baseUrl = url('/');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // 1. Homepage
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($baseUrl) . "</loc>\n";
        $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $xml .= "    <changefreq>daily</changefreq>\n";
        $xml .= "    <priority>1.0</priority>\n";
        $xml .= "  </url>\n";

        // 2. Marketplace
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($baseUrl . '/marketplace') . "</loc>\n";
        $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $xml .= "    <changefreq>daily</changefreq>\n";
        $xml .= "    <priority>0.9</priority>\n";
        $xml .= "  </url>\n";

        // 3. Blog Index
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($baseUrl . '/blog') . "</loc>\n";
        $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $xml .= "    <changefreq>daily</changefreq>\n";
        $xml .= "    <priority>0.8</priority>\n";
        $xml .= "  </url>\n";

        // 4. Published Blog Posts
        $posts = DB::table('cms_posts')
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->get();

        foreach ($posts as $post) {
            $lastmod = $post->updated_at ? date('Y-m-d', strtotime($post->updated_at)) : date('Y-m-d');
            $loc = url('/blog/' . $post->slug);
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>{$frequency}</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";
            $xml .= "  </url>\n";
        }

        // 5. Published Pages
        $pages = DB::table('cms_pages')
            ->where('status', 'published')
            ->get();

        foreach ($pages as $page) {
            $lastmod = $page->updated_at ? date('Y-m-d', strtotime($page->updated_at)) : date('Y-m-d');
            $loc = url('/page/' . $page->slug);
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>monthly</changefreq>\n";
            $xml .= "    <priority>0.6</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /* =========================================================================
       2. TECVEQ MEDIA LIBRARY ORGANIZER (media-library-organizer.2.1.5 Equivalent)
       ========================================================================= */

    public function mediaLibraryIndex(Request $request)
    {
        $pageTitle = 'TECVEQ Media Library Organizer';
        $category = $request->query('category', 'all');
        $search = $request->query('search', '');
        $type = $request->query('type', 'all');

        $query = MediaItem::query()->latest();

        if ($category !== 'all' && !empty($category)) {
            $query->where('category', $category);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        if ($type === 'image') {
            $query->where('mime_type', 'like', 'image/%');
        } elseif ($type === 'video') {
            $query->where('mime_type', 'like', 'video/%');
        } elseif ($type === 'document') {
            $query->where(function ($q) {
                $q->where('mime_type', 'like', '%pdf%')
                  ->orWhere('mime_type', 'like', '%zip%')
                  ->orWhere('mime_type', 'like', '%text%');
            });
        }

        $mediaItems = $query->paginate(24)->appends($request->query());

        // Category counts
        $counts = [
            'all' => MediaItem::count(),
            'images' => MediaItem::where('mime_type', 'like', 'image/%')->count(),
            'videos' => MediaItem::where('mime_type', 'like', 'video/%')->count(),
            'campaign_video' => MediaItem::where('category', 'campaign_video')->count(),
            'articles' => MediaItem::where('category', 'articles')->count(),
            'branding' => MediaItem::where('category', 'branding')->count(),
        ];

        // Total disk usage
        $totalBytes = MediaItem::sum('file_size') ?: 0;
        $totalStorageFormatted = $this->formatBytes($totalBytes);

        return view('admin.plugins.media-library.index', compact('pageTitle', 'mediaItems', 'category', 'search', 'type', 'counts', 'totalStorageFormatted'));
    }

    public function mediaLibraryApiList(Request $request)
    {
        $search = $request->query('search', '');
        $type = $request->query('type', 'all');

        $query = MediaItem::query()->latest();

        if (!empty($search)) {
            $query->where('filename', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
        }

        if ($type === 'image') {
            $query->where('mime_type', 'like', 'image/%');
        } elseif ($type === 'video') {
            $query->where('mime_type', 'like', 'video/%');
        }

        $items = $query->limit(50)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'filename' => $item->filename,
                'original_name' => $item->original_name,
                'url' => $item->url,
                'mime_type' => $item->mime_type,
                'file_size' => $item->formatted_size,
                'category' => $item->category,
                'dimensions' => $item->dimensions,
                'alt_text' => $item->alt_text,
                'created_at' => $item->created_at ? $item->created_at->format('M d, Y') : '',
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $items,
        ]);
    }

    public function mediaLibraryUpload(Request $request)
    {
        $request->validate([
            'files' => 'required',
            'files.*' => 'file|max:102400', // 100MB per file
            'category' => 'nullable|string|max:50',
        ]);

        $category = $request->input('category', 'general');
        $targetDir = public_path('uploads/media/' . $category);
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $uploaded = [];
        $files = $request->file('files');
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if (!$file->isValid()) continue;

            $origName = $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());
            $safeName = Str::slug(pathinfo($origName, PATHINFO_FILENAME)) . '-' . time() . '.' . $ext;
            $file->move($targetDir, $safeName);

            $fullPath = $targetDir . '/' . $safeName;
            $relPath = 'uploads/media/' . $category . '/' . $safeName;
            $url = asset($relPath);
            $mime = File::mimeType($fullPath);
            $size = File::size($fullPath);

            $dimensions = null;
            if (str_starts_with($mime, 'image/') && in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $info = @getimagesize($fullPath);
                if ($info) {
                    $dimensions = "{$info[0]}x{$info[1]}";
                }
            }

            $media = MediaItem::create([
                'user_id' => auth()->id(),
                'filename' => $safeName,
                'original_name' => $origName,
                'path' => $relPath,
                'url' => $url,
                'mime_type' => $mime,
                'file_size' => $size,
                'folder' => 'uploads/media/' . $category,
                'category' => $category,
                'dimensions' => $dimensions,
                'alt_text' => pathinfo($origName, PATHINFO_FILENAME),
            ]);

            $uploaded[] = $media;
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => count($uploaded) . ' item(s) cataloged into TECVEQ Media Library.',
                'items' => $uploaded,
            ]);
        }

        session()->flash('success', count($uploaded) . ' item(s) uploaded and cataloged in the Media Library Organizer.');
        return back();
    }

    public function mediaLibraryUpdateMeta(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:media_items,id',
            'alt_text' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:50',
        ]);

        $media = MediaItem::findOrFail($validated['id']);
        $media->update([
            'alt_text' => $validated['alt_text'] ?? $media->alt_text,
            'category' => $validated['category'] ?? $media->category,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Media metadata updated successfully.',
            'media' => $media,
        ]);
    }

    public function mediaLibraryDelete(Request $request)
    {
        $id = $request->input('id');
        $media = MediaItem::findOrFail($id);

        $fullPath = public_path($media->path);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }

        $media->delete();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Media asset deleted successfully.']);
        }

        session()->flash('success', 'Asset removed from TECVEQ Media Library.');
        return back();
    }

    /* =========================================================================
       3. TECVEQ FILE MANAGER (wp-file-manager Equivalent)
       ========================================================================= */

    public function fileManagerIndex()
    {
        $pageTitle = 'TECVEQ File Manager — Cloud Workspace & Assets';
        return view('admin.plugins.file-manager.index', compact('pageTitle'));
    }

    public function fileManagerApiList(Request $request)
    {
        $subfolder = trim($request->query('folder', ''), '/\\');
        $subfolder = str_replace(['..', '//'], '', $subfolder);

        $baseDir = base_path();
        $currentDir = empty($subfolder) ? $baseDir : $baseDir . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $subfolder);

        if (!File::exists($currentDir)) {
            File::makeDirectory($currentDir, 0755, true);
        }

        $directories = [];
        $files = [];

        foreach (File::directories($currentDir) as $dir) {
            $name = basename($dir);
            $rel = empty($subfolder) ? $name : $subfolder . '/' . $name;
            $count = count(File::files($dir)) + count(File::directories($dir));
            $directories[] = [
                'name' => $name,
                'path' => $rel,
                'is_dir' => true,
                'count' => $count,
                'modified' => date('Y-m-d H:i', File::lastModified($dir)),
            ];
        }

        foreach (File::files($currentDir) as $file) {
            $name = $file->getFilename();
            $ext = strtolower($file->getExtension());
            $rel = empty($subfolder) ? $name : $subfolder . '/' . $name;
            $isPublic = str_starts_with($rel, 'public/');
            $publicUrl = $isPublic ? asset(substr($rel, 7)) : '';

            $files[] = [
                'name' => $name,
                'path' => $rel,
                'url' => $publicUrl,
                'size' => $this->formatBytes($file->getSize()),
                'size_formatted' => $this->formatBytes($file->getSize()),
                'size_raw' => $file->getSize(),
                'ext' => $ext ?: 'file',
                'is_dir' => false,
                'is_image' => in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']),
                'is_media' => in_array($ext, ['mp4', 'webm', 'mov', 'mp3', 'wav']),
                'is_code' => in_array($ext, ['php', 'js', 'css', 'json', 'html', 'txt', 'md', 'xml', 'sql', 'env', 'blade', 'gitignore', 'yaml', 'yml']),
                'is_zip' => in_array($ext, ['zip', 'gz', 'tar', 'rar']),
                'modified' => date('Y-m-d H:i', $file->getMTime()),
            ];
        }

        return response()->json([
            'status' => 'success',
            'current_folder' => $subfolder,
            'directories' => $directories,
            'folders' => $directories,
            'files' => $files,
        ]);
    }

    public function fileManagerUpload(Request $request)
    {
        $subfolder = trim($request->input('folder', ''), '/\\');
        $subfolder = str_replace(['..', '//'], '', $subfolder);
        $targetDir = empty($subfolder) ? base_path() : base_path($subfolder);

        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $uploaded = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $file->move($targetDir, $originalName);

                    $relPath = empty($subfolder) ? $originalName : $subfolder . '/' . $originalName;
                    $uploaded[] = [
                        'name' => $originalName,
                        'path' => $relPath,
                    ];
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => count($uploaded) . ' file(s) successfully uploaded.',
            'uploaded' => $uploaded,
        ]);
    }

    public function fileManagerNewFolder(Request $request)
    {
        $name = trim($request->input('name', 'new-folder'));
        $name = str_replace(['..', '/', '\\'], '', $name);
        $parent = trim($request->input('folder', ''), '/\\');
        $parent = str_replace(['..', '//'], '', $parent);
        $target = empty($parent) ? base_path($name) : base_path($parent . '/' . $name);

        if (!File::exists($target)) {
            File::makeDirectory($target, 0755, true);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Folder '{$name}' created successfully.",
        ]);
    }

    public function fileManagerDelete(Request $request)
    {
        $path = trim($request->input('path', ''), '/\\');
        $path = str_replace(['..', '//'], '', $path);
        $fullPath = base_path($path);

        $base = realpath(base_path());
        if ($fullPath && str_starts_with(realpath($fullPath) ?: '', $base) && realpath($fullPath) !== $base) {
            if (File::isDirectory($fullPath)) {
                File::deleteDirectory($fullPath);
            } else {
                File::delete($fullPath);
            }
            return response()->json(['status' => 'success', 'message' => 'Item deleted.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid file path or cannot delete root.'], 400);
    }

    public function fileManagerReadFile(Request $request)
    {
        $path = trim($request->input('path', ''), '/\\');
        $path = str_replace(['..', '//'], '', $path);
        $fullPath = base_path($path);
        $base = realpath(base_path());

        if ($fullPath && str_starts_with(realpath($fullPath) ?: '', $base) && File::isFile($fullPath)) {
            $content = File::get($fullPath);
            return response()->json([
                'status' => 'success',
                'content' => $content,
                'path' => $path,
                'filename' => basename($fullPath),
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'File could not be read or does not exist.'], 400);
    }

    public function fileManagerSaveFile(Request $request)
    {
        $path = trim($request->input('path', ''), '/\\');
        $path = str_replace(['..', '//'], '', $path);
        $content = $request->input('content', '');
        $fullPath = base_path($path);
        $base = realpath(base_path());

        if ($fullPath && str_starts_with(realpath($fullPath) ?: '', $base) && File::isFile($fullPath)) {
            File::put($fullPath, $content);
            return response()->json([
                'status' => 'success',
                'message' => 'File saved successfully.',
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'File write permission denied or path outside sandbox.'], 400);
    }

    public function fileManagerZip(Request $request)
    {
        $path = trim($request->input('path', ''), '/\\');
        $path = str_replace(['..', '//'], '', $path);
        $fullPath = base_path($path);
        $base = realpath(base_path());

        if (!$fullPath || !str_starts_with(realpath($fullPath) ?: '', $base)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid item.'], 400);
        }

        $zipName = basename($fullPath) . '-' . time() . '.zip';
        $zipPath = dirname($fullPath) . DIRECTORY_SEPARATOR . $zipName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            if (File::isDirectory($fullPath)) {
                $files = File::allFiles($fullPath);
                foreach ($files as $file) {
                    $rel = substr($file->getRealPath(), strlen($fullPath) + 1);
                    $zip->addFile($file->getRealPath(), $rel);
                }
            } else {
                $zip->addFile($fullPath, basename($fullPath));
            }
            $zip->close();

            return response()->json([
                'status' => 'success',
                'message' => "Archive {$zipName} generated successfully.",
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to create zip archive.'], 500);
    }

    public function fileManagerUnzip(Request $request)
    {
        $path = trim($request->input('path', ''), '/\\');
        $path = str_replace(['..', '//'], '', $path);
        $fullPath = base_path($path);
        $base = realpath(base_path());

        if (!$fullPath || !str_starts_with(realpath($fullPath) ?: '', $base) || !File::isFile($fullPath)) {
            return response()->json(['status' => 'error', 'message' => 'Zip file not found.'], 400);
        }

        $destDir = dirname($fullPath);
        $zip = new ZipArchive();
        if ($zip->open($fullPath) === true) {
            $zip->extractTo($destDir);
            $zip->close();

            return response()->json([
                'status' => 'success',
                'message' => "Archive extracted successfully.",
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Could not extract zip archive.'], 500);
    }

    /* =========================================================================
       4. TECVEQ SPEED OPTIMIZER (WP Rocket Equivalent)
       ========================================================================= */

    public function speedSettings(Request $request)
    {
        $pageTitle = 'TECVEQ Speed Optimizer — Turbo Performance Engine';

        if ($request->isMethod('post')) {
            AuthSetting::set('speed_page_caching_enabled', $request->boolean('speed_page_caching_enabled', true));
            AuthSetting::set('speed_cache_lifespan_hours', (int) $request->input('speed_cache_lifespan_hours', 24));
            AuthSetting::set('speed_html_minify_enabled', $request->boolean('speed_html_minify_enabled', true));
            AuthSetting::set('speed_lazy_loading_enabled', $request->boolean('speed_lazy_loading_enabled', true));
            AuthSetting::set('speed_instant_page_prefetch_enabled', $request->boolean('speed_instant_page_prefetch_enabled', true));
            AuthSetting::set('speed_browser_cache_headers_enabled', $request->boolean('speed_browser_cache_headers_enabled', true));
            AuthSetting::set('speed_defer_js_enabled', $request->boolean('speed_defer_js_enabled', true));
            AuthSetting::set('speed_css_optimization_enabled', $request->boolean('speed_css_optimization_enabled', true));
            AuthSetting::set('speed_delay_js_scripts', $request->input('speed_delay_js_scripts', ''));
            AuthSetting::set('speed_excluded_urls', $request->input('speed_excluded_urls', ''));
            AuthSetting::set('speed_cdn_url', $request->input('speed_cdn_url', ''));

            session()->flash('success', 'TECVEQ Speed Optimizer policies updated and cache rules regenerated.');
            return back();
        }

        $settings = [
            'speed_page_caching_enabled' => (bool) AuthSetting::get('speed_page_caching_enabled', true),
            'speed_cache_lifespan_hours' => (int) AuthSetting::get('speed_cache_lifespan_hours', 24),
            'speed_html_minify_enabled' => (bool) AuthSetting::get('speed_html_minify_enabled', true),
            'speed_lazy_loading_enabled' => (bool) AuthSetting::get('speed_lazy_loading_enabled', true),
            'speed_instant_page_prefetch_enabled' => (bool) AuthSetting::get('speed_instant_page_prefetch_enabled', true),
            'speed_browser_cache_headers_enabled' => (bool) AuthSetting::get('speed_browser_cache_headers_enabled', true),
            'speed_defer_js_enabled' => (bool) AuthSetting::get('speed_defer_js_enabled', true),
            'speed_css_optimization_enabled' => (bool) AuthSetting::get('speed_css_optimization_enabled', true),
            'speed_delay_js_scripts' => AuthSetting::get('speed_delay_js_scripts', ''),
            'speed_excluded_urls' => AuthSetting::get('speed_excluded_urls', ''),
            'speed_cdn_url' => AuthSetting::get('speed_cdn_url', ''),
        ];

        // Cache size telemetry
        $cacheSize = '0 KB';
        $viewsPath = storage_path('framework/views');
        if (File::exists($viewsPath)) {
            $bytes = 0;
            foreach (File::allFiles($viewsPath) as $f) {
                $bytes += $f->getSize();
            }
            $cacheSize = $this->formatBytes($bytes);
        }

        return view('admin.plugins.speed-optimizer.index', compact('pageTitle', 'settings', 'cacheSize'));
    }

    public function speedPurgeCache()
    {
        try {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            session()->flash('success', 'TECVEQ Speed Optimizer: 100% of compiled page caches and view fragments purged successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Cache purge warning: ' . $e->getMessage());
        }

        return back();
    }

    public function speedCleanDb(Request $request)
    {
        try {
            // Clean up session table if using database driver, or clear transient logs
            if (\Schema::hasTable('sessions')) {
                DB::table('sessions')->where('last_activity', '<', time() - 86400 * 7)->delete();
            }
            // Clear expired cache entries
            if (\Schema::hasTable('cache')) {
                DB::table('cache')->where('expiration', '<', time())->delete();
            }

            session()->flash('success', 'Database optimization complete: Expired transients, orphaned sessions, and cached fragments cleared.');
        } catch (\Throwable $e) {
            session()->flash('error', 'Database cleanup error: ' . $e->getMessage());
        }

        return back();
    }

    public function speedPreloadCache(Request $request)
    {
        $urls = [
            url('/'),
            url('/marketplace'),
            url('/blog'),
        ];

        $posts = DB::table('cms_posts')->where('status', 'published')->limit(10)->get();
        foreach ($posts as $p) {
            $urls[] = url('/blog/' . $p->slug);
        }

        $preloaded = 0;
        foreach ($urls as $u) {
            try {
                Http::timeout(2)->get($u);
                $preloaded++;
            } catch (\Throwable $e) {
                // Ignore timeout on preload
            }
        }

        session()->flash('success', "Cache Preloader activated: Pre-warmed {$preloaded} top pages into high-speed memory.");
        return back();
    }

    /* =========================================================================
       5. TECVEQ RICH EDITOR (Gutenberg Equivalent)
       ========================================================================= */

    public function editorSettings(Request $request)
    {
        $pageTitle = 'TECVEQ Rich Editor — Content Studio & Block Preferences';

        if ($request->isMethod('post')) {
            AuthSetting::set('editor_default_mode', $request->input('editor_default_mode', 'visual'));
            AuthSetting::set('editor_spellcheck_enabled', $request->boolean('editor_spellcheck_enabled', true));
            AuthSetting::set('editor_auto_save_seconds', (int) $request->input('editor_auto_save_seconds', 30));
            AuthSetting::set('editor_typography_scale', $request->input('editor_typography_scale', 'modern'));

            session()->flash('success', 'TECVEQ Rich Editor preferences saved.');
            return back();
        }

        $settings = [
            'editor_default_mode' => AuthSetting::get('editor_default_mode', 'visual'),
            'editor_spellcheck_enabled' => (bool) AuthSetting::get('editor_spellcheck_enabled', true),
            'editor_auto_save_seconds' => (int) AuthSetting::get('editor_auto_save_seconds', 30),
            'editor_typography_scale' => AuthSetting::get('editor_typography_scale', 'modern'),
        ];

        return view('admin.plugins.editor.settings', compact('pageTitle', 'settings'));
    }

    /* =========================================================================
       6. TECVEQ SECURITY SUITE & FIREWALL (WAF, SQLi, Bot & Threat Shield)
       ========================================================================= */

    public function securitySettings(Request $request)
    {
        $pageTitle = 'TECVEQ Security Suite & Firewall — Threat Telemetry & WAF Center';

        if ($request->isMethod('post')) {
            $keys = [
                'project_security',
                'sqli_protection',
                'sqli_logging',
                'sqli_autoban',
                'badbot_protection',
                'badbot_logging',
                'badbot_autoban',
                'proxy_protection',
                'proxy_logging',
                'spam_protection',
                'live_traffic',
                'countryban_blacklist',
                'display_errors',
                'badword_replace',
            ];

            foreach ($keys as $k) {
                $val = $request->input($k, '0');
                DB::table('psec_settings')->updateOrInsert(['key' => $k], ['value' => $val]);
            }

            session()->flash('success', 'TECVEQ Security Firewall rules and telemetry settings updated successfully.');
            return back();
        }

        // Fetch settings
        $settings = DB::table('psec_settings')->pluck('value', 'key')->toArray();

        // Telemetry Metrics
        $totalThreats = DB::table('psec_logs')->count();
        $sqliThreats = DB::table('psec_logs')->where('type', 'like', '%SQLi%')->count();
        $badBotThreats = DB::table('psec_logs')->where('type', 'like', '%Bot%')->count();
        $bannedIpsCount = DB::table('psec_bans')->count();
        $liveTrafficCount = DB::table('psec_live-traffic')->count();

        // Recent Attack Logs
        $recentLogs = DB::table('psec_logs')->orderBy('id', 'desc')->limit(25)->get();

        // Banned IPs list
        $bannedIps = DB::table('psec_bans')->orderBy('id', 'desc')->limit(30)->get();

        // Whitelisted IPs
        $whitelistedIps = DB::table('psec_ip-whitelist')->orderBy('id', 'desc')->get();

        return view('admin.plugins.security.index', compact(
            'pageTitle',
            'settings',
            'totalThreats',
            'sqliThreats',
            'badBotThreats',
            'bannedIpsCount',
            'liveTrafficCount',
            'recentLogs',
            'bannedIps',
            'whitelistedIps'
        ));
    }

    public function securityAddBan(Request $request)
    {
        $request->validate([
            'ip' => 'required|string|max:45',
            'reason' => 'nullable|string|max:255',
        ]);

        $ip = trim($request->input('ip'));
        $reason = $request->input('reason', 'Manual Admin Blacklist');

        if (!DB::table('psec_bans')->where('ip', $ip)->exists()) {
            DB::table('psec_bans')->insert([
                'ip' => $ip,
                'date' => date('d F Y'),
                'time' => date('H:i'),
                'reason' => $reason,
                'redirect' => 0,
                'url' => null,
                'autoban' => 0,
            ]);
            session()->flash('success', "IP address {$ip} has been added to Firewall Blacklist.");
        } else {
            session()->flash('info', "IP address {$ip} is already banned.");
        }

        return back();
    }

    public function securityDeleteBan($id)
    {
        DB::table('psec_bans')->where('id', $id)->delete();
        session()->flash('success', 'IP ban lifted successfully.');
        return back();
    }

    public function securityAddWhitelist(Request $request)
    {
        $request->validate([
            'ip' => 'required|string|max:45',
            'notes' => 'nullable|string|max:255',
        ]);

        $ip = trim($request->input('ip'));
        $notes = $request->input('notes', 'Trusted Developer/Admin IP');

        if (!DB::table('psec_ip-whitelist')->where('ip', $ip)->exists()) {
            DB::table('psec_ip-whitelist')->insert([
                'ip' => $ip,
                'notes' => $notes,
            ]);
            session()->flash('success', "IP address {$ip} added to Firewall Whitelist.");
        } else {
            session()->flash('info', "IP address {$ip} is already whitelisted.");
        }

        return back();
    }

    public function securityDeleteWhitelist($id)
    {
        DB::table('psec_ip-whitelist')->where('id', $id)->delete();
        session()->flash('success', 'IP removed from whitelist.');
        return back();
    }

    public function securityClearLogs()
    {
        DB::table('psec_logs')->truncate();
        session()->flash('success', 'Security threat logs cleared successfully.');
        return back();
    }

    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
