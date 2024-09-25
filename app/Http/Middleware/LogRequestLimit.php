<?php
namespace App\Http\Middleware;
use Closure;

class LogRequestLimit
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        \Log::debug($response->headers->get('x-ratelimit-limit'));

        return $response;
    }
}