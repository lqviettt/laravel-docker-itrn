<?php

namespace App\Exceptions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e) {
            return $this->handleException($e);
        });
    }

    public function handleException(Throwable $e)
    {
        if ($e instanceof NotFoundHttpException) {
            return response(['error' => 'Item not found.'], 404);
        }

        if ($e instanceof ValidationException) {
            return response(['error' => $e->errors()], 400);
        }

        if ($e instanceof ClientException) {
            return response(['error' => $e->getMessage()], 400);
        }

        if ($e instanceof ServerException) {
            return response(['error' => $e->getMessage()], 500);
        }

        if ($e instanceof AccessDeniedHttpException) {
            return response(['error' => 'This action is unauthorized.'], 403);
        }

        if ($e instanceof BadRequestHttpException) {
            return response(['error' => $e->getMessage()], 400);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response([
                'error' => 'This method not allow.'
            ], 405);
        }

        if ($e instanceof UnauthorizedException) {
            return response([
                'code' => 'unauthorized',
                'error' => $e->getMessage(),
            ], 401);
        }
    }
}