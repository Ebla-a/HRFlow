<?php

namespace Modules\User\Services\v1;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\App\DTOs\CreateUserData;
use Modules\User\App\DTOs\UpdateEmailData;
use Modules\User\Entities\User;
use Modules\User\Events\UserCreated;
use Modules\User\Exceptions\UserNotFoundException;

class UserService
{
    /**
     * Create a new user and dispatch creation event.
     */
    public function createUser(CreateUserData $dto): User
    {
        $user = User::create([
            'email' => $dto->email,
            'password' => bcrypt($dto->password),
            'avatar_url' => $dto->avatarUrl,
            'is_active' => $dto->isActive,
        ]);

        UserCreated::dispatch($user);

        return $user;
    }

    /**
     * Get paginated list of users with optional filtering.
     */
    public function allUsers(?bool $isActive = null, int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->when(! is_null($isActive), fn ($query) => $query->where('is_active', $isActive))
            ->paginate($perPage);
    }

    /**
     * Return user instance by ID using custom exception.
     */
    public function userById(int $id): User
    {
        $user = User::find($id);

        if (! $user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * Update user email.
     */
    public function updateEmail(User $user, UpdateEmailData $dto): User
    {
        $user->when($user->email !== $dto->email, function (User $u) use ($dto) {
            $u->update([
                'email' => $dto->email,
                'email_verified_at' => null,
            ]);
        });

        return $user;
    }

    /**
     * Deactivate user account.
     */
    public function deactivateUserAccount(User $user): User
    {
        $user->when($user->is_active, fn (User $u) => $u->update(['is_active' => false]));

        return $user;
    }

    /**
     * Activate user account.
     */
    public function activateUserAccount(User $user): User
    {
        $user->when(! $user->is_active, fn (User $u) => $u->update(['is_active' => true]));

        return $user;
    }
}