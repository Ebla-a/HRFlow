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


    public function viewAny(User $user): bool
    {
        return $user->can('jobtitles.view.all');
    }

    public function create(User $user): bool
    {
        return $user->can('jobtitle.create');
    }

    public function update(User $user, JobTitle $jobTitle): bool
    {
        return $user->can('jobtitle.update');
    }

    public function delete(User $user, JobTitle $jobTitle): bool
    {
        if (! $user->can('jobtitle.delete')) {
            return false;
        }

        return ! $jobTitle->employees()->exists();
    }
}
