<?php

namespace Modules\Payroll\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Modules\Employee\Events\EmployeeHired as EventsEmployeeHired;
use Modules\Payroll\Contracts\ExchangeRateFetcherInterface;
use Modules\Payroll\Contracts\ExchangeRateProviderInterface;
use Modules\Payroll\Entities\PayrollRun;
use Modules\Payroll\Entities\Payslip;
use Modules\Payroll\Listeners\CreateInitialSalaryStructure;
use Modules\Payroll\Policies\PayrollRunPolicy;
use Modules\Payroll\Policies\PayslipPolicy;
use Modules\Payroll\Services\ApiExchangeRateFetcher;
use Modules\Payroll\Services\DatabaseExchangeRateProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\Payroll\Jobs\FetchLatestExchangeRatesJob;

class PayrollServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Payroll';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'payroll';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->job(new FetchLatestExchangeRatesJob('USD'))
                ->daily()
                ->withoutOverlapping();
        });
        Event::listen(
            EventsEmployeeHired::class,
            CreateInitialSalaryStructure::class
        );

        Gate::policy(PayrollRun::class, PayrollRunPolicy::class);
        Gate::policy(Payslip::class, PayslipPolicy::class);



        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ExchangeRateProviderInterface::class, DatabaseExchangeRateProvider::class);
        $this->app->bind(ExchangeRateFetcherInterface::class, ApiExchangeRateFetcher::class);
        $this->app->register(RouteServiceProvider::class);
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
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
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
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
