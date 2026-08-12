<?php

declare(strict_types=1);

namespace Modules\Organization\Services\V1;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Modules\Organization\DTO\V1\AssignManagerDTO;
use Modules\Organization\DTO\V1\JobTitleDTO;
use Modules\Organization\DTO\V1\StoreJobTitleDto;
use Modules\Organization\DTO\V1\UpdateJobTitleDto;
use Modules\Organization\Entities\JobTitle;
use Modules\Organization\Repository\Contracts\JobTitleRepositoryInterface;
use Modules\Organization\Transformers\V1\JobTitleResource;

class JobTitleService
{
    public function __construct(
        protected JobTitleRepositoryInterface $jobTitleRepository
    ) {}

    /**
     * Get paginated job titles.
     */
    public function getAllJobTitles(): array
{
    $page = (int) request('page', 1);
    $perPage = (int) request('per_page', 12);

    $cacheKey = "jobTitles_page_{$page}_per_{$perPage}";

    return Cache::tags(['JobTitles'])->remember(
        $cacheKey,
        now()->addHour(),
        function () {
            $paginator = $this->jobTitleRepository->getAll();

            return [
                'data' => json_decode(
                    JobTitleResource::collection(
                        $paginator->items()
                    )->toJson(),
                    true
                ),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ];
        }
    );
}
    /**
     * Create a new job title.
     */
    public function createJobTitle(StoreJobTitleDto $dto): JobTitle
    {
        $jobTitle = $this->jobTitleRepository->create(
            $dto->toArray()
        );

        $this->clearCache();

        return $jobTitle;
    }

    /**
     * Update an existing job title.
     */
    public function updateJobTitle(
        int $id,
        UpdateJobTitleDto $dto
    ): JobTitle {
        $jobTitle = $this->findJobTitleOrFail($id);

        $updatedJobTitle = $this->jobTitleRepository->update(
            $jobTitle,
            $dto->toArray()
        );

        $this->clearCache();

        return $updatedJobTitle;
    }

    /**
     * Soft delete a job title.
     */
    public function deleteJobTitle(int $id): bool
    {
        $jobTitle = $this->findJobTitleOrFail($id);

        $deleted = $this->jobTitleRepository->delete($jobTitle);

        $this->clearCache();

        return $deleted;
    }

    /**
     * Restore a soft deleted job title.
     */
   public function restoreJobTitle(int $id): JobTitle
{
    $jobTitle = JobTitle::withTrashed()->find($id);

    if (! $jobTitle) {
      abort(404, "Job title not found.");
    }

    if (! $jobTitle->trashed()) {
        return $jobTitle;
    }

    $jobTitle->restore();

    Cache::tags(['JobTitles'])->flush();

    return $jobTitle->fresh();
}
    /**
     * Find a job title or throw a proper 404 exception.
     */
    private function findJobTitleOrFail(int $id): JobTitle
    {
        $jobTitle = $this->jobTitleRepository->findById($id);

        if (! $jobTitle) {
            throw $this->jobTitleNotFoundException();
        }

        return $jobTitle;
    }

    /**
     * Build the standard JobTitle not found exception.
     */
    private function jobTitleNotFoundException(): ModelNotFoundException
    {
        $exception = new ModelNotFoundException();

        $exception->setModel(
            JobTitle::class
        );

        return $exception;
    }

    /**
     * Clear all cached JobTitle data.
     */
    private function clearCache(): void
    {
        Cache::tags(['JobTitles'])->flush();
    }
}