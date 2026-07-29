<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\App\Http\Controllers\V1\EmployeeController;

Route::prefix('v1')->group(function () {

    Route::get('employees', [EmployeeController::class, 'index']);

    Route::get('employees/{id}', [EmployeeController::class, 'show']);

    Route::post('employees', [EmployeeController::class, 'store']);

    Route::put('employees/{id}', [EmployeeController::class, 'update']);

    Route::patch('employees/{id}/terminate', [EmployeeController::class, 'terminate']);

});