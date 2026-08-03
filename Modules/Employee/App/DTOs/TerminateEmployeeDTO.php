<?php

namespace Modules\Employee\App\DTOs;

use Modules\Employee\App\Http\Requests\V1\TerminateEmployeeRequest;

class TerminateEmployeeDTO
{
    public function __construct(
        public readonly string $reason
    ) {}

    public static function fromRequest(TerminateEmployeeRequest $request): self
    {
        return new self($request->validated()['termination_reason']);
    }
}