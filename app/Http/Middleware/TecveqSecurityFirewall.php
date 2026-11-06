<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class TecveqSecurityFirewall
{
    /**
     * Handle an incoming request through the TECVEQ Security Suite & Firewall.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip() ?? '127.0.0.1';
        $uri = $request->getRequestUri();
        $path = '/' . ltrim($request->path(), '/');

        // Fast-path bypass for static files
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|woff|woff2|ttf|eot|ico|webp|mp4|webm)$/i', $path)) {
            return $next($request);
        }

        try {
            // Check if security tables exist
            if (!Schema::hasTable('psec_settings')) {
                return $next($request);
            }

            // Load settings
            $settings = $this->getSettings();
            if (($settings['project_security'] ?? 1) == 0) {
                return $next($request);
            }

            // 1. Check IP Whitelist
            if ($this->isIpWhitelisted($ip) || $this->isFileWhitelisted($path)) {
                $response = $next($request);
                return $this->applySecurityHeaders($response, $settings);
            }

            // 2. Check IP Bans & Range Bans
            if ($this->isIpBanned($ip)) {
                return response()->view('errors.firewall-blocked', [
                    'title' => 'Access Denied — IP Banned',
                    'reason' => 'Your IP address has been blacklisted by the TECVEQ Security Firewall.',
                    'ip' => $ip,
                    'type' => 'Banned IP'
                ], 403);
            }

            // 3. Bad Bots & Automated Vulnerability Scanner Shield
            $userAgent = $request->userAgent() ?? '';
            if (($settings['badbot_protection'] ?? 1) == 1 && $this->isBadBot($userAgent)) {
                $this->logThreat($ip, $path, $request->getQueryString() ?? '', 'Bad Bot', $userAgent);
                if (($settings['badbot_autoban'] ?? 0) == 1) {
                    $this->banIp($ip, 'Automated exploit scanner / malicious bot');
                }
                return response()->view('errors.firewall-blocked', [
                    'title' => 'Malicious Scanner Blocked',
                    'reason' => 'Access denied: Malicious bot or automated scanner detected.',
                    'ip' => $ip,
                    'type' => 'Bad Bot'
                ], 403);
            }

            // 4. SQL Injection, Path Traversal & Remote Code Injection Shield
            if (($settings['sqli_protection'] ?? 1) == 1) {
                $payloadToCheck = urldecode($request->getQueryString() ?? '') . ' ' . json_encode($request->except(['_token', 'password', 'password_confirmation', 'data.password']));
                if ($this->detectMaliciousPayload($payloadToCheck)) {
                    $this->logThreat($ip, $path, substr($payloadToCheck, 0, 500), 'SQLi / Injection', $userAgent);
                    if (($settings['sqli_autoban'] ?? 0) == 1) {
                        $this->banIp($ip, 'SQL Injection / Exploit Payload detected');
                    }
                    return response()->view('errors.firewall-blocked', [
                        'title' => 'Security Alert — Exploit Detected',
                        'reason' => 'Malicious request signature was intercepted and blocked by TECVEQ WAF.',
                        'ip' => $ip,
                        'type' => 'WAF Intercept (SQLi / XSS)'
                    ], 403);
                }
            }

            // 5. Live Traffic Telemetry (if enabled)
            if (($settings['live_traffic'] ?? 0) == 1 && rand(1, 3) === 1) {
                $this->recordTraffic($request, $ip, $userAgent);
            }

        } catch (\Throwable $t) {
            // Failsafe: Never break application routing if database is under maintenance
        }

        $response = $next($request);

        // 6. Enforce Hardened HTTP Security Headers
        return $this->applySecurityHeaders($response, $settings ?? []);
    }

    /**
     * Get settings from psec_settings
     */
    protected function getSettings(): array
    {
        try {
            return DB::table('psec_settings')->pluck('value', 'key')->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Check if IP is in whitelist
     */
    protected function isIpWhitelisted(string $ip): bool
    {
        return DB::table('psec_ip-whitelist')->where('ip', $ip)->exists();
    }

    /**
     * Check if path is in whitelist
     */
    protected function isFileWhitelisted(string $path): bool
    {
        return DB::table('psec_file-whitelist')->where('path', $path)->exists();
    }

    /**
     * Check if IP is banned
     */
    protected function isIpBanned(string $ip): bool
    {
        if (DB::table('psec_bans')->where('ip', $ip)->exists()) {
            return true;
        }

        // Check ranges
        $ranges = DB::table('psec_bans-ranges')->pluck('ip_range');
        foreach ($ranges as $range) {
            if ($range && str_starts_with($ip, trim($range))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect known offensive scanners, crawlers, and exploit scrapers
     */
    protected function isBadBot(string $ua): bool
    {
        if (empty($ua)) return false;
        
        $badBots = [
            'sqlmap', 'nikto', 'wpscan', 'acunetix', 'masscan', 'havij', 'dirbuster',
            'nmap', 'zgrab', 'censys', 'shodan', 'nessus', 'arachni', 'qualys',
            'openvas', 'metasploit', 'morfeus', 'pymills', 'spbot', 'jersey',
            'whatweb', 'go-http-client', 'semrushbot', 'ahrefsbot', 'mj12bot'
        ];

        $lowerUa = strtolower($ua);
        foreach ($badBots as $bot) {
            if (str_contains($lowerUa, $bot)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect SQLi, XSS, and Path Traversal signatures
     */
    protected function detectMaliciousPayload(string $input): bool
    {
        $patterns = [
            'union\s+select',
            'union\s+all\s+select',
            'information_schema',
            'benchmark\(',
            'sleep\(\d+\)',
            'waitfor\s+delay',
            'pg_sleep\(',
            '0x3a',
            '\b1=1\b',
            '\'\s*or\s*\'1\'\s*=\s*\'1',
            '"\s*or\s*"1"\s*=\s*"1',
            '\.\.\/\.\.\/',
            '\/etc\/passwd',
            'boot\.ini',
            '<script[\s>]',
            'javascript:\s*',
            'onerror\s*=',
            'onload\s*=',
            'eval\(',
            'base64_decode\('
        ];

        foreach ($patterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log attack threat into psec_logs
     */
    protected function logThreat(string $ip, string $page, string $query, string $type, string $ua): void
    {
        try {
            DB::table('psec_logs')->insert([
                'ip' => $ip,
                'date' => date('d F Y'),
                'time' => date('H:i'),
                'page' => substr($page, 0, 255),
                'query' => substr($query, 0, 1000),
                'type' => $type,
                'browser' => $this->detectBrowser($ua),
                'browser_code' => 'firewall',
                'os' => 'Unknown',
                'os_code' => 'net',
                'country' => 'Global',
                'country_code' => 'GL',
                'useragent' => substr($ua, 0, 500),
            ]);
        } catch (\Throwable $e) {}
    }

    /**
     * Auto-ban IP
     */
    protected function banIp(string $ip, string $reason): void
    {
        try {
            if (!DB::table('psec_bans')->where('ip', $ip)->exists()) {
                DB::table('psec_bans')->insert([
                    'ip' => $ip,
                    'date' => date('d F Y'),
                    'time' => date('H:i'),
                    'reason' => $reason,
                    'redirect' => 0,
                    'url' => null,
                    'autoban' => 1
                ]);
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Record live visitor traffic
     */
    protected function recordTraffic(Request $request, string $ip, string $ua): void
    {
        try {
            DB::table('psec_live-traffic')->insert([
                'ip' => $ip,
                'useragent' => substr($ua, 0, 255),
                'browser' => $this->detectBrowser($ua),
                'browser_code' => 'web',
                'os' => 'System',
                'os_code' => 'os',
                'device_type' => 'desktop',
                'country' => 'Local / Cloud',
                'country_code' => 'US',
                'request_uri' => substr($request->getRequestUri(), 0, 255),
                'domain' => $request->getHost(),
                'referer' => substr($request->headers->get('referer') ?? '', 0, 255),
                'bot' => 0,
                'date' => date('d F Y'),
                'time' => date('H:i'),
                'uniquev' => 1
            ]);
        } catch (\Throwable $e) {}
    }

    /**
     * Apply enterprise HTTP security headers
     */
    protected function applySecurityHeaders(Response $response, array $settings): Response
    {
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-Firewall-Protection', 'TECVEQ-Security-v4.1.0');

        return $response;
    }

    protected function detectBrowser(string $ua): string
    {
        if (str_contains($ua, 'Chrome')) return 'Chrome';
        if (str_contains($ua, 'Firefox')) return 'Firefox';
        if (str_contains($ua, 'Safari')) return 'Safari';
        if (str_contains($ua, 'Edge')) return 'Edge';
        return 'Other';
    }
}
