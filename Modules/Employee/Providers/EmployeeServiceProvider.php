<?php

namespace Modules\Employee\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Employee\Observers\EmployeeObserver;
use Modules\Employee\App\Policies\EmployeePolicy;
use Modules\Employee\Entities\Employee;
use Modules\User\Providers\EventServiceProvider;

/**
 * Summary of EmployeeServiceProvider
 */
class EmployeeServiceProvider extends ServiceProvider
{
    protected $moduleName = 'Employee';
    protected $moduleNameLower = 'employee';
    /**
     * Summary of boot
     * @return void
     */
    public function boot()
    {
        Employee::observe(EmployeeObserver::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
        
        $this->app->register(EventServiceProvider::class);
        $this->loadMigrationsFrom(__DIR__ . '/../../Database/Migrations');

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

        $this->loadMigrationsFrom(
            module_path($this->moduleName, 'Database/Migrations')
        );
    }
    /**
     * Summary of register
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php')
                => config_path($this->moduleNameLower . '.php'),
        ], 'config');

        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }
    /**
     * Summary of registerViews
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(
            array_merge($this->getPublishableViewPaths(), [$sourcePath]),
            $this->moduleNameLower
        );
    }
    /**
     * Summary of registerTranslations
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(
                module_path($this->moduleName, 'Resources/lang'),
                $this->moduleNameLower
            );

            $this->loadJsonTranslationsFrom(
                module_path($this->moduleName, 'Resources/lang')
            );
        }
    }
    /**
     * Summary of provides
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];

        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }

        return $paths;
    }
}