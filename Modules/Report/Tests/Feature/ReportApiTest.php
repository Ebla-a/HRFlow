<?php

namespace Modules\Report\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    public function test_report_api_routes_are_registered(): void
    {
        $uris = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => $route->uri())
            ->values()
            ->all();

        $this->assertContains('api/v1/reports/{type}', $uris);
        $this->assertContains('api/v1/reports/{type}/show', $uris);
        $this->assertContains('api/v1/reports/{type}/generate', $uris);
        $this->assertContains('api/v1/reports/payroll/generate/{run}', $uris);
    }

    public function test_report_routes_use_generate_controller_action(): void
    {
        $routes = Route::getRoutes()->getRoutes();

        $generate = collect($routes)->first(
            fn ($route) => $route->uri() === 'api/v1/reports/{type}/generate'
        );

        $this->assertNotNull($generate);
        $this->assertSame('POST', $generate->methods()[0]);
    }
}
