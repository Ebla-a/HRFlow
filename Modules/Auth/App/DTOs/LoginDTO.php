<?php

namespace Modules\Auth\App\DTOs;

class LoginDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {
    }
}
 