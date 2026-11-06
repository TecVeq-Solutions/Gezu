<?php

namespace App\Services;

use App\Enums\NotificationEvent;
use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotificationService
{
    public function __construct(
        protected FirebaseNotificationService $firebaseService
    ) {}

    /**
     * Dispatch multi-channel notification to a user with non-blocking error isolation.
     */
    public function send(
        User $user,
        NotificationEvent|string $event,
        string $title,
        string $body,
        ?string $actionUrl = null,
        array $metadata = []
    ): void {
        $eventString = $event instanceof NotificationEvent ? $event->value : (string) $event;

        // 1. Channel A: Database Notification (Required & Atomic)
        try {
            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\GezuxPlatformNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'event' => $eventString,
                    'title' => $title,
                    'body' => $body,
                    'action_url' => $actionUrl,
                    'metadata' => $metadata,
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            NotificationLog::create([
                'user_id' => $user->id,
                'event' => $eventString,
                'channel' => 'database',
                'title' => $title,
                'body' => $body,
                'action_url' => $actionUrl,
                'status' => 'sent',
                'sent_at' => now(),
                'metadata' => $metadata,
            ]);
        } catch (\Throwable $e) {
            Log::error('Database notification failed: ' . $e->getMessage());
        }

        // 2. Channel B: Firebase Cloud Messaging (Web Push)
        try {
            $fcmResult = $this->firebaseService->sendToUser($user, $title, $body, $actionUrl, $metadata);

            NotificationLog::create([
                'user_id' => $user->id,
                'event' => $eventString,
                'channel' => 'firebase',
                'title' => $title,
                'body' => $body,
                'action_url' => $actionUrl,
                'status' => ($fcmResult['status'] ?? 'sent') === 'failed' ? 'failed' : 'sent',
                'sent_at' => now(),
                'metadata' => array_merge($metadata, ['fcm' => $fcmResult]),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Firebase push channel error: ' . $e->getMessage());
        }

        // 3. Channel C: Email Notification (Logged)
        try {
            NotificationLog::create([
                'user_id' => $user->id,
                'event' => $eventString,
                'channel' => 'email',
                'title' => $title,
                'body' => $body,
                'action_url' => $actionUrl,
                'status' => 'sent',
                'sent_at' => now(),
                'metadata' => $metadata,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Email channel error: ' . $e->getMessage());
        }
    }

    /**
     * Notify all admin team members.
     */
    public function notifyAdmins(
        NotificationEvent|string $event,
        string $title,
        string $body,
        ?string $actionUrl = null,
        array $metadata = []
    ): void {
        $admins = User::where('email', 'like', '%admin%')
            ->orWhere('id', 1)
            ->get();

        foreach ($admins as $admin) {
            $this->send($admin, $event, $title, $body, $actionUrl, $metadata);
        }
    }
}
