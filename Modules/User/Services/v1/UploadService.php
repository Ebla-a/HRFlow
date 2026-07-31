<?php
namespace Modules\User\Services\v1;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\User\App\DTOs\UpdateAvatarData;
use Modules\User\Entities\User;

class UploadService
{
    public function updateProfileImage(User $user, UpdateAvatarData $dto): User
    {
        $file = $dto->avatar;

        DB::transaction(function () use ($user, $file) {
           collect([$user->avatar_url])
    ->when(
        fn ($old) => $old->first() && Storage::disk('public')->exists($old->first()),
        fn ($old) => Storage::disk('public')->delete($old->first())
    );


            $path = $file->store('profiles', 'public');

            $user->update([
                'avatar_url' => $path,
            ]);
        });

        return $user->refresh();
    }
}