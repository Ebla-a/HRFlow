<?php
namespace Modules\Organization\Services\V1;
use Exception;
use Illuminate\Support\Facades\Cache;
use Modules\Organization\DTO\V1\AssignManagerDTO;
use Modules\Organization\DTO\V1\DepartmentDTO;
use Modules\Organization\Entities\Department;
use Modules\Organization\Repository\Contracts\DepartmentRepositoryInterface;
use Modules\Organization\Transformers\V1\DepartmentResource;

class DepartmentService
{
    public function __construct(
        protected DepartmentRepositoryInterface $departmentRepository
    ) {}



   public function getHierarchicalTree()
{

      $page = request('page', 1);
    $perPage = request('per_page', 12);

    $cacheKey = "departments_tree_page_{$page}_per_{$perPage}";
    $tag = Cache::tags(['Organization']);

    return $tag->remember($cacheKey, now()->addHours(1), function () use ($tag, $cacheKey) {

        $lock = Cache::lock("lock:{$cacheKey}", 10);

        return $lock->block(5, function () use ($tag, $cacheKey) {

            $data = $tag->get($cacheKey);
            if ($data !== null) {
                return $data;
            }

            $paginator = $this->departmentRepository->getTree();

              $formattedData = json_decode(
                DepartmentResource::collection($paginator->items())->toJson(),
                true
            );

            return [
                'data' => $formattedData,
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                ],
            ];
        });
    });

}


    /**
     * Summary of getDepartmentDetails
     * @param int $id
     * @throws Exception
     * @return Department
     */
    public function getDepartmentDetails(int $id): Department
    {
        $department = $this->departmentRepository->findById($id);
        if(!$department || $department->deleted_at != null) {
            throw new Exception('Department not found.', 404);
        }


        return $department;
    }
   /**
    * Summary of createDepartment
    * @param DepartmentDTO $dto
    * @return Department
    */
   public function createDepartment(DepartmentDTO $dto): Department
    {
        $department = $this->departmentRepository->create($dto->toArray());
        //cache invalidation
        Cache::tags(['Organization'])->flush();
        return $department;
    }


    /**
     * Summary of updateDepartment
     * @param int $id
     * @param DepartmentDTO $dto
     * @return Department
     */
    public function updateDepartment(int $id, DepartmentDTO $dto): Department
    {
        $department = $this->getDepartmentDetails($id);
        $newDepartment = $this->departmentRepository->update($department, $dto->toArray());
        //cache invalidation
        Cache::tags(['Organization'])->flush();
        return $newDepartment;
    }

    /**
     * Summary of deleteDepartment
     * @param int $id
     * @return bool
     */
    public function deleteDepartment(int $id): bool
    {
        $department = $this->getDepartmentDetails($id);



        $deletedDep = $this->departmentRepository->delete($department);
        //cache invalidation
        Cache::tags(['Organization'])->flush();

        return $deletedDep;
    }



    /**
     * Summary of restoreDepartment
     * @param int $id
     * @return Department
     */
    public function restoreDepartment(int $id): Department
    {
        $department = $this->departmentRepository->restore($id);

        Cache::tags(['Organization'])->flush();

        return $department;
    }
    /**
     * Summary of forceDeleteDepartment
     * @param int $id
     * @return bool
     */
    public function forceDeleteDepartment(int $id): bool
    {
        $deleted = $this->departmentRepository->forceDelete($id);

        Cache::tags(['Organization'])->flush();

        return $deleted;
    }
     /**
      * Summary of assignManager
      * @param int $departmentId
      * @param AssignManagerDTO $dto
      * @return Department
      */
     public function assignManager(int $departmentId, AssignManagerDTO $dto): Department
    {
        $department = $this->departmentRepository->updateManager($departmentId, $dto->manager_id);

        Cache::tags(['Organization'])->flush();

        return $department;
    }
}
