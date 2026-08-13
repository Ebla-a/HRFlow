<?php

use Illuminate\Support\Facades\Route;
use Modules\Payroll\Http\Controllers\V1\ExchangeRateController;
use Modules\Payroll\Http\Controllers\V1\PayrollRunController;
use Modules\Payroll\Http\Controllers\V1\PayslipController;
use Modules\Payroll\Http\Controllers\V1\SalaryStructureController;

Route::prefix('v1/payroll')->middleware(['auth:sanctum'])->group(function () {
    
    // Salary Structures
    Route::prefix('salary-structures')->group(function () {
        Route::get('/', [SalaryStructureController::class, 'index'])
            ->middleware('permission:view.structure.salary.all');
            
        Route::post('/', [SalaryStructureController::class, 'store'])
            ->middleware('permission:create.structure.salary');
            
        Route::put('/{salaryStructure}', [SalaryStructureController::class, 'update'])
            ->middleware('permission:update.structure.salary');
    });

    // Payroll Run
    Route::prefix('runs')->group(function () {
        Route::get('/', [PayrollRunController::class, 'index'])
            ->middleware('permission:view_payroll_runs');
            
        Route::post('/', [PayrollRunController::class, 'store'])
            ->middleware('permission:create.payroll.run');
            
        Route::post('/{payrollRun}/process', [PayrollRunController::class, 'process'])
            ->middleware('permission:create.payroll.run');
            
        Route::post('/{payrollRun}/finalize', [PayrollRunController::class, 'finalize'])
            ->middleware('permission:finalize.payroll.run');
            
        Route::get('/{payrollRun}/summary', [PayrollRunController::class, 'summary'])
            ->middleware('permission:view_payroll_runs');
            
        Route::get('/{payrollRun}/payslips', [PayrollRunController::class, 'payslips'])
            ->middleware('permission:view.payslip.all');
    });

    // Payslip
    Route::prefix('payslips')->group(function () {
        Route::get('/my', [PayslipController::class, 'myPayslips'])
            ->middleware('permission:view.payslip.own');
            
        Route::get('/{payslip}', [PayslipController::class, 'show'])
            ->middleware('permission:view.payslip.all|view.payslip.own');
            
        Route::post('/{payslip}/deductions', [PayslipController::class, 'addDeduction'])
            ->middleware('permission:update.payslip');
    });


    // Exchange Rates
    Route::prefix('exchange-rates')->group(function () {
        Route::get('/', [ExchangeRateController::class, 'index'])
            ->middleware('permission:view.exchange.rates'); 
    });

});