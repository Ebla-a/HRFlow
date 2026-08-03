<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\App\Http\Controllers\V1\EmployeeController;
use Modules\Employee\App\Http\Controllers\V1\EmployeeDocumentController;

Route::prefix('v1')->middleware(['auth:sanctum', 'ensure.api.header'])->group(function () {
    
    // --- Employee Routes ---
    Route::get('/employees/me', [EmployeeController::class, 'me']); 
    
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/employees/{employee}', [EmployeeController::class, 'show']);
    Route::put('/employees/{employee}', [EmployeeController::class, 'update']);
    Route::post('/employees/{employee}/terminate', [EmployeeController::class, 'terminate']);

    // --- Employee Documents Routes ---
    Route::get('/employees/{employee}/documents', [EmployeeDocumentController::class, 'index']);
    Route::post('/employees/{employee}/documents', [EmployeeDocumentController::class, 'store']);
    
    Route::post('/documents/{document}', [EmployeeDocumentController::class, 'update']); 
    Route::delete('/documents/{document}', [EmployeeDocumentController::class, 'destroy']);
    Route::get('/documents/{document}/download', [EmployeeDocumentController::class, 'download']);
});