<?php

declare(strict_types=1);

namespace Modules\User\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\User\App\DTOs\AssignPermissionData;
use Modules\User\Entities\User;
use Modules\User\Http\Requests\AssignPermissionRequest;
use Modules\User\Http\Requests\AssignRoleRequest;
use Modules\User\Http\Requests\CreatePermissionRequest;
use Modules\User\Http\Requests\CreateRoleRequest;
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

        $role = $this->hrService->createRole(
            $request->validated()['name']
        );

        return $this->success(
            [],
            'Role created successfully'
        );
    }

    /**
     * Delete an existing role.
     */
    public function deleteRole(Role $role): JsonResponse
    {
        $this->authorize('manageRoles', User::class);

        $this->hrService->deleteRole($role);

        return $this->success(
            [],
            'Role deleted successfully'
        );
    }

    /**
     * Create a new permission.
     */
    public function createPermission(
        CreatePermissionRequest $request
    ): JsonResponse {
        $this->authorize('managePermissions', User::class);

        $permission = $this->hrService->createPermission(
            $request->validated()['name']
        );

        return $this->success(
            [],
            'Permission created successfully'
        );
    }

    /**
     * Delete an existing permission.
     */
    public function deletePermission(
        Permission $permission
    ): JsonResponse {
        $this->authorize('managePermissions', User::class);

        $this->hrService->deletePermission($permission);

        return $this->success(
            [],
            'Permission deleted successfully'
        );
    }

    /**
     * Grant a role to a user.
     */
    public function grantRole(
        AssignRoleRequest $request,
        User $user
    ): JsonResponse {
        $this->authorize('manageRoles', User::class);

        $this->hrService->grantRole(
            $user,
            $request->validated()['role']
        );

        return $this->success(
            [],
            'Role granted successfully'
        );
    }

    /**
     * Revoke a role from a user.
     */
    public function revokeRole(
        AssignRoleRequest $request,
        User $user
    ): JsonResponse {
        $this->authorize('manageRoles', User::class);

        $this->hrService->revokeRole(
            $user,
            $request->validated()['role']
        );

        return $this->success(
            [],
            'Role revoked successfully'
        );
    }

    /**
     * Grant a permission to a user.
     */
    public function grantPermission(
        AssignPermissionRequest $request,
        User $user
    ): JsonResponse {
        $this->authorize('managePermissions', User::class);

        $dto = AssignPermissionData::fromArray(
            $request->validated()
        );

        $this->hrService->grantPermission(
            $user,
            $dto
        );

        return $this->success(
            [],
            'Permission granted successfully'
        );
    }

    /**
     * Revoke a permission from a user.
     */
    public function revokePermission(
        AssignPermissionRequest $request,
        User $user
    ): JsonResponse {
        $this->authorize('managePermissions', User::class);

        $dto = AssignPermissionData::fromArray(
            $request->validated()
        );

        $this->hrService->revokePermission(
            $user,
            $dto
        );

        return $this->success(
            [],
            'Permission revoked successfully'
        );
    }
}