<?php

namespace Modules\Auth\Services;

use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Auth\App\DTOs\ChangePasswordDTO;
use Modules\Auth\App\DTOs\LoginDTO;
use Modules\Auth\App\DTOs\ResetPasswordDTO;
use Modules\Auth\App\Exceptions\ExpiredResetToken;
use Modules\Auth\App\Exceptions\InactiveUserException;
use Modules\Auth\App\Exceptions\InvalidCredentialsException;
use Modules\Auth\Events\PasswordChanged;
use Modules\User\Entities\User;

class AuthService
{
    /**
     * Login user.
     *
     * @return array{access_token: string, token_type: string, user: User}
     */
    public function login(LoginDTO $dto): array
    {
        $user = User::query()
            ->where('email', $dto->email)
            ->first();

        if (! $user) {
            $this->recordLoginAttempt($dto->email, 'email_not_found');

            throw new \Exception('Email not found.', 404);
        }

        if (! Hash::check($dto->password, $user->password)) {
            $this->recordLoginAttempt($dto->email, 'invalid_password');

              throw new InvalidCredentialsException();
        }

        if (! $user->is_active) {
            $this->recordLoginAttempt($dto->email, 'inactive_account');

           throw new InactiveUserException();
        }
        $token = $user
            ->createToken('auth_token')
            ->plainTextToken;

        $this->recordLoginAttempt($dto->email, 'success');

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
    public function updatePassword(ChangePasswordDTO $dto, Request $request): void
    {
        $user = $request->user();

        if (! Hash::check($dto->currentPassword, $user->password)) {
            throw ValidationException::withMessages([
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
    public function forgotPassword(string $email): string
    {
        $user = User::query()
            ->where('email', $email)
            ->first();

        if (! $user) {
            throw new \Exception('Email not found.', 404);
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
    public function resetPassword(ResetPasswordDTO $dto): void
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $dto->email)
            ->first();

        if (! $record || ! Hash::check($dto->token, $record->token)) {
             throw new ExpiredResetToken();
        }

        $user = User::query()
            ->where('email', $dto->email)
            ->first();

        if (! $user) {
            throw new \Exception('User not found.', 404);
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
            ->where('email', $dto->email)
            ->delete();
    }

    /**
     * Record a login attempt.
     *
     * @param string $email
     * @param string $status
     * @return void
     */
    private function recordLoginAttempt(string $email, string $status): void
    {
        LoginAttempt::create([
            'email' => $email,
            'status' => $status,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
