<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BrandAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user || (!$user->isBrand() && !session()->has('impersonated_by') && !$user->isAdmin())) {
            abort(403, 'Unauthorized. Brand advertiser access required.');
        }

        return $next($request);
    }
}
