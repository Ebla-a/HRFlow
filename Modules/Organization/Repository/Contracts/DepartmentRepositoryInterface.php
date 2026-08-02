<?php
namespace Modules\Organization\Repository\Contracts;

use Modules\Organization\Entities\Department;

interface DepartmentRepositoryInterface
{
    public function getTree();
    public function findById(int $id): ?Department;
    public function create(array $data): Department;
    public function update(Department $department, array $data): Department;
    public function delete(Department $department): bool;
    public function hasEmployees(Department $department): bool;

    public function restore(int $id): Department;
    public function forceDelete(int $id): bool;
    public function updateManager(int $departmentId, int $managerId): Department;

}
