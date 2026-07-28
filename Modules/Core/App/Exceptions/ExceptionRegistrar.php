<?php

namespace Modules\Core\App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionRegistrar
{
    /**
     * @param Exceptions $exceptions
     * @return void
     */
    public static function register(Exceptions $exceptions): void
    {
        static::validation($exceptions);
        static::authentication($exceptions);
        static::authorization($exceptions);
        static::notFound($exceptions);
        static::serverError($exceptions);
    }
    /**
     * 
     * @param Exceptions $exceptions
     * @return void
     */
    protected static function validation(Exceptions $exceptions): void
    {
        $exceptions->render(function (ValidationException $e) {

            return response()->json([
              'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        });
    }
    /**
     * @param Exceptions $exceptions
     * @return void
     */
    protected static function authentication(Exceptions $exceptions): void
    {
        $exceptions->render(function (AuthenticationException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);

        });
    }
    /**
     * @param Exceptions $exceptions
     * @return void
     */
    protected static function authorization(Exceptions $exceptions): void
    {
        $exceptions->render(function (AuthorizationException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Forbidden',
            ], 403);

        });
    }
    /**
     * @param Exceptions $exceptions
     * @return void
     */
    protected static function notFound(Exceptions $exceptions): void
    {
        $exceptions->render(function (
            ModelNotFoundException|NotFoundHttpException $e
        ) {

            return response()->json([
               'status' => false,
                'message' => 'Resource not found',
            ], 404);

        });
    }
    /**
     * @param Exceptions $exceptions
     * @return void
     */
    protected static function serverError(Exceptions $exceptions): void
    {
        $exceptions->render(function (Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Internal Server Error',
            ], 500);

        });
    }
}