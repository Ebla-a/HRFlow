<?php

namespace Modules\Core\App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Modules\Auth\App\Exceptions\ExpiredResetToken;
use Modules\Auth\App\Exceptions\InactiveUserException;
use Modules\Auth\App\Exceptions\InvalidCredentialsException;
use Modules\Core\App\Exceptions\UserNotFoundException;
use Spatie\Permission\Exceptions\UnauthorizedException as SpatieUnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Modules\Employee\App\Exceptions\InvalidJobTitleForDepartmentException;
use Throwable;

class ExceptionRegistrar
{
    public static function register(Exceptions $exceptions): void
    {
        static::validation($exceptions);
        static::authentication($exceptions);
        static::authorization($exceptions);
        static::notFound($exceptions);
        static::userNotFound($exceptions);
        static::authFailures($exceptions);
        static::invalidJobTitleForDepartment($exceptions);
        static::serverError($exceptions);
    }

    protected static function validation(Exceptions $exceptions): void
    {
        $exceptions->render(fn (ValidationException $e) =>
            static::formatResponse(
                __('Validation failed.'),
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->errors()
            )
        );
    }

    protected static function authentication(Exceptions $exceptions): void
    {
        $exceptions->render(fn (AuthenticationException $e) =>
            static::formatResponse(
                __('Unauthenticated.'),
                Response::HTTP_UNAUTHORIZED
            )
        );
    }

    protected static function authorization(Exceptions $exceptions): void
    {
        $exceptions->render(fn (AuthorizationException|SpatieUnauthorizedException $e) =>
            static::formatResponse(
                __('Forbidden. You do not have the required permissions.'),
                Response::HTTP_FORBIDDEN
            )
        );
    }

    protected static function notFound(Exceptions $exceptions): void
    {
        $exceptions->render(fn (ModelNotFoundException|NotFoundHttpException $e) =>
            static::formatResponse(
                __('Resource not found.'),
                Response::HTTP_NOT_FOUND
            )
        );
    }

    protected static function userNotFound(Exceptions $exceptions): void
    {
        $exceptions->render(fn (UserNotFoundException $e) =>
            static::formatResponse(
                $e->getMessage() ?: __('User not found.'),
                Response::HTTP_NOT_FOUND
            )
        );
    }

    protected static function invalidJobTitleForDepartment(Exceptions $exceptions): void
    {
        $exceptions->render(fn (InvalidJobTitleForDepartmentException $e) =>
            static::formatResponse(
                $e->getMessage() ?: __('Invalid job title for department.'),
                Response::HTTP_UNPROCESSABLE_ENTITY
            )
        );
    }

    protected static function serverError(Exceptions $exceptions): void
    {
        $exceptions->render(function (Throwable $e) {

            if (method_exists($e, 'render')) {
                return null;
            }

            report($e);

            $statusCode = $e instanceof HttpExceptionInterface
                ? $e->getStatusCode()
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            $message = config('app.debug')
                ? $e->getMessage()
                : ($statusCode === Response::HTTP_INTERNAL_SERVER_ERROR
                    ? __('Internal server error.')
                    : $e->getMessage());

            return static::formatResponse($message, $statusCode);
        });
    }


    protected static function formatResponse(string $message, int $status, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }


    protected static function authFailures(Exceptions $exceptions): void
{
    $exceptions->render(fn (InvalidCredentialsException $e) =>
        static::formatResponse(
            $e->getMessage(),
            Response::HTTP_UNAUTHORIZED // 401
        )
    );

    $exceptions->render(fn (InactiveUserException $e) =>
        static::formatResponse(
            $e->getMessage(),
            Response::HTTP_FORBIDDEN // 403
        )
    );

     $exceptions->render(fn (ExpiredResetToken $e) =>
            static::formatResponse(
                $e->getMessage(),
                $e->getCode() ?: Response::HTTP_BAD_REQUEST // 400
            )
        );
}


}
