<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogRequest
{
    private $dontReport = [
        'password',
        '_token',
        'access_token',
    ];
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->isProduction()) {
            Log::info(
                'request',
                [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'parameters' => $request->except($this->dontReport),
                'ip' => $request->ip(),
                'user_agent' => $request->header('user-agent'),
                ]
            );
        }

        return $next($request);
    }
}
