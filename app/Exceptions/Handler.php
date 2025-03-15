<?php
namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are handled by the handler.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        UnauthorizedHttpException::class,
        ValidationException::class,
        NotFoundHttpException::class,
    ];

    /**
     * Register the exception handling for the application.
     *
     * @return void
     */
    public function register()
    {
        // Custom exception handling for the API
        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ResponseHelper::error(
                    'Unauthorized. Token may be invalid or expired.',
                    [],
                    401
                );
            }
        });

        $this->renderable(function (UnauthorizedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ResponseHelper::error(
                    'Unauthorized. Token may be invalid or expired.',
                    [],
                    401
                );
            }
        });

        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ResponseHelper::error(
                    'Validation failed',
                    $e->errors(),
                    422
                );
            }
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ResponseHelper::error(
                    'Route not found',
                    [],
                    404
                );
            }
        });
    }
}
