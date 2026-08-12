<?php

namespace Modules\Organization\DTO\V1;

class StoreDepartmentDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $code,
        public readonly ?int $parentId = null,
        public readonly ?int $managerId = null,
        public readonly bool $isActive = true,
    ) {}

    /**
     * Summary of fromRequest
     * @param array $data
     * @return StoreDepartmentDto
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            code: $data['code'],
            parentId: isset($data['parent_id']) ? (int) $data['parent_id'] : null,
            managerId: isset($data['manager_id']) ? (int) $data['manager_id'] : null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : true,
        );
    }
    /**
     * Summary of toArray
     * @return array{code: string, is_active: bool, manager_id: int|null, name: string, parent_id: int|null}
     */
    public function toArray(): array
    {
        return array_filter([
            'name'       => $this->name,
            'code'       => $this->code,
            'parent_id'  => $this->parentId,
            'manager_id' => $this->managerId,
            'is_active'  => $this->isActive,
        ], fn ($value) => $value !== null);
    }
}
