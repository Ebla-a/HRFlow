<?php
namespace Modules\Organization\Repository\Implementation;

use Illuminate\Support\Facades\DB;
use Modules\Organization\Entities\Department;
use Modules\Organization\Repository\Contracts\DepartmentRepositoryInterface;

class DepartmentRepository implements DepartmentRepositoryInterface
{
/**
 * Summary of getTree
 */
public function getTree()
{
    $perPage = request('per_page', 12);
    return Department::active()->with(['childrenRecursive', 'jobTitles','employees:id,first_name,last_name,user_id,department_id'])
        ->whereNull('parent_id')
        ->latest()
        ->paginate($perPage);
}

    /**
     * Summary of findById
     * @param int $id
     * @return Department|\stdClass|null
     */
    public function findById(int $id): ?Department
    {
        return Department::with([ 'childrenRecursive', 'jobTitles', 'employees'])->find($id);
    }
    /**
     * Summary of create
     * @param array $data
     * @return TModel
     */
    public function create(array $data): Department
    {
        return Department::create($data);
    }
    /**
     * Summary of update
     * @param Department $department
     * @param array $data
     * @return Department|null
     */
    public function update(Department $department, array $data): Department
    {
        $department->update($data);
        return $department->fresh();
    }
    /**
     * Summary of delete
     * @param Department $department
     * @return bool
     */
    public function delete(Department $department): bool
    {
        return (bool) $department->delete();
    }
    /**
     * Summary of hasEmployees
     * @param Department $department
     * @return bool
     */
    public function hasEmployees(Department $department): bool
    {
        return $department->employees()->exists();
    }



    /**
     * Summary of restore
     * @param int $id
     * @return Department|null
     */
    public function restore(int $id): Department
    {
        //get the department including soft deleted ones
        $department = Department::onlyTrashed()->findOrFail($id);
        $department->restore();

        return $department->fresh();
    }
    /**
     * Summary of forceDelete
     * @param int $id
     * @return bool
     */
    public function forceDelete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $department = Department::withTrashed()->findOrFail($id);
//zero out the department_id for all employees in this department
            $department->employees()->update(['department_id' => null]);
//zero out the parent_id for all child departments of this department
            Department::where('parent_id', $department->id)
                ->update(['parent_id' => null]);

            return (bool) $department->forceDelete();
        });
    }

    /**
     * Summary of updateManager
     * @param int $departmentId
     * @param int $managerId
     * @return Department
     */
    public function updateManager(int $departmentId, int $managerId): Department
    {
        $department = Department::findOrFail($departmentId);

        $department->update([
            'manager_id' => $managerId,
        ]);

        return $department->fresh(['manager']);
    }

}
