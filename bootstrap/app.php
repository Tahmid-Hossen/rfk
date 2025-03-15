<?php

use App\Helpers\ResponseHelper;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Configure API middleware
        $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class,  // Handles stateful requests for Sanctum (optional)
            SubstituteBindings::class,  // Binds route parameters to models
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle ValidationException for API requests
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ResponseHelper::error(
                    'Validation failed',
                    $e->errors(),
                    422
                );
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ResponseHelper::error(
                    'Unauthorized. Token may be invalid or expired.',
                    [],
                    401
                );
            }
        });

        /*$exceptions->render(function (UnauthorizedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ResponseHelper::error(
                    'Unauthorized. Token may be invalid or expired.',
                    [],
                    401
                );
            }
        });*/

        // Optionally handle NotFoundHttpException if route is not found
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ResponseHelper::error(
                    'Route not found',
                    [],
                    404
                );
            }
        });

    })
    ->create();
