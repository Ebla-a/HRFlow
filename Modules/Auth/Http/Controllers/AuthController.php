<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Auth\App\Services\AuthService;
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
     * Login user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login(
            $request->toDTO()
        );
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        return $this->authService->logout($request);
    }

    /**
     * Get authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->authService->me($request);
    }

    /**
     * Update authenticated user password.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        return $this->authService->updatePassword(
            $request->toDTO(),
            $request
        );
    }

    /**
     * Send reset password token.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        return $this->authService->forgotPassword(
            $request->validated('email')
        );
    }

    /**
     * Reset password.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        return $this->authService->resetPassword(
            $request->toDTO()
        );
    }
}
