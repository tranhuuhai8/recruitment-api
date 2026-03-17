<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth('api')->user();

        $roleMap = [
            'admin' => User::ROLE_ADMIN,
            'company' => User::ROLE_COMPANY,
            'applicant' => User::ROLE_APPLICANT,
        ];

        $allowedRoles = array_map(fn ($role) => $roleMap[$role] ?? null, $roles);

        if (!$user || !in_array($user->role, $allowedRoles, strict: true)) {
            return response()->json(
                ['message' => trans('auth.permission_denied')],
                ResponseHelper::STATUS_CODE_FORBIDDEN
            );
        }

        if ($user->status === User::STATUS_UNVERIFIED) {
            return response()->json(
                ['message' => trans('auth.not_active')],
                ResponseHelper::STATUS_CODE_FORBIDDEN
            );
        }

        if ($user->status === User::STATUS_LOCKED) {
            return response()->json(
                ['message' => trans('auth.locked')],
                ResponseHelper::STATUS_CODE_FORBIDDEN
            );
        }

        return $next($request);
    }
}
