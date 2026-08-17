<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Auth\App\DTOs\ChangePasswordDTO;
use Modules\Auth\App\DTOs\LoginDTO;
use Modules\Auth\App\DTOs\ResetPasswordDTO;
use Modules\Auth\Http\Requests\ForgotPasswordRequest;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\ResetPasswordRequest;
use Modules\Auth\Http\Requests\UpdatePasswordRequest;
use Modules\Auth\Http\Resources\UserAuthResource;
use Modules\Auth\Services\AuthService;


final class AuthController extends Controller
{

    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    /**
     * Authenticate user and return access token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $dto = new LoginDTO(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        $result = $this->authService->login($dto);

        return $this->success(
            data: [
                'access_token' => $result['access_token'],
                'token_type' => $result['token_type'],
                'user' => new UserAuthResource($result['user']),
            ],
            message: __('Login successful')
        );
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request);

        return $this->success(
            data: [],
            message: __('Logout successful')
        );
    }

    /**
     * Return the authenticated user's profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->me($request);

        return $this->success(
            data: new UserAuthResource($user),
            message: __('User retrieved successfully')
        );
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(
        UpdatePasswordRequest $request
    ): JsonResponse {
        $dto = new ChangePasswordDTO(
            currentPassword: $request->validated('current_password'),
            password: $request->validated('password'),
        );

        $this->authService->updatePassword(
            $dto,
            $request
        );

        return $this->success(
            data: [],
            message: __('Password updated successfully')
        );
    }

    /**
     * Send password reset token.
     */
    public function forgotPassword(
        ForgotPasswordRequest $request
    ): JsonResponse {
        $token = $this->authService->forgotPassword(
            $request->validated('email')
        );

        return $this->success(
            data: [
                'token' => $token,
            ],
            message: __('Password reset token generated successfully')
        );
    }

    /**
     * Reset user's password.
     */
    public function resetPassword(
        ResetPasswordRequest $request
    ): JsonResponse {
        $dto = new ResetPasswordDTO(
            email: $request->validated('email'),
            token: $request->validated('token'),
            password: $request->validated('password'),
        );

        $this->authService->resetPassword($dto);

        return $this->success(
            data: [],
            message: __('Password reset successfully')
        );
    }



    
}