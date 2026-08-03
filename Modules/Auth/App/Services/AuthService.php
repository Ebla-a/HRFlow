<?php

namespace Modules\Auth\App\Services;

use Modules\User\Entities\User;
use App\Models\LoginAttempt;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Auth\App\DTOs\ChangePasswordDTO;
use Modules\Auth\App\DTOs\LoginDTO;
use Modules\Auth\App\DTOs\ResetPasswordDTO;
use Modules\Auth\App\Events\PasswordChanged; 

class AuthService
{ 
    /**
     * Login user.
     */
    public function login(LoginDTO $dto): array
    { 
        $user = User::query()
            ->where('email', $dto->email)
            ->first();

        if (! $user) {
          LoginAttempt::create([
            'email' => $dto->email,
            'status' => 'email_not_found',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

            throw new \Exception(
              'Email not found.', 
              404);
        }

        if (! Hash::check(
          $dto->password,
          $user->password
          )) {
            LoginAttempt::create([
                'email' => $dto->email,
                'status' => 'invalid_password',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            throw new \Exception(
              'Invalid password.',
               401
            );
        }

        if (! $user->is_active) {
          LoginAttempt::create([
            'email' => $dto->email,
            'status' => 'inactive_account',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

            throw new \Exception(
              'User account is inactive.',
               403
            );
        }

        $token = $user
            ->createToken('auth_token')
            ->plainTextToken;

        LoginAttempt::create([
            'email' => $dto->email,
            'status' => 'success',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ];
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): void
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        } 
    }

    /**
     * Authenticated user.
     */
    public function me(Request $request): User
    {
        return $request->user();
    }

    /**
     * Update password.
     */
   public function updatePassword(
    ChangePasswordDTO $dto,
    Request $request
): void {
    $user = $request->user();

    if (! Hash::check($dto->currentPassword, $user->password)) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'current_password' => ['Current password is incorrect.'],
        ]);
    }

    $user->password = Hash::make($dto->password);
    $user->save();

    event(new PasswordChanged(
        $user,
        request()->ip(),
        request()->userAgent()
    ));
}
    /**
     * Forgot password.
     */
    public function forgotPassword(
        string $email
    ): string {

        $user = User::where(
            'email',
            $email
        )->first();

        if (! $user) {
            throw new \Exception(
                'Email not found.',
                404
            );
        }

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

        return $token;
    }

    /**
     * Reset password.
     */
    public function resetPassword(
        ResetPasswordDTO $dto
    ): void {

        $record = DB::table('password_reset_tokens')
            ->where('email', $dto->email)
            ->first();

        if (
            ! $record ||
            ! Hash::check(
                $dto->token,
                $record->token
            )
        ) {
            throw new \Exception(
                'Invalid or expired reset token.',
                400
            );
        }

   

        $user = User::where(
            'email',
            $dto->email
        )->first();

             $user->password = Hash::make($dto->password);
             $user->save();

        if (! $user) {
            throw new \Exception(
                'User not found.',
                404
            );
        }

        $user->update([
            'password' => Hash::make(
                $dto->password
            ),
        ]);

        event(new PasswordChanged(
            $user,
            request()->ip(),
            request()->userAgent()
        ));
        

        DB::table('password_reset_tokens')
            ->where('email', $dto->email)
            ->delete();
    }
}