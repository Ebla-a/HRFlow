<?php
namespace Modules\Organization\Repository\Implementation;

use Modules\Organization\Entities\JobTitle;
use Modules\Organization\Repository\Contracts\JobTitleRepositoryInterface;

class JobTitleRepository implements JobTitleRepositoryInterface


{
    /**
     * Summary of getAll
     */
    public function getAll()
    {
    $perPage = request('per_page', 12);

        return JobTitle::active()->with(['employees:id,first_name,last_name,user_id,job_title_id','department:id,name'])->latest()
        ->paginate($perPage);
    }
    
    /**
     * Summary of findById
     * @param int $id
     * @return JobTitle|\stdClass|null
     */
    public function findById(int $id): ?JobTitle
    {
        return JobTitle::with(['department','employees'])->find($id);
    }
    /**
     * Summary of create
     * @param array $data
     * @return TModel
     */
    public function create(array $data): JobTitle
    {
        return JobTitle::create($data);
    }
    /**
     * Summary of update
     * @param JobTitle $jobTitle
     * @param array $data
     * @return JobTitle|null
     */
    public function update(JobTitle $jobTitle, array $data): JobTitle
    {
        $jobTitle->update($data);
        return $jobTitle->fresh();
    }
    /**
     * Summary of delete
     * @param JobTitle $jobTitle
     * @return bool
     */
    public function delete(JobTitle $jobTitle): bool
    {
        return (bool) $jobTitle->delete();
    }
      /**
       * Summary of restore
       * @param int $id
       * @return JobTitle|null
       */
      public function restore(int $id): JobTitle
    {
        //get the job title including soft deleted ones
        $jobTitle = JobTitle::onlyTrashed()->findOrFail($id);
        $jobTitle->restore();

        return $jobTitle->fresh();
    }
}
