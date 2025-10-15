<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Cors;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('api')
                ->name('api.')
                ->group(base_path('routes/api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            App\Http\Middleware\LogRequest::class,
        ]);
        $middleware->alias([
            'is-admin' => \App\Http\Middleware\IsAdmin::class,
            'is-company' => \App\Http\Middleware\IsCompany::class,
            'is-applicant' => \App\Http\Middleware\IsApplicant::class,
        ]);
        $middleware->append(Cors::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, $request) {
            $errors = $e->errors();

            $firstError = $errors[array_key_first($errors)][0];
            $errorCount = collect($errors)->flatten()->count() - 1;

            $customMessage = $firstError;
            if ($errorCount) {
                $customMessage .= " và còn {$errorCount} lỗi khác";
            }

            return response()->json([
                'message' => $customMessage,
                'errors' => $errors
            ], $e->status);
        });
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        });
        $exceptions->render(function (\Throwable $e, $request) {
            if ($e instanceof HttpExceptionInterface && $e->getStatusCode() === 401) {
                return null;
            }

            return response()->json([
                'message' => 'Có lỗi xảy ra',
                'status_code' => 500
            ], 500);
        });
    })->create();
