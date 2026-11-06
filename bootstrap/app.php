<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'auth/*',
            'api/*',
        ]);

        $middleware->append(\App\Http\Middleware\TecveqSecurityFirewall::class);

        $middleware->alias([
            'admin.access' => \App\Http\Middleware\AdminAccessMiddleware::class,
            'brand.access' => \App\Http\Middleware\BrandAccessMiddleware::class,
            'clipper.access' => \App\Http\Middleware\ClipperAccessMiddleware::class,
            'streamer.access' => \App\Http\Middleware\StreamerAccessMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Automatically capture user-facing & system exceptions into database ErrorLog table
        $exceptions->report(function (\Throwable $e) {
            try {
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return;
                }
                if (\Illuminate\Support\Facades\Schema::hasTable('error_logs')) {
                    \App\Models\ErrorLog::create([
                        'user_id' => \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null,
                        'exception_type' => get_class($e),
                        'message' => $e->getMessage() ?: 'No message provided',
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'url' => request()->fullUrl(),
                        'trace' => substr($e->getTraceAsString(), 0, 5000),
                    ]);
                }
            } catch (\Throwable $dbError) {
                // Fail silently so database log issues never crash the application
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            $path = '/' . ltrim($request->path(), '/');

            // 1. Check for active TECVEQ SEO Redirection rule
            try {
                $redir = \Illuminate\Support\Facades\DB::table('seo_redirections')
                    ->where('source_url', $path)
                    ->where('is_active', 1)
                    ->first();

                if ($redir) {
                    \Illuminate\Support\Facades\DB::table('seo_redirections')
                        ->where('id', $redir->id)
                        ->increment('hits_count');

                    return redirect($redir->target_url, (int) $redir->status_code);
                }

                // 2. Log 404 error hit into TECVEQ 404 Monitor
                $log = \Illuminate\Support\Facades\DB::table('seo_404_logs')->where('uri', $path)->first();
                if ($log) {
                    \Illuminate\Support\Facades\DB::table('seo_404_logs')->where('id', $log->id)->update([
                        'hits' => $log->hits + 1,
                        'referer' => $request->headers->get('referer') ?: $log->referer,
                        'last_occurred_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    \Illuminate\Support\Facades\DB::table('seo_404_logs')->insert([
                        'uri' => $path,
                        'referer' => $request->headers->get('referer'),
                        'ip_address' => $request->ip(),
                        'user_agent' => substr($request->userAgent() ?? '', 0, 255),
                        'hits' => 1,
                        'last_occurred_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Throwable $t) {
                // Silently bypass if DB is resetting
            }

            return null;
        });
    })->create();
