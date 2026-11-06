<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClipperAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user || (!$user->isClipper() && !session()->has('impersonated_by') && !$user->isAdmin())) {
            abort(403, 'Unauthorized. Creator/Clipper access required.');
        }

        return $next($request);
    }
}
