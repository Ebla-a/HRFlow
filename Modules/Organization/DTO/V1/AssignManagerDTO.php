<?php

namespace Modules\Organization\DTO\V1;

class AssignManagerDTO
{
    public function __construct(
        public readonly int $manager_id
    ) {}
    /**
     * Summary of fromRequest
     * @param array $data
     * @return AssignManagerDTO
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            manager_id: (int) $data['manager_id']
        );
    }
}
