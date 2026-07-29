<?php

namespace Modules\User\Services\v1;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\User\Entities\User;
use Modules\User\Exceptions\NotFoundException;
use InvalidArgumentException;

class UploadService
{

    protected array $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    protected int $maxFileSize = 2048 * 1024; 

    public function updateProfileImage(array $data): User
    {
        $user = User::find($data['id']);

        if (!$user) {
            throw new NotFoundException();
        }

        if (isset($data['avatar_url']) ) {
        
            $file = $data['avatar_url'];
            
            if (!$file->isValid()) {
                throw new InvalidArgumentException('Invalid or corrupted image file uploaded.');
            }

            if (!in_array($file->getMimeType(), $this->allowedMimes, true)) {
                throw new InvalidArgumentException('Only JPG, PNG, and WEBP image formats are allowed.');
            }

            if ($file->getSize() > $this->maxFileSize) {
                throw new InvalidArgumentException('Image size exceeds the maximum limit of 2MB.');
            }

            DB::transaction(function () use ($user, $file) {
                if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                    Storage::disk('public')->delete($user->avatar_url);
                }
                $path = $file->store('profiles', 'public');

                $user->avatar_url = $path;
                $user->save();
            });
        }

        return $user;
    }



    
}