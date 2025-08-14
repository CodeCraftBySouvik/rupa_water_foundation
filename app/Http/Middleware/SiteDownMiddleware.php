<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteDownMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (env('SITE_DOWN', false) === true || env('SITE_DOWN', false) === 'true') {
            abort(503, 'The site is temporarily down for maintenance.');
        }

        return $next($request);
    }
}
