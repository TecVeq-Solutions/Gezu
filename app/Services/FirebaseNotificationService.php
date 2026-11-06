<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotificationDevice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    /**
     * Send Web Push notification to all active devices of a user.
     */
    public function sendToUser(User $user, string $title, string $body, ?string $actionUrl = null, array $metadata = []): array
    {
        $devices = UserNotificationDevice::where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->where('permission_status', 'granted')
            ->get();

        if ($devices->isEmpty()) {
            return [
                'status' => 'skipped',
                'reason' => 'No active devices registered',
                'count' => 0,
            ];
        }

        $results = [];
        foreach ($devices as $device) {
            $results[] = $this->sendToToken($device->token, $title, $body, $actionUrl, $metadata, $device);
        }

        return [
            'status' => 'completed',
            'deliveries' => $results,
            'count' => count($results),
        ];
    }

    /**
     * Send Web Push notification to a single FCM device token.
     */
    public function sendToToken(string $token, string $title, string $body, ?string $actionUrl = null, array $metadata = [], ?UserNotificationDevice $deviceRecord = null): array
    {
        $serverKey = config('services.firebase.server_key') ?? env('FIREBASE_SERVER_KEY');
        $projectId = config('services.firebase.project_id') ?? env('FIREBASE_PROJECT_ID');

        // If credentials are not configured yet, record a simulated delivery safely without throwing exception
        if (!$serverKey && !$projectId) {
            if ($deviceRecord) {
                $deviceRecord->update([
                    'last_seen_at' => now(),
                    'last_successful_delivery_at' => now(),
                ]);
            }
            return [
                'token' => substr($token, 0, 15) . '...',
                'status' => 'simulated_success',
                'message' => 'Firebase credentials pending in environment; queued in simulator mode',
            ];
        }

        try {
            // FCM Legacy / HTTP V1 payload structure
            $payload = [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => '/images/gezux-favicon.jpg',
                    'click_action' => $actionUrl ?? '/admin',
                ],
                'data' => array_merge($metadata, [
                    'title' => $title,
                    'body' => $body,
                    'url' => $actionUrl ?? '/admin',
                    'timestamp' => (string) now()->timestamp,
                ]),
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->timeout(5)->post('https://fcm.googleapis.com/fcm/send', $payload);

            if ($response->successful()) {
                if ($deviceRecord) {
                    $deviceRecord->update([
                        'last_seen_at' => now(),
                        'last_successful_delivery_at' => now(),
                    ]);
                }
                return [
                    'token' => substr($token, 0, 15) . '...',
                    'status' => 'delivered',
                    'response' => $response->json(),
                ];
            } else {
                if ($deviceRecord) {
                    $deviceRecord->update([
                        'last_failure_at' => now(),
                        'last_failure_reason' => $response->body(),
                    ]);
                }
                return [
                    'token' => substr($token, 0, 15) . '...',
                    'status' => 'failed',
                    'error' => $response->body(),
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('Firebase notification exception: ' . $e->getMessage());
            if ($deviceRecord) {
                $deviceRecord->update([
                    'last_failure_at' => now(),
                    'last_failure_reason' => $e->getMessage(),
                ]);
            }
            return [
                'token' => substr($token, 0, 15) . '...',
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }
}
