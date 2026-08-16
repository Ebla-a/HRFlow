<?php

namespace Modules\Organization\DTO\V1;

use Modules\Organization\Enums\JobTitleGrade;

class UpdateJobTitleDto
{
    public function __construct(
      public readonly ?string $title = null,
     public readonly ?JobTitleGrade $grade = null,
        public readonly ?int $departmentId = null,
        public readonly ?string $description = null,
        public readonly ?bool $isActive = null,
    ) {}
    /**
     * Summary of fromRequest
     * @param array $data
     * @return UpdateJobTitleDto
     */
    public static function fromRequest(array $data): self
    {
        $gradeValue = isset($data['grade'])
            ? ($data['grade'] instanceof JobTitleGrade ? $data['grade'] : JobTitleGrade::tryFrom($data['grade']))
            : null;
       return new self(
            title: $data['title'] ?? null,
            grade: $gradeValue ,
            departmentId: isset($data['department_id']) ? (int) $data['department_id'] : null,
            description: $data['description'] ?? null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : null,
        );
    }
    /**
     * Summary of toArray
     * @return array{department_id: int, description: string|null, grade: string, is_active: bool|null, title: string}
     */
    public function toArray(): array
    {
       return array_filter([
            'title'         => $this->title,
            'grade'         => $this->grade instanceof JobTitleGrade ? $this->grade->value : $this->grade,
            'department_id' => $this->departmentId,
            'description'   => $this->description,
            'is_active'     => $this->isActive,
        ], fn ($value) => $value !== null);
    }
}
