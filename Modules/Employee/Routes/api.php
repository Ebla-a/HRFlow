<?php
return;
use Illuminate\Support\Facades\Route;
use Modules\Employee\App\Http\Controllers\V1\EmployeeController;
use Modules\Employee\App\Http\Controllers\V1\EmployeeDocumentController;

Route::prefix('v1')->middleware(['auth:sanctum', 'ensure.api.header'])->group(function () {
    Route::get('/employees/me', [EmployeeController::class, 'me']);
    
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/employees/{employee}', [EmployeeController::class, 'show']);
    Route::put('/employees/{employee}', [EmployeeController::class, 'update']);
    Route::post('/employees/{employee}/terminate', [EmployeeController::class, 'terminate']);

    Route::get('/employees/{employee}/documents', [EmployeeDocumentController::class, 'index']);
    Route::post('/employees/{employee}/documents', [EmployeeDocumentController::class, 'store']);
});