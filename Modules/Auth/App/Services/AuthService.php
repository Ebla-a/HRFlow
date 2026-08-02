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
use Modules\Core\App\Traits\ApiResponseTrait;
use Modules\Auth\App\Events\PasswordChanged;
use Modules\Auth\Http\Resources\UserAuthResource;

class AuthService
{
        use ApiResponseTrait;



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
           return $this->error(
            'Invalid credentials provided.',
               401,
            [
                  'email' => [
                     'Invalid email or password.',
                ],
            ]
          );
        }

        if (! $user->is_active) {
           return $this->error(
            'User account is inactive.',
              403
           );
        }

        $token = $user->createToken('auth_token')->plainTextToken;

         return $this->success(
           [
             'access_token' => $token,
             'token_type' => 'Bearer',
             'user' => new UserAuthResource($user),
           ],
          'Login successful'
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

      return $this->success(
        null,
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

         return $this->success(
           new UserAuthResource($user),
             'User profile fetched successfully'
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
           return $this->error(
             'Current password does not match.',
               422,
            [
                'current_password' => [
                  'The provided current password is incorrect.',
                ],
            ]
          );
        }

        $user->update([
            'password' => Hash::make($dto->password),
        ]);

        event(new PasswordChanged(
          $user,
          request()->ip(),
          request()->userAgent()
       ));

        return $this->success(
          null,
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

       return $this->success(
        [
           'reset_token' => $token,
        ],
          'Password reset token generated successfully.'
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
        return $this->error('Invalid or expired reset token.', 400);
    }

    /** @var User|null $user */
    $user = User::query()
        ->where('email', $dto->email)
        ->first();

    if (! $user) {
        return $this->error('User not found.', 404);
    }

    $user->update([
        'password' => Hash::make($dto->password),
    ]);

     event(new PasswordChanged(
        $user,
        request()->ip(),
        request()->userAgent()
      ));

    DB::table('password_reset_tokens')
        ->where('email', '=', $dto->email)
        ->delete();

    return $this->success(
       null,
         'Password reset successfully'
     );
  }
}
