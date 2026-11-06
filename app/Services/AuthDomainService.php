<?php

namespace App\Services;

use App\Mail\GezuxPlatformEmail;
use App\Models\AuthSetting;
use App\Models\Brand;
use App\Models\CreatorProfile;
use App\Models\NotificationLog;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthDomainService
{
    /**
     * Allowed self-registration roles.
     */
    protected static array $allowedRoles = ['brand', 'clipper'];

    /**
     * Handle user self-registration.
     */
    public static function register(array $data): array
    {
        $email = EmailDomainPolicyService::normalizeEmail($data['email'] ?? '');
        $name = trim($data['name'] ?? '');
        $role = strtolower(trim($data['role'] ?? ''));
        $password = $data['password'] ?? '';

        // 1. Strict Role Authorization
        if (!in_array($role, self::$allowedRoles, true)) {
            throw ValidationException::withMessages([
                'role' => ['Invalid registration role. Only Brand and Clipper accounts are permitted.'],
            ]);
        }

        // 2. Email Policy & Disposable Domain Validation
        $policy = EmailDomainPolicyService::validateEmail($email);
        if (!$policy['allowed']) {
            throw ValidationException::withMessages([
                'email' => [$policy['reason'] ?? 'This email domain cannot be used for registration.'],
            ]);
        }

        // 3. Check for existing email
        if (User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['An account with this email address already exists.'],
            ]);
        }

        // 4. Create User Record
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'status' => 'active',
            'password' => $password, // Casted to hashed automatically
        ]);

        // 5. Initialize Role-specific profile & wallet
        if ($role === 'brand') {
            $companySlug = Str::slug($name) . '-' . substr(md5(uniqid()), 0, 6);
            Brand::create([
                'user_id' => $user->id,
                'company_name' => $name,
                'company_slug' => $companySlug,
                'verification_status' => 'verified',
            ]);
        } else {
            $username = Str::slug($name) . '_' . substr(md5(uniqid()), 0, 5);
            CreatorProfile::create([
                'user_id' => $user->id,
                'display_name' => $name,
                'username' => $username,
                'verification_status' => 'verified',
            ]);
        }

        // Create user wallet
        Wallet::create([
            'user_id' => $user->id,
            'available_balance' => 0.00,
            'pending_balance' => 0.00,
            'locked_balance' => 0.00,
        ]);

        // 6. Generate Verification OTP Code
        $codeResult = VerificationCodeService::generateCode($email, 'email_verification', $user);
        
        if ($codeResult['success']) {
            self::sendVerificationEmail($email, $name, $codeResult['code'], $codeResult['expires_in_minutes']);
        }

        return [
            'user' => $user,
            'email' => $email,
            'role' => $role,
            'requires_verification' => true,
        ];
    }

    /**
     * Authenticate existing user with rate limiting and role-directed redirect.
     */
    public static function login(string $email, string $password, bool $remember = false, ?string $ip = null, ?string $expectedRole = null): array
    {
        $email = EmailDomainPolicyService::normalizeEmail($email);
        $throttleKey = 'login:' . $email . '|' . ($ip ?? request()?->ip() ?? '127.0.0.1');

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => ["Too many login attempts. Please try again in {$seconds} seconds."],
            ]);
        }

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        if ($user->status === 'suspended') {
            throw ValidationException::withMessages([
                'email' => ['This account has been suspended. Please contact GEZUX support.'],
            ]);
        }

        // Strict Portal Role Mismatch Protection
        if ($expectedRole !== null) {
            $normalizedExpected = strtolower(trim($expectedRole));
            $userRole = strtolower(trim($user->role ?? ''));

            if ($normalizedExpected === 'brand' && $userRole !== 'brand') {
                RateLimiter::hit($throttleKey, 60);
                throw ValidationException::withMessages([
                    'email' => ['This account cannot access the Brand portal. Please use the Clipper sign-in option.'],
                ]);
            }

            if ($normalizedExpected === 'clipper' && $userRole !== 'clipper') {
                RateLimiter::hit($throttleKey, 60);
                throw ValidationException::withMessages([
                    'email' => ['This account cannot access the Clipper portal. Please use the Brand sign-in option.'],
                ]);
            }
        }

        RateLimiter::clear($throttleKey);
        Auth::login($user, $remember);
        if (request()?->hasSession()) {
            request()->session()->regenerate();
        }

        return [
            'user' => $user,
            'role' => $user->role ?? ($user->isAdmin() ? 'admin' : ($user->isBrand() ? 'brand' : 'clipper')),
            'redirect_url' => $user->getRedirectUrl(),
        ];
    }

    /**
     * Verify email with 6-digit code and authenticate.
     */
    public static function verifyEmail(string $email, string $code): array
    {
        $result = VerificationCodeService::verifyCode($email, $code, 'email_verification');

        if (!$result['valid']) {
            throw ValidationException::withMessages([
                'code' => [$result['error']],
            ]);
        }

        $user = $result['user'] ?? User::where('email', $email)->first();

        if ($user) {
            Auth::login($user);
            request()?->session()->regenerate();
            return [
                'user' => $user,
                'redirect_url' => $user->getRedirectUrl(),
            ];
        }

        return ['user' => null, 'redirect_url' => '/login'];
    }

    /**
     * Dispatch verification email with code (NO verification link).
     */
    public static function sendVerificationEmail(string $email, string $name, string $code, int $expiresInMinutes = 10): void
    {
        $rendered = EmailTemplateService::render('email_verification', [
            'user_name' => $name,
            'verification_code' => $code,
            'expires_in' => "{$expiresInMinutes} minutes",
        ]);

        try {
            Mail::to($email)->send(new GezuxPlatformEmail(
                subjectText: $rendered['subject'],
                heading: $rendered['heading'],
                bodyContent: $rendered['body'],
                actionUrl: null, // Strictly NO link for verification
                contextData: [
                    'recipient' => $email,
                    'purpose' => 'Account Email Verification',
                    'expires_in' => "{$expiresInMinutes} minutes",
                ],
                codePresentation: $code
            ));

            NotificationLog::create([
                'user_id' => User::where('email', $email)->value('id'),
                'event' => 'EMAIL_VERIFICATION',
                'channel' => 'email',
                'title' => $rendered['subject'],
                'body' => $rendered['body'],
                'status' => 'sent',
                'sent_at' => now(),
                'metadata' => ['email' => $email],
            ]);
        } catch (\Throwable $e) {
            NotificationLog::create([
                'user_id' => User::where('email', $email)->value('id'),
                'event' => 'EMAIL_VERIFICATION',
                'channel' => 'email',
                'title' => $rendered['subject'],
                'body' => $rendered['body'],
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
                'metadata' => ['email' => $email],
            ]);
        }
    }
}
