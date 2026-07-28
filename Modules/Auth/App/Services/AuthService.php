<?php

namespace Modules\Auth\App\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Auth\App\DTOs\ChangePasswordDTO;
use Modules\Auth\App\DTOs\LoginDTO;
use Modules\Auth\App\DTOs\ResetPasswordDTO;
use Modules\Auth\Http\Resources\UserAuthResource;

class AuthService
{
    /**
     * Return unified API response.
     */
    private function apiResponse(
        string $status,
        string $message,
        mixed $data = null,
        mixed $errors = null,
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
            'meta' => null,
        ], $code);
    }

    /**
     * Login user.
     */
    public function login(LoginDTO $dto): JsonResponse
    {
        /** @var User|null $user */
        $user = User::query()
            ->where('email', $dto->email)
            ->first();

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            return $this->apiResponse(
                'error',
                'Invalid credentials provided.',
                null,
                [
                    'email' => [
                        'Invalid email or password.',
                    ],
                ],
                401
            );
        }

        if ($user->is_active === false) {
            return $this->apiResponse(
                'error',
                'Your account is deactivated.',
                null,
                null,
                403
            );
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->apiResponse(
            'success',
            'Login successful',
            [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserAuthResource($user),
            ]
        );
    }

   /**
 * Logout user.
 */
  public function logout(Request $request): JsonResponse
   {
    /** @var User|null $user */
    $user = $request->user();

    if ($user) {

        /** @var \Laravel\Sanctum\PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();

        if ($token !== null) {
            $token->delete();
        }
      }

    return $this->apiResponse(
        'success',
        'Successfully logged out'
      );
    }

    /**
     * Authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return $this->apiResponse(
            'success',
            'User profile fetched successfully',
            new UserAuthResource($user)
        );
    }

    /**
     * Update password.
     */
    public function updatePassword(
        ChangePasswordDTO $dto,
        Request $request
    ): JsonResponse {

        /** @var User $user */
        $user = $request->user();

        if (! Hash::check($dto->currentPassword, $user->password)) {
            return $this->apiResponse(
                'error',
                'Current password does not match.',
                null,
                [
                    'current_password' => [
                        'The provided current password is incorrect.',
                    ],
                ],
                422
            );
        }

        $user->update([
            'password' => Hash::make($dto->password),
        ]);

        return $this->apiResponse(
            'success',
            'Password updated successfully'
        );
    }

    /**
     * Forgot password.
     */
    public function forgotPassword(string $email): JsonResponse
    {
        $token = Str::random(60);

        DB::table('password_reset_tokens')
            ->updateOrInsert(
                [
                    'email' => $email,
                ],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

        return $this->apiResponse(
            'success',
            'Password reset token generated successfully.',
            [
                'reset_token' => $token,
            ]
        );
    }

    /**
    * Reset password.
    */
   public function resetPassword(
    ResetPasswordDTO $dto
     ): JsonResponse {

    $record = DB::table('password_reset_tokens')
        ->where('email', '=', $dto->email)
        ->first();

    if (! $record || ! Hash::check($dto->token, $record->token)) {
        return $this->apiResponse(
            'error',
            'Invalid or expired reset token.',
            null,
            null,
            400
        );
    }

    /** @var User|null $user */
    $user = User::query()
        ->where('email', $dto->email)
        ->first();

    if (! $user) {
        return $this->apiResponse(
            'error',
            'User not found.',
            null,
            null,
            404
        );
    }

    $user->update([
        'password' => Hash::make($dto->password),
    ]);

    $user->tokens()->delete();

    DB::table('password_reset_tokens')
        ->where('email', '=', $dto->email)
        ->delete();

    return $this->apiResponse(
        'success',
        'Password reset successfully'
    );
  }
}