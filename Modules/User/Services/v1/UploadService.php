<?php

declare(strict_types=1);

namespace Modules\User\Services\v1;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\User\App\DTOs\UpdateAvatarData;
use Modules\User\Entities\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UploadService
{
    private const DISK = 'local';

    private const PROFILE_DIRECTORY = 'profiles';

    /**
     * Upload or replace user's profile image.
     */
    public function updateProfileImage(
        User $user,
        UpdateAvatarData $dto
    ): User {
        $file = $dto->avatar;

        DB::transaction(function () use ($user, $file): void {
            // Delete old avatar if it exists.
            if (
                $user->avatar_url !== null
                && Storage::disk(self::DISK)->exists($user->avatar_url)
            ) {
                Storage::disk(self::DISK)->delete($user->avatar_url);
            }

            // Store the new avatar on the private local disk.
            $path = $file->store(
                self::PROFILE_DIRECTORY,
                self::DISK
            );

            $user->update([
                'avatar_url' => $path,
            ]);
        });

        return $user->refresh();
    }

    /**
     * Download user's profile image.
     */
    public function downloadProfileImage(
        User $user
    ): StreamedResponse {
        if (
            $user->avatar_url === null
            || ! Storage::disk(self::DISK)->exists($user->avatar_url)
        ) {
            abort(Response::HTTP_NOT_FOUND, 'Profile image not found.');
        }

        return Storage::disk(self::DISK)->download(
            $user->avatar_url,
            'profile-image.' . pathinfo(
                $user->avatar_url,
                PATHINFO_EXTENSION
            )
        );
    }
}