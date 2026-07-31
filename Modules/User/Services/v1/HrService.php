<?php
namespace Modules\User\Services\v1;

use Modules\User\App\DTOs\AssignPermissionData;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HrService
{
    public function createRole(string $name): Role
    {
        return Role::create(['name' => $name]);
    }

    public function deleteRole(Role $role): void
    {
        $role->delete();
    }

    public function createPermission(string $name): Permission
    {
        return Permission::create(['name' => $name]);
    }

    public function deletePermission(Permission $permission): void
    {
        $permission->delete();
    }

    public function grantRole(User $user, string $roleName): User
    {
        $user->assignRole($roleName);

        return $user;
    }

    public function revokeRole(User $user, string $roleName): User
    {
        $user->removeRole($roleName);

        return $user;
    }

    public function grantPermission(User $user, AssignPermissionData $dto): User
    {
        $user->givePermissionTo($dto->permission);

        return $user;
    }

    public function revokePermission(User $user, AssignPermissionData $dto): User
    {
        $user->revokePermissionTo($dto->permission);

        return $user;
    }
}