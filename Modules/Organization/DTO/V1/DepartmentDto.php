<?php
namespace Modules\Organization\DTO\V1;

class DepartmentDTO
{
    public function __construct(
    public readonly ?string $name = null,
        public readonly ?string $code = null,
        public readonly ?int $parentId = null,
        public readonly ?int $managerId = null,
        public readonly ?bool $isActive = null,
    ) {}
    /**
     * Summary of fromRequest
     * @param array $data
     * @return DepartmentDTO
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            code: $data['code']?? null,
            parentId: isset($data['parent_id']) ? (int) $data['parent_id'] : null,
            managerId: isset($data['manager_id']) ? (int) $data['manager_id'] : null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : true,
        );
    }
    /**
     * Summary of toArray
     * @return array{code: string|null, is_active: bool|null, manager_id: int|null, name: string|null, parent_id: int|null}
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'code' => $this->code,
            'parent_id' => $this->parentId,
            'manager_id' => $this->managerId,
            'is_active' => $this->isActive,
        ], fn ($value) => $value !== null);
    }
}

