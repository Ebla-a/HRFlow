<?php

use Illuminate\Support\Facades\Route;
use Modules\Payroll\Http\Controllers\V2\PayrollRunController as PayrollRunControllerV2;
use Modules\Payroll\Http\Controllers\V2\PayslipController as PayslipControllerV2;

Route::prefix('v2/payroll')->middleware(['auth:sanctum'])->group(function () {

    Route::prefix('runs')->group(function () {
        Route::get('/', [PayrollRunControllerV2::class, 'index']);
        Route::post('/', [PayrollRunControllerV2::class, 'store']);
        Route::post('/{payrollRun}/process', [PayrollRunControllerV2::class, 'process']);
        Route::post('/{payrollRun}/finalize', [PayrollRunControllerV2::class, 'finalize']);
        Route::get('/{payrollRun}/summary', [PayrollRunControllerV2::class, 'summary']);
    });

    Route::prefix('payslips')->group(function () {
        Route::get('/{payslip}', [PayslipControllerV2::class, 'show']);
        Route::post('/{payslip}/deductions', [PayslipControllerV2::class, 'addDeduction']);
    });

});