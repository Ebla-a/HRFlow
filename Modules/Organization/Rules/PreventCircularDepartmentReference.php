<?php

namespace Modules\Organization\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Organization\Entities\Department;

class PreventCircularDepartmentReference implements ValidationRule
{
    /**
     * @param int|string|null $departmentId
     */
    public function __construct(private readonly int|string|null $departmentId)
    {
    }

    /**
     * Summary of validate
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // If there is no parent value or no current department ID , skip validation.
        if (!$value || !$this->departmentId) {
            return;
        }
        // 1. Prevent the department from setting itself as its own parent.
        if ((string) $value === (string) $this->departmentId) {
            $fail('The department cannot be its own parent.');
            return;
        }
// 2. Fetch the parent candidate to check for circular reference.

        $parentCandidate = Department::find($value);
        if ($parentCandidate && $this->isDescendant($parentCandidate, $this->departmentId)) {
            $fail('The selected parent department is a descendant of the current department, which would create a circular reference.');
        }
    }

    /**
     * Summary of isDescendant
     * @param mixed $department
     * @param int|string $currentDepartmentId
     * @return bool
     */
// Recursively traverse up the parent tree to ensure no circular dependency exists.
    private function isDescendant(?Department $department, int|string $currentDepartmentId): bool
    {
        while ($department) {
            if ($department->id == $currentDepartmentId) {
                return true;
            }
            $department = $department->parent;
        }

        return false;
    }
}
