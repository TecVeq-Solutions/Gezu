<?php

namespace App\Services;

use App\Models\AuthSetting;
use App\Models\DisposableEmailDomain;

class EmailDomainPolicyService
{
    /**
     * Default list of known disposable email providers.
     */
    protected static array $defaultDisposableDomains = [
        'mailinator.com',
        '10minutemail.com',
        'tempmail.com',
        'temp-mail.org',
        'guerrillamail.com',
        'yopmail.com',
        'trashmail.com',
        'sharklasers.com',
        'getairmail.com',
        'dispostable.com',
        'throwawaymail.com',
        'nada.ltd',
        'fakemailgenerator.com',
    ];

    /**
     * Normalize email string safely.
     */
    public static function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    /**
     * Extract domain from normalized email.
     */
    public static function extractDomain(string $email): string
    {
        $normalized = self::normalizeEmail($email);
        $parts = explode('@', $normalized);
        return count($parts) === 2 ? $parts[1] : '';
    }

    /**
     * Validate an email against the active policy.
     * Returns array: ['allowed' => bool, 'reason' => string|null]
     */
    public static function validateEmail(string $email): array
    {
        $email = self::normalizeEmail($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['allowed' => false, 'reason' => 'Invalid email address format.'];
        }

        $domain = self::extractDomain($email);
        if (empty($domain)) {
            return ['allowed' => false, 'reason' => 'Invalid email domain.'];
        }

        // 1. Check exact allowed emails first
        $allowedExactEmails = (array) AuthSetting::get('allowed_exact_emails', []);
        $normalizedAllowedExact = array_map('strtolower', array_map('trim', $allowedExactEmails));
        if (in_array($email, $normalizedAllowedExact, true)) {
            return ['allowed' => true, 'reason' => null];
        }

        // 2. Check exact blocked emails
        $blockedExactEmails = (array) AuthSetting::get('blocked_exact_emails', []);
        $normalizedBlockedExact = array_map('strtolower', array_map('trim', $blockedExactEmails));
        if (in_array($email, $normalizedBlockedExact, true)) {
            return ['allowed' => false, 'reason' => 'This email address is restricted from registration.'];
        }

        // 3. Check domain mode
        $policyMode = AuthSetting::get('email_policy_mode', 'allow_normal_with_disposable_blocklist');

        if ($policyMode === 'allow_configured_only') {
            $allowedDomains = (array) AuthSetting::get('allowed_domains', ['gmail.com', 'outlook.com', 'hotmail.com', 'yahoo.com', 'icloud.com', 'proton.me']);
            $normalizedAllowed = array_map('strtolower', array_map('trim', $allowedDomains));
            if (!in_array($domain, $normalizedAllowed, true)) {
                return ['allowed' => false, 'reason' => 'Registration is restricted to authorized email domains only.'];
            }
            return ['allowed' => true, 'reason' => null];
        }

        // 4. Check if domain is blocked in database or default disposable list
        $isDisposableInDb = DisposableEmailDomain::where('domain', $domain)->where('is_blocked', true)->exists();
        $isWhitelistedInDb = DisposableEmailDomain::where('domain', $domain)->where('is_blocked', false)->exists();

        if ($isWhitelistedInDb) {
            return ['allowed' => true, 'reason' => null];
        }

        if ($isDisposableInDb || in_array($domain, self::$defaultDisposableDomains, true)) {
            return ['allowed' => false, 'reason' => 'Temporary and disposable email addresses are not supported.'];
        }

        return ['allowed' => true, 'reason' => null];
    }
}
