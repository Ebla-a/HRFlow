<?php

namespace Modules\Organization\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;
use Modules\Organization\Policies\DepartmentPolicy;
use Modules\Organization\Policies\JobTitlePolicy;

class AuthServiceProvider extends ServiceProvider
{


     protected $policies = [
        Department::class => DepartmentPolicy::class,
        JobTitle::class => JobTitlePolicy::class,
    ];


    public function boot(): void
    {
        $this->registerPolicies();
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
