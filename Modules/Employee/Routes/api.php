<?php

 

use Illuminate\Support\Facades\Route;
use Modules\Employee\App\Http\Controllers\V1\EmployeeController;

Route::prefix('v1')->group(function () {

    Route::get('employees', [EmployeeController::class, 'index']);

    Route::get('employees/{employee}', [EmployeeController::class, 'show']);

    Route::post('employees', [EmployeeController::class, 'store']);

    Route::put('employees/{employee}', [EmployeeController::class, 'update']);

    Route::patch(
        'employees/{employee}/terminate',
        [EmployeeController::class, 'terminate']
    );

});