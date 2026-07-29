<?php

namespace Modules\User\Http\Controllers\v1;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Services\v1\HrService;
use Modules\Core\App\Traits\ApiResponseTrait;
use Modules\User\Http\Requests\CreatePermissionRequest;
use Modules\User\Http\Requests\CreateRoleRequest;
use Modules\User\Http\Requests\GranteRevokepermissionRequest;
use Modules\User\Http\Requests\GrantRevokeeRoleRequest;

class HrAdminController extends Controller
{
    use ApiResponseTrait;

    /**
     * Create a new security role.
     *
     * @param CreateRoleRequest $request
     * @param HrService $service
     * @return JsonResponse
     */
    public function createRole(CreateRoleRequest $request,HrService $service)
    {
        $service->createRole($request->validated());
        return $this->success(null, "Role created successfully", 200);
    }


    /**
     * Delete an existing role by ID.
     *
     * @param int|string $id
     * @param HrService $service
     * @return JsonResponse
     */

    public function deleteRole($id,HrService $service)
    {
        $service->deleteRole(['id' => $id]);
        return $this->success(null, "Role deleted successfully", 200);
    }



    /**
     * Create a new permission.
     *
     * @param CreatePermissionRequest $request
     * @param HrService $service
     * @return JsonResponse
     */
    public function createPermission(CreatePermissionRequest $request,HrService $service)
    {
        $service->createPermission($request->validated());
        return $this->success(null, "Permission created successfully", 200);
    }


    /**
     * Delete an existing permission by ID.
     *
     * @param int|string $id
     * @param HrService $service
     * @return JsonResponse
     */
    public function deletePermission($id,HrService $service)
    {
        $service->deletePermission(['id' => $id]);
        return $this->success(null, "Permission deleted successfully", 200);
    }


    /**
     * Assign/grant a role to a user.
     *
     * @param GrantRevokeeRoleRequest $request
     * @param HrService $service
     * @return JsonResponse
     */
    public function GrantRole(GrantRevokeeRoleRequest $request,HrService $service)
    {
        $service->GrantRole($request->validated());
        return $this->success(null, "Role granted successfully", 200);
    }

    /**
     * Revoke  role from a user.
     *
     * @param GrantRevokeeRoleRequest $request
     * @param HrService $service
     * @return JsonResponse
     */
    public function revokeRole(GrantRevokeeRoleRequest $request,HrService $service)
    {
        $service->revokeRole($request->validated());
        return $this->success(null, "Role revoked successfully", 200);
    }

    /**
     * Grant  permission to a user.
     *
     * @param GranteRevokepermissionRequest $request
     * @param HrService $service
     * @return JsonResponse
     */
    public function GrantPermission(GranteRevokepermissionRequest $request,HrService $service)
    {
        $service->GrantPermission($request->validated());
        return $this->success(null, "Permission granted successfully", 200);
    }

    /**
     * Revoke permission from a user.
     *
     * @param GranteRevokepermissionRequest $request
     * @param HrService $service
     * @return JsonResponse
     */
    public function revokePermission(GranteRevokepermissionRequest $request,HrService $service)
    {
        $service->revokePermission($request->validated());
        return $this->success(null, "Permission revoked successfully", 200);
    }


}
