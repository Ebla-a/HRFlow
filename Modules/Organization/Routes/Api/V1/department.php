<?php
namespace Modules\Organization\Routes\Api\V1;

use Illuminate\Support\Facades\Route;
use Modules\Organization\Http\Controllers\V1\DepartmentController;

Route::prefix('v1')->group(function () {

    Route::get('/departments', [DepartmentController::class, 'index']);

    Route::post('/departments', [DepartmentController::class, 'store']);
        // ->middleware('role:Hr_admin');

    Route::get('/departments/{id}', [DepartmentController::class, 'show']);
        // ->middleware('role:Hr_admin|manager');

    Route::put('/departments/{id}', [DepartmentController::class, 'update']);
        // ->middleware('permission:department.update');

    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);
        // ->middleware('permission:department.delete');


        Route::post('departments/{id}/restore', [DepartmentController::class, 'restore']);
        // ->middleware('departments.restore');

  
    Route::put('departments/{id}/assign-manager', [DepartmentController::class, 'assignManager']);
        // ->middleware('departments.assign-manager');
});
