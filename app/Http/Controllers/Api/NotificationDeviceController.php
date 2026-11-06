<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNotificationDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationDeviceController extends Controller
{
    /**
     * Register or update an FCM device token for web push notifications.
     */
    public function registerDevice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string|max:500',
            'device_type' => 'nullable|string|max:50',
            'platform' => 'nullable|string|max:50',
            'permission_status' => 'nullable|string|in:granted,denied,default',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $userId = $validated['user_id'] ?? auth()->id() ?? User::where('email', 'admin@gezux.io')->value('id') ?? 1;

        $device = UserNotificationDevice::updateOrCreate(
            ['token' => $validated['token']],
            [
                'user_id' => $userId,
                'device_type' => $validated['device_type'] ?? 'desktop_browser',
                'platform' => $validated['platform'] ?? 'web',
                'user_agent' => $request->userAgent(),
                'permission_status' => $validated['permission_status'] ?? 'granted',
                'last_seen_at' => now(),
                'revoked_at' => null,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Notification device token registered successfully.',
            'device_id' => $device->id,
        ]);
    }
}
