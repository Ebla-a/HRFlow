<?php

namespace Modules\Leave\DTO;

class LeaveTypeDTO
{
    public function __construct(
        public readonly string $name,
        public readonly int $annual_days,
        public readonly bool $is_paid,
        public readonly bool $requires_attachment,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'annual_days' => $this->annual_days,
            'is_paid' => $this->is_paid,
            'requires_attachment' => $this->requires_attachment,
        ];
    }
}
 