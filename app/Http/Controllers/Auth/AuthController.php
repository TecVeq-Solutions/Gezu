<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuthSetting;
use App\Models\User;
use App\Services\AuthDomainService;
use App\Services\EmailDomainPolicyService;
use App\Services\VerificationCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Get public authentication configuration (Google status, code length, etc.).
     */
    public function config(): JsonResponse
    {
        return response()->json([
            'google_oauth_enabled' => (bool) AuthSetting::get('google_oauth_enabled', false),
            'verification_required' => (bool) AuthSetting::get('email_verification_required', true),
            'code_length' => (int) AuthSetting::get('verification_code_length', 6),
            'cooldown_seconds' => (int) AuthSetting::get('verification_resend_cooldown_seconds', 60),
            'allowed_roles' => ['brand', 'clipper', 'streamer'],
        ]);
    }

    /**
     * User registration with strict role validation.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:brand,clipper,streamer'],
        ]);

        $result = AuthDomainService::register($validated);

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully. Please enter the verification code sent to your email.',
            'email' => $result['email'],
            'role' => $result['role'],
            'requires_verification' => true,
        ], 201);
    }

    /**
     * Login with rate limiting and server-side role resolution.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
            'portal' => ['nullable', 'string', 'in:brand,clipper,streamer'],
            'intended_role' => ['nullable', 'string', 'in:brand,clipper,streamer'],
        ]);

        $expectedRole = $validated['portal'] ?? $validated['intended_role'] ?? null;

        $result = AuthDomainService::login(
            $validated['email'],
            $validated['password'],
            $request->boolean('remember'),
            $request->ip(),
            $expectedRole
        );

        return response()->json([
            'success' => true,
            'message' => 'Signed in successfully.',
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
                'role' => $result['role'],
            ],
            'redirect_url' => $result['redirect_url'],
        ]);
    }

    /**
     * Verify email with OTP code.
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'code' => ['required', 'string', 'min:4', 'max:8'],
        ]);

        $result = AuthDomainService::verifyEmail($validated['email'], $validated['code']);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully.',
            'redirect_url' => $result['redirect_url'],
        ]);
    }

    /**
     * Resend verification code with cooldown enforcement.
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $email = EmailDomainPolicyService::normalizeEmail($validated['email']);
        $user = User::where('email', $email)->first();

        if ($user && $user->email_verified_at) {
            return response()->json([
                'success' => true,
                'message' => 'Your email is already verified. You can sign in.',
            ]);
        }

        $codeResult = VerificationCodeService::generateCode($email, 'email_verification', $user);

        if (!$codeResult['success']) {
            return response()->json([
                'success' => false,
                'message' => $codeResult['error'],
                'cooldown_remaining' => $codeResult['cooldown_remaining'] ?? 0,
            ], 429);
        }

        AuthDomainService::sendVerificationEmail(
            $email,
            $user?->name ?? 'Creator',
            $codeResult['code'],
            $codeResult['expires_in_minutes']
        );

        return response()->json([
            'success' => true,
            'message' => 'A new verification code has been dispatched to your email.',
            'expires_in_minutes' => $codeResult['expires_in_minutes'],
        ]);
    }

    /**
     * Request password reset code (Safe against account enumeration).
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $email = EmailDomainPolicyService::normalizeEmail($validated['email']);
        $user = User::where('email', $email)->first();

        if ($user) {
            $codeResult = VerificationCodeService::generateCode($email, 'password_reset', $user);
            if ($codeResult['success']) {
                $rendered = \App\Services\EmailTemplateService::render('password_reset', [
                    'user_name' => $user->name,
                    'verification_code' => $codeResult['code'],
                    'expires_in' => "{$codeResult['expires_in_minutes']} minutes",
                ]);

                try {
                    \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\GezuxPlatformEmail(
                        subjectText: $rendered['subject'],
                        heading: $rendered['heading'],
                        bodyContent: $rendered['body'],
                        actionUrl: null,
                        contextData: [
                            'recipient' => $email,
                            'purpose' => 'Password Reset OTP',
                        ],
                        codePresentation: $codeResult['code']
                    ));
                } catch (\Throwable $e) {
                    // Safe error logging
                }
            }
        }

        // Generic response to avoid account enumeration
        return response()->json([
            'success' => true,
            'message' => 'If an account exists with this email address, a password reset code has been sent.',
        ]);
    }

    /**
     * Reset password using OTP code.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'code' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = EmailDomainPolicyService::normalizeEmail($validated['email']);
        $verify = VerificationCodeService::verifyCode($email, $validated['code'], 'password_reset');

        if (!$verify['valid']) {
            throw ValidationException::withMessages([
                'code' => [$verify['error']],
            ]);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Account not found.'],
            ]);
        }

        $user->password = $validated['password'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You may now sign in with your new password.',
        ]);
    }

    /**
     * Redirect user to Google OAuth 2.0 gateway.
     */
    public function googleRedirect()
    {
        try {
            $url = \App\Services\GoogleAuthService::generateAuthUrl();
            return redirect()->away($url);
        } catch (\Throwable $e) {
            return redirect('/login')->withErrors(['oauth' => $e->getMessage()]);
        }
    }

    /**
     * Handle Google OAuth callback.
     */
    public function googleCallback(Request $request)
    {
        try {
            $result = \App\Services\GoogleAuthService::handleCallback($request);

            if ($result['is_new_user']) {
                return redirect('/register?google=1');
            }

            return redirect($result['redirect_url'] ?? '/marketplace');
        } catch (\Throwable $e) {
            return redirect('/login')->withErrors(['oauth' => $e->getMessage()]);
        }
    }

    /**
     * Get pending Google OAuth registration data from session.
     */
    public function pendingGoogleAuth(): JsonResponse
    {
        $pending = session('pending_google_auth');
        if (!$pending) {
            return response()->json(['pending' => false]);
        }

        return response()->json([
            'pending' => true,
            'email' => $pending['email'],
            'name' => $pending['name'],
        ]);
    }

    /**
     * Complete Google Registration with user-selected role (Brand, Clipper, or Streamer).
     */
    public function completeGoogleRegistration(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:brand,clipper,streamer'],
        ]);

        $user = \App\Services\GoogleAuthService::completeGoogleRegistration($validated['role']);

        return response()->json([
            'success' => true,
            'message' => 'Account created with Google successfully.',
            'redirect_url' => $user->getRedirectUrl(),
        ]);
    }

    /**
     * User logout.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'success' => true,
            'message' => 'Signed out successfully.',
            'redirect_url' => '/login',
        ]);
    }
}
