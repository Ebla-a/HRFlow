<?php

namespace Modules\Organization\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Organization\Entities\JobTitle;
use Modules\User\Entities\User;

class JobTitlePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Summary of viewAny
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('jobtitles.view.all');
    }
    /**
     * Summary of create
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('jobtitle.create');
    }
    /**
     * Summary of update
     * @param User $user
     * @param JobTitle $jobTitle
     * @return bool
     */
    public function update(User $user, JobTitle $jobTitle): bool
    {
        return $user->can('jobtitle.update');
    }
    /**
     * Summary of delete
     * @param User $user
     * @param JobTitle $jobTitle
     * @return bool
     */
    public function delete(User $user, JobTitle $jobTitle): bool
    {
        if (! $user->can('jobtitle.delete')) {
            return false;
        }

        return ! $jobTitle->employees()->exists();
    }
}
