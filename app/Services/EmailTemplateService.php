<?php

namespace App\Services;

use App\Models\AuthSetting;

class EmailTemplateService
{
    /**
     * Default template blueprints.
     */
    protected static array $defaultTemplates = [
        'email_verification' => [
            'subject' => 'Your GEZUX Verification Code',
            'heading' => 'Verify Your Email Address',
            'body' => 'Use the secure verification code below to verify your GEZUX account. This code expires in {{ expires_in }}. If you did not request this code, you can safely ignore this email.',
            'code_presentation' => 'Your GEZUX verification code is: {{ verification_code }}',
        ],
        'password_reset' => [
            'subject' => 'Your GEZUX Password Reset Code',
            'heading' => 'Reset Your Password',
            'body' => 'We received a request to reset your GEZUX password. Enter the 6-digit code below to set a new password. This code expires in {{ expires_in }}.',
            'code_presentation' => 'Your password reset code is: {{ verification_code }}',
        ],
        'welcome_brand' => [
            'subject' => 'Welcome to GEZUX — Launch Your Creator Campaigns',
            'heading' => 'Welcome to GEZUX, {{ user_name }}!',
            'body' => 'Your brand account is active. You can now create video campaigns, fund campaign bounties, and partner with top verified clippers.',
            'code_presentation' => null,
        ],
        'welcome_clipper' => [
            'subject' => 'Welcome to GEZUX — Start Clipping & Earning',
            'heading' => 'Welcome to GEZUX, {{ user_name }}!',
            'body' => 'Your creator profile is active. Browse open marketplace campaigns, submit high-performing clips, and earn verified rewards.',
            'code_presentation' => null,
        ],
        'security_alert' => [
            'subject' => 'GEZUX Security Alert — New Sign In',
            'heading' => 'New Sign In Detected',
            'body' => 'A new sign-in was detected on your GEZUX account at {{ timestamp }} from IP {{ ip_address }}. If this was not you, please secure your account immediately.',
            'code_presentation' => null,
        ],
    ];

    /**
     * Render template safely using whitelisted placeholder replacement only.
     * Prevents arbitrary PHP execution or SSTI.
     */
    public static function render(string $templateKey, array $variables = []): array
    {
        $customTemplates = (array) AuthSetting::get('email_templates', []);
        $template = $customTemplates[$templateKey] ?? self::$defaultTemplates[$templateKey] ?? [
            'subject' => 'GEZUX Notification',
            'heading' => 'GEZUX Platform Notification',
            'body' => 'You have a new update from GEZUX.',
            'code_presentation' => null,
        ];

        // Safe global context variables
        $defaultVars = [
            'app_name' => config('app.name', 'GEZUX'),
            'year' => date('Y'),
            'support_email' => 'support@gezux.io',
            'timestamp' => now()->toDateTimeString(),
        ];

        $mergedVars = array_merge($defaultVars, $variables);

        $renderedSubject = strip_tags(self::replacePlaceholders($template['subject'] ?? '', $mergedVars));
        $renderedHeading = strip_tags(self::replacePlaceholders($template['heading'] ?? '', $mergedVars));
        $renderedBody = self::replacePlaceholders($template['body'] ?? '', $mergedVars);
        $renderedCodePresentation = !empty($template['code_presentation']) 
            ? strip_tags(self::replacePlaceholders($template['code_presentation'], $mergedVars)) 
            : null;

        return [
            'subject' => $renderedSubject,
            'heading' => $renderedHeading,
            'body' => $renderedBody,
            'code_presentation' => $renderedCodePresentation,
        ];
    }

    /**
     * Whitelist placeholder replacer (e.g. {{ user_name }} -> John).
     */
    public static function replacePlaceholders(string $text, array $vars): string
    {
        // 1. Replace whitelisted placeholders
        $processed = preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($matches) use ($vars) {
            $key = $matches[1];
            return isset($vars[$key]) && is_scalar($vars[$key]) ? htmlspecialchars((string) $vars[$key], ENT_QUOTES, 'UTF-8') : '';
        }, $text);

        // 2. Safely strip any remaining unapproved curly expressions / injection attempts
        return preg_replace('/\{\{.*?\}\}/', '', $processed);
    }

    /**
     * Get all templates for admin management.
     */
    public static function getAllTemplates(): array
    {
        $custom = (array) AuthSetting::get('email_templates', []);
        $result = [];
        foreach (self::$defaultTemplates as $key => $default) {
            $result[$key] = array_merge($default, $custom[$key] ?? []);
        }
        return $result;
    }
}
