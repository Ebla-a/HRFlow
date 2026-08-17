<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\V1\EmployeeController;
use Modules\Employee\Http\Controllers\V1\EmployeeDocumentController;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    // --- Employee Routes ---
    
    Route::get('/employees/me', [EmployeeController::class, 'me']); 

    Route::get('/employees', [EmployeeController::class, 'index']) 
     ->middleware('permission:employees.view.all');

    Route::post('/employees', [EmployeeController::class, 'store'])
        ->middleware('permission:employee.create');

    Route::get('/employees/{employee}', [EmployeeController::class, 'show']);

    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])
        ->middleware('permission:employee.update');

    Route::post('/employees/{employee}/terminate', [EmployeeController::class, 'terminate'])
    ->middleware('permission:employee.change.status,sanctum');

    // --- Employee Documents Routes ---
    
    Route::get('/employees/{employee}/documents', [EmployeeDocumentController::class, 'index']);
        

    Route::post('/employees/{employee}/documents', [EmployeeDocumentController::class, 'store'])
        ->middleware('permission:upload.documents.employee.all');

    Route::post('/documents/{document}', [EmployeeDocumentController::class, 'update'])
        ->middleware('permission:upload.documents.employee.all'); 

    Route::delete('/documents/{document}', [EmployeeDocumentController::class, 'destroy'])
        ->middleware('permission:delete.documents.employee.all');

    Route::get('/documents/{document}/download', [EmployeeDocumentController::class, 'download'])
        ->middleware('permission:view.documents.employee.all');
});