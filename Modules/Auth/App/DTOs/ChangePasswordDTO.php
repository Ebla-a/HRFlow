<?php

namespace Modules\Auth\App\DTOs;

class ChangePasswordDTO
{
    public function __construct(
        public readonly string $currentPassword,
        public readonly string $password,
    ) {
    }
}
 