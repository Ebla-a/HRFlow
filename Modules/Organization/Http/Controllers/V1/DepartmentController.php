<?php

namespace Modules\Organization\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Organization\DTO\V1\AssignManagerDTO;
use Modules\Organization\DTO\V1\DepartmentDTO;
use Modules\Organization\Entities\Department;
use Modules\Organization\Http\Requests\V1\Department\AssignManagerRequest;
use Modules\Organization\Http\Requests\V1\Department\StoreDepartmentRequest;
use Modules\Organization\Http\Requests\V1\Department\UpdateDepartmentRequest;
use Modules\Organization\Services\V1\DepartmentService;
use Modules\Organization\Transformers\V1\DepartmentResource;

class DepartmentController extends Controller
{


public function __construct(
        protected DepartmentService $departmentService
    ) {}


    /**
     * Summary of index
     * @return JsonResponse
     */
    public function index():JsonResponse
    {

         $result = $this->departmentService->getHierarchicalTree();

         return $this->success($data = $result['data'],
            $message = 'Departments retrieved successfully',
            $status = 200,
            $meta = $result['meta']
        );

        }


   /**
    * Summary of store
    * @param StoreDepartmentRequest $request
    * @return JsonResponse
    */
   public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $this->authorize('create', Department::class);

        $dto = DepartmentDTO::fromRequest($request->validated());
        $department = $this->departmentService->createDepartment($dto);

        return $this->success([
            'status' => true,
            'message' => 'Department created successfully.',
            'data' => new DepartmentResource($department),
        ], 201);
    }

    /**
     * Summary of show
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
            $department = $this->departmentService->getDepartmentDetails($id);
//for enssure that the manager og this department can view the department details and also the hr_admin can view the department details

            $this->authorize('view', $department);

            return $this->success([
                'status' => true,
                'data' => new DepartmentResource($department),
            ]);

    }
    /**
     * Summary of update
     * @param UpdateDepartmentRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateDepartmentRequest $request, int $id): JsonResponse
    {

            $dto = DepartmentDTO::fromRequest($request->validated());
            $department = $this->departmentService->updateDepartment($id, $dto);

            return $this->success([
                'status' => true,
                'message' => 'Department updated successfully.',
                'data' => new DepartmentResource($department),
            ]);

    }
    /**
     * Summary of destroy
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {

            $this->departmentService->deleteDepartment($id);

            return $this->success([
                'status' => true,
                'message' => 'Department deleted successfully.',
            ]);

    }



    /**
     * Summary of restore
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $department = $this->departmentService->restoreDepartment($id);

        return $this->success(new DepartmentResource($department),
'successfully restore the department'        );
    }
    /**
     * Summary of forceDelete
     * @param int $id
     * @return JsonResponse
     */
    public function forceDelete(int $id): JsonResponse
    {
        $this->departmentService->forceDeleteDepartment($id);

        return $this->success(null,
'successfully deleted the department permanently'        );
    }
    /**
     * Summary of assignManager
     * @param AssignManagerRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function assignManager(AssignManagerRequest $request, int $id): JsonResponse
    {
        $dto = AssignManagerDTO::fromRequest($request->validated());

        $department = $this->departmentService->assignManager($id, $dto);

        return $this->success(new DepartmentResource($department),
'manager assigned to the department successfully');
    }



}
