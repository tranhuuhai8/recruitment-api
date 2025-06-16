<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('admin')->user();
        if ($user && (int) $user->role === User::ROLE_ADMIN && (int) $user->status == User::STATUS_ACTIVE) {
            return $next($request);
        }

        return ResponseHelper::sendResponse(ResponseHelper::STATUS_CODE_FORBIDDEN, trans('auth.permission_denied'));
    }
}
