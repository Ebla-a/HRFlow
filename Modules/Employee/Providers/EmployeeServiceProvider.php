<?php

namespace Modules\Employee\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Policies\EmployeePolicy;
use Modules\User\Providers\EventServiceProvider;

class EmployeeServiceProvider extends ServiceProvider
{
    protected $moduleName = 'Employee';

    protected $moduleNameLower = 'employee';

    public function boot(): void
    {

    if ($this->app->runningInConsole()) {
    $this->publishes([
        module_path('Employee', 'Database/Seeders') => database_path('seeders'),
    ], 'employee-seeders');
}

         Gate::policy(
            Employee::class,
            EmployeePolicy::class
        );
        $this->app->register(EventServiceProvider::class);
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

        $this->loadMigrationsFrom(
            module_path($this->moduleName, 'Database/Migrations')
        );
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path(
                $this->moduleName,
                'Config/config.php'
            ) => config_path(
                $this->moduleNameLower . '.php'
            ),
        ], 'config');

        $this->mergeConfigFrom(
            module_path(
                $this->moduleName,
                'Config/config.php'
            ),
            $this->moduleNameLower
        );
    }

    public function registerViews(): void
    {
        $viewPath = resource_path(
            'views/modules/' . $this->moduleNameLower
        );

        $sourcePath = module_path(
            $this->moduleName,
            'Resources/views'
        );

        $this->publishes([
            $sourcePath => $viewPath,
        ], [
            'views',
            $this->moduleNameLower . '-module-views',
        ]);

        $this->loadViewsFrom(
            array_merge(
                $this->getPublishableViewPaths(),
                [$sourcePath]
            ),
            $this->moduleNameLower
        );
    }

    public function registerTranslations(): void
    {
        $langPath = resource_path(
            'lang/modules/' . $this->moduleNameLower
        );

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom(
                $langPath,
                $this->moduleNameLower
            );

            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $resourcePath = module_path(
                $this->moduleName,
                'Resources/lang'
            );

            $this->loadTranslationsFrom(
                $resourcePath,
                $this->moduleNameLower
            );

            $this->loadJsonTranslationsFrom($resourcePath);
        }
    }

    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];

        foreach (Config::get('view.paths') as $path) {
            $moduleViewPath = $path .
                '/modules/' .
                $this->moduleNameLower;

            if (is_dir($moduleViewPath)) {
                $paths[] = $moduleViewPath;
            }
        }

        return $paths;
    }
}

