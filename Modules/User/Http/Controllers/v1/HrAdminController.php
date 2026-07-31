<?php
namespace Modules\User\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\User\App\DTOs\AssignPermissionData;
use Modules\User\App\Http\Requests\AssignPermissionRequest;
use Modules\User\App\Http\Requests\AssignRoleRequest;
use Modules\User\App\Http\Requests\CreatePermissionRequest;
use Modules\User\App\Http\Requests\CreateRoleRequest;
use Modules\User\Entities\User;
use Modules\User\Services\v1\HrService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HrAdminController extends Controller
{
    public function __construct(
        protected HrService $hrService
    ) {}

    /**
     * Create a new security role.
     */
    public function createRole(CreateRoleRequest $request): JsonResponse
    {
         $this->authorize('manageRoles', User::class);
        $role = $this->hrService->createRole($request->validated()['name']);

        return $this->success($role, 'Role created successfully.', 201);
    }

    /**
     * Delete an existing role by Model Binding.
     */
    public function deleteRole(Role $role): JsonResponse
    {
        $this->authorize('manageRoles', User::class);
        $this->authorize('manageRoles', User::class);
        $this->hrService->deleteRole($role);

        return $this->success(null, 'Role deleted successfully.');
    }

    /**
     * Create a new permission.
     */
    public function createPermission(CreatePermissionRequest $request): JsonResponse
    {
          $this->authorize('managePermissions', User::class);
        $permission = $this->hrService->createPermission($request->validated()['name']);

        return $this->success($permission, 'Permission created successfully.', 201);
    }

    /**
     * Delete an existing permission by Model Binding.
     */
    public function deletePermission(Permission $permission): JsonResponse
    {
         $this->authorize('managePermissions', User::class);
        $this->hrService->deletePermission($permission);

        return $this->success(null, 'Permission deleted successfully.');
    }

    /**
     * Assign/grant a role to a user.
     */
    public function grantRole(AssignRoleRequest $request, User $user): JsonResponse
    {
        $this->authorize('manageRoles', User::class);
        $updatedUser = $this->hrService->grantRole($user, $request->validated()['role']);

        return $this->success(null, 'Role granted successfully.');

    }

    /**
     * Revoke a role from a user.
     */
    public function revokeRole(AssignRoleRequest $request, User $user): JsonResponse
    {
         $this->authorize('managePermissions', User::class);
        $updatedUser = $this->hrService->revokeRole($user, $request->validated()['role']);

        return $this->success(null, 'Role revoked successfully.');
    }

    /**
     * Grant a permission to a user using DTO.
     */
    public function grantPermission(AssignPermissionRequest $request, User $user): JsonResponse
    {

        $dto = AssignPermissionData::fromArray($request->validated());
        $this->hrService->grantPermission($user, $dto);

        return $this->success(null, 'Permission granted successfully.');
    }

    /**
     * Revoke a permission from a user using DTO.
     */
    public function revokePermission(AssignPermissionRequest $request, User $user): JsonResponse
    {
         $this->authorize('managePermissions', User::class);
        $dto = AssignPermissionData::fromArray($request->validated());
        $this->hrService->revokePermission($user, $dto);

        return $this->success(null, 'Permission revoked successfully.');
    }
}