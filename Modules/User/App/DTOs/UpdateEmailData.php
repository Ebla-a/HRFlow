<?php

namespace Modules\User\App\DTOs;

readonly class UpdateEmailData
{
    public function __construct(
        public int $userId,
        public string $email,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            userId:$data['userId'],
            email: $data['email'],
        );
    }
}