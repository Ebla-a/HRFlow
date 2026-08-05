<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->prefix('report')->group(function () {
    
    Route::post('/payroll/generate/{run}', [ReportController::class,'generatePayroll']);

    // List all report summaries for a type
    Route::get('/{type}', [ReportController::class, 'index']);

    // Read a specific report summary for a type/month/year
    Route::get('/{type}/show', [ReportController::class, 'show']);

    // Generate a report summary on demand
    Route::post('/{type}/generate', [ReportController::class, 'generate']);

});
