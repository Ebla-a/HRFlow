<?php

namespace Modules\User\App\DTOs;

readonly class AssignPermissionData
{
    public function __construct(
        public string $permission,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            permission: $data['permission'],
        );
    }
}