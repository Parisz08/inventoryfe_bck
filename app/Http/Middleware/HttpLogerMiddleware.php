<?php

namespace App\Http\Middleware;
use Closure;
use Exception;
use App\ActivityLog;

class HttpLogerMiddleware
{
    public function handle($request, Closure $next)
    {
        $user             = new ActivityLog;
        $user->method     = $request->method();
        $user->url        = $request->url();
        $user->ip         = $request->ip();
        $user->user_agent = $request->userAgent();
        $user->save();

        return $next($request);
    }
}