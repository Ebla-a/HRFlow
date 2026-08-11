<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

Route::middleware(['auth:sanctum'])->prefix('v1/reports')->group(function () {

    Route::post('/payroll/generate/{run}', [ReportController::class, 'generatePayroll'])
        ->middleware('permission:create.payroll.run');

    Route::get('/{type}/export', [ReportController::class, 'exportExcel'])
        ->middleware('permission:export.report');

    Route::get('/{type}', [ReportController::class, 'index'])
        ->middleware('permission:view.reports.all|report.view.department');

    Route::get('/{type}/show', [ReportController::class, 'show'])
        ->middleware('permission:view.reports.all|report.view.department');

    Route::post('/{type}/generate', [ReportController::class, 'generate'])
        ->middleware('permission:create.report');

});