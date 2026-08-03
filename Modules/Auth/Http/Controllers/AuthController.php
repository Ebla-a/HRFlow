<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Auth\App\Services\AuthService;
use Modules\Auth\Http\Resources\UserAuthResource;
use Modules\Auth\Http\Requests\ForgotPasswordRequest;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\ResetPasswordRequest;
use Modules\Auth\Http\Requests\UpdatePasswordRequest;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    /**
     * Login
     */
    public function login(
        LoginRequest $request
    ): JsonResponse {

        try {

            $data = $this->authService->login(
                $request->toDTO()
            );

            return Controller::success(
                [
                    'access_token' => $data['access_token'],
                    'token_type' => $data['token_type'],
                    'user' => new UserAuthResource($data['user']),
                ],
                'Login successful'
            );

        } catch (\Exception $e) {

            return Controller::error(
               $e->getMessage(),
               is_numeric($e->getCode()) ? (int) $e->getCode() : 500
             );
        }
    }

    /**
     * Logout
     */
    public function logout(
        Request $request
    ): JsonResponse {

        try {

            $this->authService->logout(
                $request
            );

            return Controller::success(
                null,
                'Successfully logged out'
            );

        } catch (\Exception $e) {

            return Controller::error(
                $e->getMessage(),
                is_numeric($e->getCode()) ? (int) $e->getCode() : 500
            );
        }
    }

    /**
     * Authenticated user
     */
    public function me(
        Request $request
    ): JsonResponse {

        try {

            $data = $this->authService->me(
                $request
            );

            return Controller::success(
                new UserAuthResource($data),
                'User profile fetched successfully'
            );

        } catch (\Exception $e) {

           return Controller::error(
               $e->getMessage(),
                is_numeric($e->getCode()) ? (int) $e->getCode() : 500
            );
        }
    }

    /**
     * Update password
     */
    public function updatePassword(
        UpdatePasswordRequest $request
    ): JsonResponse {

        try {

            $this->authService->updatePassword(
                $request->toDTO(),
                $request
            );

            return Controller::success(
                null,
                'Password updated successfully'
            );

        } catch (\Exception $e) {

             return Controller::error(
                 $e->getMessage(),
                 is_numeric($e->getCode()) ? (int) $e->getCode() : 500
            );
        }
    }

    /**
     * Forgot password
     */
    public function forgotPassword(
        ForgotPasswordRequest $request
    ): JsonResponse {

        try {

            $token = $this->authService->forgotPassword(
                $request->validated('email')
            );

            return Controller::success(
                [
                    'reset_token' => $token,
                ],
                'Password reset token generated successfully.'
            );

        } catch (\Exception $e) {

            return Controller::error(
               $e->getMessage(),
               is_numeric($e->getCode()) ? (int) $e->getCode() : 500
            );
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(
        ResetPasswordRequest $request
    ): JsonResponse {

        try {

            $this->authService->resetPassword(
                $request->toDTO()
            );

            return Controller::success(
                null,
                'Password reset successfully'
            );

        } catch (\Exception $e) {

             return Controller::error(
               $e->getMessage(),
               is_numeric($e->getCode()) ? (int) $e->getCode() : 500
            );
        }
    }
}
 