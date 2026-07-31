<?php

namespace Modules\User\App\DTOs;

readonly class CreateUserData
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $avatarUrl,
        public bool $isActive = true,
   
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            avatarUrl:$data['avatarUrl'],
            isActive: $data['is_active'] ?? true,
        );
    }
}