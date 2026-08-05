<?php

namespace Modules\Organization\Routes\Api\V1;

use Illuminate\Support\Facades\Route;
use Modules\Organization\Http\Controllers\V1\DepartmentController;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    Route::get('/departments', [DepartmentController::class, 'index'])
        ->middleware('permission:departments.view');

    Route::post('/departments', [DepartmentController::class, 'store'])
        ->middleware('permission:departments.create'); 

    Route::get('/departments/{id}', [DepartmentController::class, 'show'])
        ->middleware('permission:departments.show');

    Route::put('/departments/{id}', [DepartmentController::class, 'update'])
        ->middleware('permission:departments.update');

    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])
        ->middleware('permission:departments.delete');

    Route::post('/departments/{id}/restore', [DepartmentController::class, 'restore'])
        ->middleware('permission:departments.restore');

    Route::put('/departments/{id}/assign-manager', [DepartmentController::class, 'assignManager'])
        ->middleware('permission:departments.assign_manager');
});