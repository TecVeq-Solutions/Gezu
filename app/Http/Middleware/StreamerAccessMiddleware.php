<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StreamerAccessMiddleware
{
    /**
     * Handle an incoming request for Streamer Portal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user || (!$user->isStreamer() && !session()->has('impersonated_by') && !$user->isAdmin())) {
            abort(403, 'Unauthorized. Streamer portal access required.');
        }

        return $next($request);
    }
}
