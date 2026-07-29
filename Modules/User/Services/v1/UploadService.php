<?php
namespace Modules\User\Services\v1;

use Illuminate\Support\Facades\Storage;
use Modules\User\Entities\User;
use Modules\User\Exceptions\UserNotFoundException;

class UploadService
{

    public function updateProfileImage(array $data)
    {
        $id = $data['id'];
        $user = User::find($id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        if (isset($data['image'])) {
            $file = $data['image'];
            
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }


            $path = $file->store('profiles', 'public');

            // 4. Update Database
            $user->profile_image = $path;
            $user->save();
        }

        return $user;
    }
}

