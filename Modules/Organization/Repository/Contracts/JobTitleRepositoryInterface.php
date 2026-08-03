<?php
namespace Modules\Organization\Repository\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Organization\Entities\JobTitle;

interface JobTitleRepositoryInterface
{
    public function getAll();
    public function findById(int $id): ?JobTitle;
    public function create(array $data): JobTitle;
    public function update(JobTitle $jobTitle, array $data): JobTitle;
    public function delete(JobTitle $jobTitle): bool;
    public function restore(int $id): JobTitle;

}
