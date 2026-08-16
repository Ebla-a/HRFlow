<?php

namespace Modules\Performance\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider; 
use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\Performance\Entities\PerformanceReview;
use Modules\Performance\Policies\PerformancePolicy;

class PerformanceServiceProvider extends ServiceProvider
{
    protected $moduleName = 'Performance';

    protected $moduleNameLower = 'performance';

    public function boot(): void
    {
        /*
         * Register Performance policies.
         */
        Gate::policy(
            PerformanceCycle::class,
            PerformancePolicy::class
        );

        Gate::policy(
            PerformanceReview::class,
            PerformancePolicy::class
        );

         Gate::policy(
        Employee::class,
        PerformancePolicy::class
    );

        /*
         * Employee is also authorized through
         * PerformancePolicy for performance-related actions.
         */ 

        $this->registerPolicies();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

        $this->loadMigrationsFrom(
            module_path(
                $this->moduleName,
                'Database/Migrations'
            )
        );
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerPolicies(): void
    {
        //
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

        foreach (config('view.paths') as $path) {
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