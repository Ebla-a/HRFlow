<?php

namespace Modules\Employee\App\DTOs;

use Modules\Employee\Http\Requests\UpdateEmployeeRequest;

class UpdateEmployeeDTO
{
    public function __construct(
        public readonly array $data
    ) {}

    public static function fromRequest(UpdateEmployeeRequest $request): self
    {
        return new self($request->validated());
    }
}