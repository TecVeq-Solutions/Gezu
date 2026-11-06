<?php

namespace App\Services;

use App\Models\AuthSetting;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VerificationCodeService
{
    /**
     * Generate and store a secure verification OTP code.
     */
    public static function generateCode(string $email, string $purpose = 'email_verification', ?User $user = null): array
    {
        $email = EmailDomainPolicyService::normalizeEmail($email);
        $length = (int) AuthSetting::get('verification_code_length', 6);
        $length = max(4, min(8, $length));
        $codeType = AuthSetting::get('verification_code_type', 'numeric'); // numeric, alphanumeric, letters
        $expiryMinutes = (int) AuthSetting::get('verification_code_expiry_minutes', 10);
        $cooldownSeconds = (int) AuthSetting::get('verification_resend_cooldown_seconds', 60);

        // Check recent code for resend cooldown
        $latestCode = VerificationCode::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest('created_at')
            ->first();

        if ($latestCode && $latestCode->created_at->diffInSeconds(now()) < $cooldownSeconds) {
            $remaining = $cooldownSeconds - $latestCode->created_at->diffInSeconds(now());
            return [
                'success' => false,
                'error' => "Please wait {$remaining} seconds before requesting a new code.",
                'cooldown_remaining' => $remaining,
            ];
        }

        // Check hourly rate limit
        $hourlyLimit = (int) AuthSetting::get('verification_hourly_limit', 5);
        $sentLastHour = VerificationCode::where('email', $email)
            ->where('purpose', $purpose)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($sentLastHour >= $hourlyLimit) {
            return [
                'success' => false,
                'error' => 'Too many verification requests. Please try again in an hour.',
            ];
        }

        // Generate safe random code
        $rawCode = self::generateRawCode($length, $codeType);
        $codeHash = hash('sha256', $rawCode);

        // Invalidate previous unconsumed codes for the same purpose
        VerificationCode::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $record = VerificationCode::create([
            'user_id' => $user?->id ?? User::where('email', $email)->value('id'),
            'email' => $email,
            'code_hash' => $codeHash,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes($expiryMinutes),
            'attempts' => 0,
            'sent_count' => 1,
            'ip_address' => request()?->ip() ?? '127.0.0.1',
        ]);

        return [
            'success' => true,
            'code' => $rawCode, // Plaintext returned ONLY for immediate email dispatch
            'expires_at' => $record->expires_at,
            'expires_in_minutes' => $expiryMinutes,
        ];
    }

    /**
     * Validate and consume a submitted verification code.
     */
    public static function verifyCode(string $email, string $code, string $purpose = 'email_verification'): array
    {
        $email = EmailDomainPolicyService::normalizeEmail($email);
        $code = trim($code);
        $maxAttempts = (int) AuthSetting::get('verification_max_attempts', 5);

        $record = VerificationCode::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest('created_at')
            ->first();

        if (!$record) {
            return [
                'valid' => false,
                'error' => 'No active verification code found. Please request a new code.',
            ];
        }

        if ($record->isExpired()) {
            return [
                'valid' => false,
                'error' => 'The verification code has expired. Please request a new code.',
            ];
        }

        if ($record->hasExceededAttempts($maxAttempts)) {
            $record->update(['consumed_at' => now()]);
            return [
                'valid' => false,
                'error' => 'Too many invalid attempts. This code has been invalidated. Please request a new code.',
            ];
        }

        // Compare secure SHA-256 hash
        $providedHash = hash('sha256', $code);
        if (!hash_equals($record->code_hash, $providedHash)) {
            $record->increment('attempts');
            $remaining = $maxAttempts - $record->attempts;
            return [
                'valid' => false,
                'error' => $remaining > 0 
                    ? "Incorrect verification code. {$remaining} attempts remaining."
                    : 'Incorrect code. Maximum attempts reached.',
            ];
        }

        // Mark consumed immediately to prevent replay
        $record->update(['consumed_at' => now()]);

        // If email_verification, also mark user email_verified_at
        $user = User::where('email', $email)->first();
        if ($user && $purpose === 'email_verification' && !$user->email_verified_at) {
            $user->update(['email_verified_at' => now()]);
        }

        return [
            'valid' => true,
            'user' => $user,
        ];
    }

    /**
     * Helper to generate cryptographically secure code without ambiguous characters.
     */
    protected static function generateRawCode(int $length, string $type): string
    {
        if ($type === 'numeric') {
            $min = (int) ('1' . str_repeat('0', $length - 1));
            $max = (int) str_repeat('9', $length);
            return (string) random_int($min, $max);
        }

        // Safe characters avoiding ambiguous 0/O, 1/I/l
        $chars = ($type === 'letters')
            ? 'ABCDEFGHJKLMNPQRSTUVWXYZ'
            : '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        $code = '';
        $maxIndex = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, $maxIndex)];
        }

        return $code;
    }
}
