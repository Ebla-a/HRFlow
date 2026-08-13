<?php

declare(strict_types=1);

namespace Modules\Organization\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Organization\Entities\JobTitle;
use Modules\User\Entities\User;

class JobTitlePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(
            'jobtitles.view.all',
            'sanctum'
        );
    }

    public function view(User $user, JobTitle $jobTitle): bool
    {
        return $user->hasPermissionTo(
            'jobtitles.view.all',
            'sanctum'
        );
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(
            'jobtitle.create',
            'sanctum'
        );
    }

    public function update(
        User $user,
        JobTitle $jobTitle
    ): bool {
        return $user->hasPermissionTo(
            'jobtitle.update',
            'sanctum'
        );
    }

    public function delete(
        User $user,
        JobTitle $jobTitle
    ): bool {
        if (! $user->hasPermissionTo(
            'jobtitle.delete',
            'sanctum'
        )) {
            return false;
        }

        return ! $jobTitle->employees()->exists();
    }

    public function restore(
        User $user,
        JobTitle $jobTitle
    ): bool {
        return $user->hasPermissionTo(
            'jobtitle.restore',
            'sanctum'
        );
    }
}