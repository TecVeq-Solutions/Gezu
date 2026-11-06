<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use Illuminate\Http\Request;

class ErrorLogController extends Controller
{
    /**
     * Display live system error logs with search, user context & stacktraces
     */
    public function index(Request $request)
    {
        $pageTitle = 'Real-Time System Error Logs';

        $query = ErrorLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('exception_type', 'like', "%{$search}%")
                  ->orWhere('file', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%");
            });
        }

        if ($request->filled('exception_type')) {
            $query->where('exception_type', $request->input('exception_type'));
        }

        $logs = $query->paginate(20)->withQueryString();
        $totalErrors = ErrorLog::count();
        $todayErrors = ErrorLog::whereDate('created_at', today())->count();
        $distinctTypes = ErrorLog::select('exception_type')
            ->distinct()
            ->take(15)
            ->pluck('exception_type');

        return view('admin.errors.index', compact('pageTitle', 'logs', 'totalErrors', 'todayErrors', 'distinctTypes'));
    }

    /**
     * Clear all recorded error logs
     */
    public function clear()
    {
        ErrorLog::truncate();
        session()->flash('success', 'All system error logs have been cleared successfully.');
        return redirect()->route('admin.errors.index');
    }

    /**
     * Delete a single error log record
     */
    public function destroy($id)
    {
        $log = ErrorLog::findOrFail($id);
        $log->delete();

        session()->flash('success', 'Error log entry deleted.');
        return back();
    }
}
