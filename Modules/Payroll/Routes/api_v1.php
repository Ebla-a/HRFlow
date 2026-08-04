<?php

use Illuminate\Support\Facades\Route;
use Modules\Payroll\Http\Controllers\V1\PayrollRunController;
use Modules\Payroll\Http\Controllers\V1\PayslipController;

Route::prefix('v1/payroll')->middleware(['auth:sanctum'])->group(function () {
    
    Route::prefix('runs')->group(function () {
        Route::get('/', [PayrollRunController::class, 'index']);
        Route::post('/', [PayrollRunController::class, 'store']);
        Route::post('/{payrollRun}/process', [PayrollRunController::class, 'process']);
        Route::post('/{payrollRun}/finalize', [PayrollRunController::class, 'finalize']);
        Route::get('/{payrollRun}/summary', [PayrollRunController::class, 'summary']);
    });

    Route::prefix('payslips')->group(function () {
        Route::get('/{payslip}', [PayslipController::class, 'show']);
        Route::post('/{payslip}/deductions', [PayslipController::class, 'addDeduction']);
    });

});