<?php
namespace Modules\Organization\Repository\Implementation;

use Illuminate\Database\Eloquent\Collection;
use Modules\Organization\Entities\JobTitle;
use Modules\Organization\Repository\Contracts\JobTitleRepositoryInterface;

class JobTitleRepository implements JobTitleRepositoryInterface


{
    public function getAll()
    {
    $perPage = request('per_page', 12);

        return JobTitle::active()->with(['employees:id,first_name,last_name,user_id,job_title_id','department:id,name'])->latest()
        ->paginate($perPage);;
    }

    public function findById(int $id): ?JobTitle
    {
        return JobTitle::with(['department','employees'])->find($id);
    }

    public function create(array $data): JobTitle
    {
        return JobTitle::create($data);
    }

    public function update(JobTitle $jobTitle, array $data): JobTitle
    {
        $jobTitle->update($data);
        return $jobTitle->fresh();
    }

    public function delete(JobTitle $jobTitle): bool
    {
        return (bool) $jobTitle->delete();
    }

      public function restore(int $id): JobTitle
    {
        //get the job title including soft deleted ones
        $jobTitle = JobTitle::onlyTrashed()->findOrFail($id);
        $jobTitle->restore();

        return $jobTitle->fresh();
    }
}
