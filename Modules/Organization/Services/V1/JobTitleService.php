<?php
namespace Modules\Organization\Services\V1;
use Illuminate\Support\Facades\Cache;
use Modules\Organization\DTO\V1\JobTitleDTO;
use Modules\Organization\Entities\JobTitle;
use Modules\Organization\Repository\Contracts\JobTitleRepositoryInterface;
use Modules\Organization\Transformers\V1\JobTitleResource;

class JobTitleService
{
    public function __construct(
        protected JobTitleRepositoryInterface $jobTitleRepository
    ) {}
   
    public function getAllJobTitles()
    {

      $page = request('page', 1);
    $perPage = request('per_page', 12);

    $cacheKey = "jobTitles_page_{$page}_per_{$perPage}";
    $tag = Cache::tags(['JobTitles']);

    return $tag->remember($cacheKey, now()->addHours(1), function () use ($tag, $cacheKey) {

        $lock = Cache::lock("lock:{$cacheKey}", 10);

        return $lock->block(5, function () use ($tag, $cacheKey) {

            $data = $tag->get($cacheKey);
            if ($data !== null) {
                return $data;
            }

            $paginator = $this->jobTitleRepository->getAll();

              $formattedData = json_decode(
                JobTitleResource::collection($paginator->items())->toJson(),
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
     * Summary of createJobTitle
     * @param JobTitleDTO $dto
     * @return JobTitle
     */
    public function createJobTitle(JobTitleDTO $dto): JobTitle
    {
        $jobTitle= $this->jobTitleRepository->create($dto->toArray());
        //invalidate the cache for job titles after creating a new job title
        Cache::tags(['JobTitles'])->flush();
        return $jobTitle;

    }
    /**
     * Summary of updateJobTitle
     * @param int $id
     * @param JobTitleDTO $dto
     * @return JobTitle
     */
    public function updateJobTitle(int $id, JobTitleDTO $dto): JobTitle
    {
        $jobTitle = $this->jobTitleRepository->findById($id);
        $jobTitleNew= $this->jobTitleRepository->update($jobTitle, $dto->toArray());
        Cache::tags(['JobTitles'])->flush();
        return $jobTitleNew;

    }
    /**
     * Summary of deleteJobTitle
     * @param int $id
     * @return bool
     */
    public function deleteJobTitle(int $id): bool
    {
        $jobTitle = $this->jobTitleRepository->findById($id);

        $deletedJob= $this->jobTitleRepository->delete($jobTitle);
        Cache::tags(['JobTitles'])->flush();
         return $deletedJob;
    }
     /**
      * Summary of restoreJobTitle
      * @param int $id
      * @return JobTitle
      */
     public function restoreJobTitle(int $id)
    {
        $department = $this->jobTitleRepository->restore($id);

        Cache::tags(['JobTitles'])->flush();

        return $department;
    }
}
