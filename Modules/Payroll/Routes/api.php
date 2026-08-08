<?php

use Illuminate\Support\Facades\Route;
use Modules\Payroll\Http\Controllers\V1\PayrollRunController;
use Modules\Payroll\Http\Controllers\V1\PayslipController;
use Modules\Payroll\Http\Controllers\V1\SalaryStructureController;

Route::prefix('v1/payroll')->middleware(['auth:sanctum'])->group(function () {
    
    // Salary strucuer
    // Route::prefix('salary-structures')->group(function () {
    //     Route::get('/', [SalaryStructureController::class, 'index']);
    //     Route::post('/', [SalaryStructureController::class, 'store']);
    //     Route::put('/{id}', [SalaryStructureController::class, 'update']);
    // });

    // payroll run
    Route::prefix('runs')->group(function () {
        Route::get('/', [PayrollRunController::class, 'index']);
        Route::post('/', [PayrollRunController::class, 'store']);
        Route::post('/{payrollRun}/process', [PayrollRunController::class, 'process']);
        Route::post('/{payrollRun}/finalize', [PayrollRunController::class, 'finalize']);
        Route::get('/{payrollRun}/summary', [PayrollRunController::class, 'summary']);
        Route::get('/{payrollRun}/payslips', [PayrollRunController::class, 'payslips']); // قائمة القسائم للدورة
    });

    // payslip
    Route::prefix('payslips')->group(function () {
        Route::get('/my', [PayslipController::class, 'myPayslips']); // قسائم راتبي للموظف
        Route::get('/{payslip}', [PayslipController::class, 'show']);
        Route::post('/{payslip}/deductions', [PayslipController::class, 'addDeduction']);
    });

});