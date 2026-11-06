<?php

namespace App\Services;

use App\Models\SocialAccount;
use App\Models\User;
use Exception;

class SocialAccountService
{
    public const SUPPORTED_PLATFORMS = [
        'TikTok' => [
            'name' => 'TikTok',
            'icon' => 'heroicon-o-video-camera',
            'color' => '#FE2C55',
            'handle_prefix' => '@',
        ],
        'Instagram' => [
            'name' => 'Instagram',
            'icon' => 'heroicon-o-camera',
            'color' => '#E1306C',
            'handle_prefix' => '@',
        ],
        'YouTube Shorts' => [
            'name' => 'YouTube Shorts',
            'icon' => 'heroicon-o-play',
            'color' => '#FF0000',
            'handle_prefix' => '@',
        ],
        'Facebook' => [
            'name' => 'Facebook Reels / Pages',
            'icon' => 'heroicon-o-globe-alt',
            'color' => '#1877F2',
            'handle_prefix' => '@',
        ],
        'X' => [
            'name' => 'X (Twitter)',
            'icon' => 'heroicon-o-chat-bubble-left',
            'color' => '#000000',
            'handle_prefix' => '@',
        ],
        'LinkedIn' => [
            'name' => 'LinkedIn Video / Pulse',
            'icon' => 'heroicon-o-briefcase',
            'color' => '#0A66C2',
            'handle_prefix' => 'in/',
        ],
        'Pinterest' => [
            'name' => 'Pinterest Idea Pins',
            'icon' => 'heroicon-o-photo',
            'color' => '#E60023',
            'handle_prefix' => '@',
        ],
        'Threads' => [
            'name' => 'Threads',
            'icon' => 'heroicon-o-at-symbol',
            'color' => '#000000',
            'handle_prefix' => '@',
        ],
    ];

    /**
     * Connect or update a creator's social account.
     */
    public static function connectAccount(User $user, string $platform, array $data): SocialAccount
    {
        $platformKey = self::normalizePlatform($platform);

        $username = trim($data['username'] ?? '');
        if (empty($username)) {
            throw new Exception('A valid username or handle is required.');
        }

        // Clean handle prefix
        $username = ltrim($username, '@');

        return SocialAccount::updateOrCreate(
            [
                'user_id' => $user->id,
                'platform' => $platformKey,
            ],
            [
                'external_account_id' => $data['external_account_id'] ?? ('soc_' . uniqid()),
                'username' => $username,
                'display_name' => $data['display_name'] ?? $user->name,
                'avatar_url' => $data['avatar_url'] ?? null,
                'followers_count' => (int) ($data['followers_count'] ?? 1000),
                'verified' => true,
                'access_token' => $data['access_token'] ?? ('token_' . bin2hex(random_bytes(16))),
                'refresh_token' => $data['refresh_token'] ?? ('ref_' . bin2hex(random_bytes(16))),
                'token_expires_at' => now()->addDays(90),
                'connected_at' => now(),
                'last_synced_at' => now(),
                'status' => 'connected',
            ]
        );
    }

    /**
     * Disconnect a social account.
     */
    public static function disconnectAccount(User $user, string $platform): bool
    {
        $platformKey = self::normalizePlatform($platform);

        $account = SocialAccount::where('user_id', $user->id)
            ->where('platform', $platformKey)
            ->first();

        if (!$account) {
            return false;
        }

        return (bool) $account->delete();
    }

    /**
     * Refresh/Sync metrics and telemetry for a social account.
     */
    public static function syncAccount(SocialAccount $account): SocialAccount
    {
        $account->update([
            'last_synced_at' => now(),
            'status' => 'connected',
        ]);

        return $account;
    }

    /**
     * Get list of all platforms with connection status for a user.
     */
    public static function getPlatformsStatus(User $user): array
    {
        $userAccounts = $user->socialAccounts->keyBy('platform');
        $result = [];

        foreach (self::SUPPORTED_PLATFORMS as $key => $meta) {
            $account = $userAccounts->get($key);
            $result[$key] = [
                'platform' => $key,
                'name' => $meta['name'],
                'icon' => $meta['icon'],
                'color' => $meta['color'],
                'is_connected' => $account !== null,
                'account' => $account,
                'username' => $account?->username,
                'followers_count' => $account?->followers_count ?? 0,
                'last_synced' => $account?->last_synced_at ? $account->last_synced_at->diffForHumans() : null,
                'status' => $account?->status ?? 'not_connected',
            ];
        }

        return $result;
    }

    public static function normalizePlatform(string $platform): string
    {
        $p = strtolower(trim($platform));
        if (str_contains($p, 'tiktok')) return 'TikTok';
        if (str_contains($p, 'instagram')) return 'Instagram';
        if (str_contains($p, 'youtube')) return 'YouTube Shorts';
        if (str_contains($p, 'facebook') || str_contains($p, 'fb')) return 'Facebook';
        if (str_contains($p, 'twitter') || str_contains($p, 'x')) return 'X';
        if (str_contains($p, 'linkedin')) return 'LinkedIn';
        if (str_contains($p, 'pinterest')) return 'Pinterest';
        if (str_contains($p, 'threads')) return 'Threads';
        return $platform;
    }
}
