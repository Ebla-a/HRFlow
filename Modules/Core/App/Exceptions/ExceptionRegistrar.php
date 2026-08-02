<?php

namespace Modules\Core\App\Exceptions;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException as SpatieUnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionRegistrar
{
    protected static Controller $controller;

    public static function register(Exceptions $exceptions): void
    {

        static::validation($exceptions);
        static::authentication($exceptions);
        static::authorization($exceptions);
        static::notFound($exceptions);
        static::userNotFound($exceptions);
        static::serverError($exceptions);

    }

    protected static function validation(Exceptions $exceptions): void
    {
        $exceptions->render(fn (ValidationException $e) =>
            controller::error(
                'Validation failed.',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->errors()
            )
        );
    }

    protected static function authentication(Exceptions $exceptions): void
    {
        $exceptions->render(fn (AuthenticationException $e) =>
           controller::error(
                'Unauthenticated.',
                Response::HTTP_UNAUTHORIZED
            )
        );
    }

    protected static function authorization(Exceptions $exceptions): void
    {
        $exceptions->render(fn (AuthorizationException|SpatieUnauthorizedException $e) =>
            controller::error(
                'Forbidden. You do not have the required permissions.',
                Response::HTTP_FORBIDDEN
            )
        );
    }

    protected static function notFound(Exceptions $exceptions): void
    {
        $exceptions->render(fn (ModelNotFoundException|NotFoundHttpException $e) =>
          controller::error(
                'Resource not found.',
                Response::HTTP_NOT_FOUND
            )
        );
    }

    protected static function serverError(Exceptions $exceptions): void
    {
        $exceptions->render(function (Throwable $e) {

            report($e);

            $statusCode = $e instanceof HttpExceptionInterface
                ? $e->getStatusCode()
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            $message = config('app.debug')
                ? $e->getMessage()
                : ($statusCode === Response::HTTP_INTERNAL_SERVER_ERROR
                    ? 'Internal server error.'
                    : $e->getMessage());

            return controller::error($message, $statusCode);
        });
    }

    protected static function userNotFound(Exceptions $exceptions): void
    {
        $exceptions->render(fn (UserNotFoundException $e) =>
            controller::error(
                $e->getMessage() ?: 'User not found.',
                Response::HTTP_NOT_FOUND
            )
        );
    }
}
