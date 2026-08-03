<?php

namespace Modules\Auth\App\DTOs;

class ResetPasswordDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $token,
        public readonly string $password,
    ) {
    }
}
 