<?php

namespace App\Http\Middleware;

use App\Services\StopBotService;
use Closure;
use Illuminate\Http\Request;

class StopBotMiddleware
{
    public function __construct(private StopBotService $stopBot)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip() ?? '';
        $ua = $request->userAgent() ?? '';
        $url = $request->fullUrl();

        if ($this->stopBot->blocker($ip, $ua, $url)) {
            return response('Access denied', 403);
        }

        return $next($request);
    }
}
