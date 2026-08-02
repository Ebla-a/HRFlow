<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\App\Http\Controllers\V1\EmployeeController;

Route::prefix('employee')->group(function () {

    Route::get('/', [
        EmployeeController::class,
        'index'
    ]);

}); 