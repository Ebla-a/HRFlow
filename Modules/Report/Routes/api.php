<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

Route::middleware('auth:sanctum')->prefix('report')->group(function () {

    Route::post('/payroll/generate/{run}', [ReportController::class, 'generatePayroll']);


    Route::get('/{type}/export', [ReportController::class, 'exportExcel']);

    // List all report summaries for a type
    Route::get('/{type}', [ReportController::class, 'index']);

    // Read a specific report summary for a type/month/year
    Route::get('/{type}/show', [ReportController::class, 'show']);

    // Generate a report summary on demand
    Route::post('/{type}/generate', [ReportController::class, 'generate']);

});