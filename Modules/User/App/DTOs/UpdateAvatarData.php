<?php

namespace Modules\User\App\DTOs;

use Illuminate\Http\UploadedFile;

readonly class UpdateAvatarData
{
    public function __construct(
        public UploadedFile $avatar,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            avatar: $data['avatar'],
        );
    }
}