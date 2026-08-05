<?php

namespace Modules\Organization\Routes\Api\V1;

use Illuminate\Support\Facades\Route;
use Modules\Organization\Http\Controllers\V1\JobTitleController;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    Route::get('/job-titles', [JobTitleController::class, 'index'])
        ->middleware('permission:job_titles.view');

    Route::post('/job-titles', [JobTitleController::class, 'store'])
        ->middleware('permission:job_titles.create');

    Route::put('/job-titles/{id}', [JobTitleController::class, 'update'])
        ->middleware('permission:job_titles.update');

    Route::delete('/job-titles/{id}', [JobTitleController::class, 'destroy'])
        ->middleware('permission:job_titles.delete');

    Route::post('/job-titles/{id}/restore', [JobTitleController::class, 'restore'])
        ->middleware('permission:job_titles.restore');
});