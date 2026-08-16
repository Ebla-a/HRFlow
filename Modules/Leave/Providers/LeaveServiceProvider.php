<?php

namespace Modules\Leave\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Modules\Leave\Entities\LeaveRequest;
use Modules\Leave\Policies\LeaveRequestPolicy;
use Modules\Leave\Repositories\LeaveRequestRepository;
use Modules\Leave\Repositories\Interfaces\LeaveRequestRepositoryInterface;
use Modules\Leave\Providers\RouteServiceProvider;
use Modules\Leave\Providers\EventServiceProvider;
use Modules\Leave\Observers\LeaveRequestObserver;
use Modules\Leave\Repositories\Interfaces\LeaveBalanceRepositoryInterface;
use Modules\Leave\Repositories\LeaveTypeRepository;
use Modules\Leave\Repositories\Interfaces\LeaveTypeRepositoryInterface;
use Modules\Leave\Repositories\LeaveBalanceRepository;

class LeaveServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Leave';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'leave';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
   {
      Gate::policy(
        LeaveRequest::class,
        LeaveRequestPolicy::class
     );

      LeaveRequest::observe(
        LeaveRequestObserver::class
     );

    $this->registerTranslations();
    $this->registerConfig();
    $this->registerViews();
    $this->loadMigrationsFrom(
        module_path($this->moduleName, 'Database/Migrations')
     );
  }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
   {
      $this->app->register(
        RouteServiceProvider::class
      );

      $this->app->register(
        EventServiceProvider::class
      );

      $this->app->bind(
        LeaveRequestRepositoryInterface::class,
        LeaveRequestRepository::class
      );

      $this->app->bind(
        LeaveTypeRepositoryInterface::class,
        LeaveTypeRepository::class
      );
       $this->app->bind(
        LeaveBalanceRepositoryInterface::class,
        LeaveBalanceRepository::class
      );
   }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'));
        }
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

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
