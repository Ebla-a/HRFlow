<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('report')->group(function () {
    Route::get('/{type}', [ReportController::class, 'index']);
    Route::get('/{type}/show', [ReportController::class, 'show']);
    Route::post('/{type}/generate', [ReportController::class, 'generate']);
});
