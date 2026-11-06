<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SystemLogController extends Controller
{
    /**
     * Get parsed application error logs from storage/logs/laravel.log
     */
    public function index(Request $request): JsonResponse
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!File::exists($logFile)) {
            return response()->json([
                'status' => 'success',
                'logs' => [],
                'file_size' => '0 KB',
                'total_entries' => 0
            ]);
        }

        $content = File::get($logFile);
        $sizeBytes = File::size($logFile);
        $fileSize = round($sizeBytes / 1024, 2) . ' KB';

        // Parse log entries [YYYY-MM-DD HH:MM:SS] environment.LEVEL: message
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] ([a-z0-9_-]+)\.([A-Z]+): (.*?)(?=(?:^\[\d{4}-\d{2}-\d{2})|\z)/ms';
        
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        
        $logs = [];
        $limit = $request->input('limit', 50);

        foreach (array_reverse($matches) as $match) {
            $timestamp = $match[1] ?? '';
            $env = $match[2] ?? 'production';
            $level = $match[3] ?? 'ERROR';
            $fullMessage = trim($match[4] ?? '');

            // Split first line and stacktrace
            $lines = explode("\n", $fullMessage);
            $message = $lines[0] ?? '';
            $stackTrace = count($lines) > 1 ? implode("\n", array_slice($lines, 1)) : '';

            $logs[] = [
                'id' => md5($timestamp . $message),
                'timestamp' => $timestamp,
                'environment' => $env,
                'level' => $level,
                'message' => $message,
                'stacktrace' => substr($stackTrace, 0, 4000),
            ];

            if (count($logs) >= $limit) {
                break;
            }
        }

        return response()->json([
            'status' => 'success',
            'logs' => $logs,
            'file_size' => $fileSize,
            'total_entries' => count($matches)
        ]);
    }

    /**
     * Clear application log file
     */
    public function clear(): JsonResponse
    {
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            File::put($logFile, '');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Application log cleared successfully.'
        ]);
    }
}
