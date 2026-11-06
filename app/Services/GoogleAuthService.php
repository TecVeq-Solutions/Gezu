<?php

namespace App\Services;

use App\Models\AuthSetting;
use App\Models\Brand;
use App\Models\CreatorProfile;
use App\Models\NotificationLog;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GoogleAuthService
{
    /**
     * Check if Google OAuth is enabled and configured.
     */
    public static function isEnabled(): bool
    {
        $enabled = (bool) AuthSetting::get('google_oauth_enabled', false);
        $clientId = self::getClientId();
        $clientSecret = self::getClientSecret();

        return $enabled && !empty($clientId) && !empty($clientSecret);
    }

    /**
     * Get Google Client ID from database settings or environment.
     */
    public static function getClientId(): string
    {
        return (string) (AuthSetting::get('google_client_id') ?: config('services.google.client_id', ''));
    }

    /**
     * Get Google Client Secret from database settings or environment.
     */
    public static function getClientSecret(): string
    {
        return (string) (AuthSetting::get('google_client_secret') ?: config('services.google.client_secret', ''));
    }

    /**
     * Get Canonical Callback / Redirect URI.
     */
    public static function getRedirectUri(): string
    {
        $configured = AuthSetting::get('google_redirect_uri');
        if (!empty($configured)) {
            return (string) $configured;
        }
        return url('/auth/google/callback');
    }

    /**
     * Build Google OAuth 2.0 Authorization URL with CSRF state protection.
     */
    public static function generateAuthUrl(): string
    {
        if (!self::isEnabled()) {
            throw new \RuntimeException('Google authentication is currently disabled by system administration.');
        }

        $state = Str::random(40);
        session(['google_oauth_state' => $state]);
        if (request()->filled('role')) {
            session(['google_oauth_role' => request('role')]);
        }

        $params = [
            'client_id' => self::getClientId(),
            'redirect_uri' => self::getRedirectUri(),
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * Handle Google Callback with OAuth state validation & domain restriction checks.
     */
    public static function handleCallback(Request $request): array
    {
        if (!self::isEnabled()) {
            throw new \RuntimeException('Google authentication is currently disabled.');
        }

        // 1. Check for provider error / user denied consent
        if ($request->has('error')) {
            throw new \RuntimeException('Google authorization was cancelled or denied: ' . $request->input('error'));
        }

        // 2. Validate OAuth state (CSRF mitigation)
        $sessionState = session('google_oauth_state');
        $callbackState = $request->input('state');

        if (empty($sessionState) || empty($callbackState) || !hash_equals($sessionState, $callbackState)) {
            throw new \RuntimeException('Invalid OAuth session state. Please try signing in again.');
        }

        session()->forget('google_oauth_state');

        $code = $request->input('code');
        if (empty($code)) {
            throw new \RuntimeException('Missing authorization code from Google.');
        }

        // 3. Exchange authorization code for tokens
        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => self::getClientId(),
            'client_secret' => self::getClientSecret(),
            'redirect_uri' => self::getRedirectUri(),
            'grant_type' => 'authorization_code',
        ]);

        if (!$tokenResponse->successful()) {
            Log::error('Google OAuth token exchange failed', ['response' => $tokenResponse->body()]);
            throw new \RuntimeException('Failed to communicate with Google authentication servers.');
        }

        $accessToken = $tokenResponse->json('access_token');

        // 4. Retrieve user profile
        $userResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');
        if (!$userResponse->successful()) {
            throw new \RuntimeException('Failed to fetch verified user profile from Google.');
        }

        $googleUser = $userResponse->json();
        $email = EmailDomainPolicyService::normalizeEmail($googleUser['email'] ?? '');
        $name = trim($googleUser['name'] ?? 'Creator');
        $emailVerified = (bool) ($googleUser['email_verified'] ?? false);

        if (empty($email) || !$emailVerified) {
            throw new \RuntimeException('Google account does not possess a verified email address.');
        }

        // 5. Server-side Domain & Exact Email Allowlist Validation
        $allowedDomains = (array) AuthSetting::get('google_allowed_domains', []);
        $allowedExact = (array) AuthSetting::get('google_allowed_exact_emails', []);

        if (!empty($allowedDomains) || !empty($allowedExact)) {
            $domain = EmailDomainPolicyService::extractDomain($email);
            $normalizedAllowedDomains = array_map('strtolower', array_map('trim', $allowedDomains));
            $normalizedAllowedExact = array_map('strtolower', array_map('trim', $allowedExact));

            $isDomainAllowed = in_array($domain, $normalizedAllowedDomains, true);
            $isExactAllowed = in_array($email, $normalizedAllowedExact, true);

            if (!$isDomainAllowed && !$isExactAllowed) {
                throw new \RuntimeException('Your Google email domain is not authorized for access.');
            }
        }

        // 6. Match Existing User vs New Registration
        $user = User::where('email', $email)->first();

        if ($user) {
            if ($user->status === 'suspended') {
                throw new \RuntimeException('This account has been suspended. Please contact support.');
            }

            // Direct sign in with existing server-side role (Role is NEVER overwritten)
            Auth::login($user, true);
            if (request()?->hasSession()) {
                request()->session()->regenerate();
            }

            NotificationLog::create([
                'user_id' => $user->id,
                'event' => 'SECURITY_ALERT',
                'channel' => 'database',
                'title' => 'Google Sign In',
                'body' => "Signed in with Google on " . now()->toDateTimeString(),
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return [
                'is_new_user' => false,
                'user' => $user,
                'redirect_url' => $user->getRedirectUrl(),
            ];
        }

        // 7. New User: Store Google identity in session and require role selection (Brand vs Clipper)
        session([
            'pending_google_auth' => [
                'email' => $email,
                'name' => $name,
                'google_id' => $googleUser['sub'] ?? null,
                'timestamp' => now()->timestamp,
            ]
        ]);

        if ($presetRole = session('google_oauth_role')) {
            session()->forget('google_oauth_role');
            if (in_array(strtolower($presetRole), ['brand', 'clipper', 'streamer'], true)) {
                $user = self::completeGoogleRegistration($presetRole);
                return [
                    'is_new_user' => false,
                    'user' => $user,
                    'redirect_url' => $user->getRedirectUrl(),
                ];
            }
        }

        return [
            'is_new_user' => true,
            'email' => $email,
            'name' => $name,
            'redirect_url' => '/register?google=1',
        ];
    }

    /**
     * Complete registration for Google-verified user with selected role.
     */
    public static function completeGoogleRegistration(string $role): User
    {
        $role = strtolower(trim($role));
        if (!in_array($role, ['brand', 'clipper', 'streamer'], true)) {
            throw ValidationException::withMessages([
                'role' => ['Invalid registration role. Choose Brand, Clipper, or Streamer.'],
            ]);
        }

        $pending = session('pending_google_auth');
        if (empty($pending) || empty($pending['email'])) {
            throw new \RuntimeException('No pending Google authentication session found. Please sign in with Google again.');
        }

        $email = $pending['email'];
        $name = $pending['name'] ?? 'Creator';

        if (User::where('email', $email)->exists()) {
            session()->forget('pending_google_auth');
            $existing = User::where('email', $email)->first();
            Auth::login($existing);
            return $existing;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'status' => 'active',
            'email_verified_at' => now(), // Google identity is pre-verified
            'password' => Str::random(32), // Secure randomized password
        ]);

        if ($role === 'brand') {
            Brand::create([
                'user_id' => $user->id,
                'company_name' => $name,
                'company_slug' => Str::slug($name) . '-' . substr(md5(uniqid()), 0, 6),
                'verification_status' => 'verified',
            ]);
        } else {
            CreatorProfile::create([
                'user_id' => $user->id,
                'display_name' => $name,
                'username' => Str::slug($name) . '_' . substr(md5(uniqid()), 0, 5),
                'verification_status' => 'verified',
            ]);
        }

        Wallet::create([
            'user_id' => $user->id,
            'available_balance' => 0.00,
            'pending_balance' => 0.00,
            'locked_balance' => 0.00,
        ]);

        session()->forget('pending_google_auth');
        Auth::login($user);
        if (request()?->hasSession()) {
            request()->session()->regenerate();
        }

        return $user;
    }

    /**
     * Test Google configuration health without user login.
     */
    public static function testConfiguration(): array
    {
        $clientId = self::getClientId();
        $clientSecret = self::getClientSecret();

        if (empty($clientId) || empty($clientSecret)) {
            return [
                'status' => 'Invalid Configuration',
                'message' => 'Google Client ID or Client Secret is missing.',
                'healthy' => false,
            ];
        }

        if (!str_contains($clientId, '.apps.googleusercontent.com')) {
            return [
                'status' => 'Partially Configured',
                'message' => 'Client ID format does not appear to be a standard Google OAuth client ID.',
                'healthy' => false,
            ];
        }

        return [
            'status' => 'Configured',
            'message' => 'Google OAuth credentials and callback URI are properly initialized.',
            'healthy' => true,
        ];
    }
}
