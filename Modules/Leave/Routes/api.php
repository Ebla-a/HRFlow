<?php

use Illuminate\Support\Facades\Route;
use Modules\Leave\Http\Controllers\v1\LeaveRequestController;
use Modules\Leave\Http\Controllers\v1\LeaveTypeController;

Route::prefix('v1')
    ->middleware('auth:sanctum')
    ->group(function () {

        Route::apiResource(
            'leave-types',
            LeaveTypeController::class
        );

        Route::prefix('leave')
            ->group(function () {

                Route::get(
                    'requests',
                    [
                        LeaveRequestController::class,
                        'index'
                    ]
                );

                Route::post(
                    'requests',
                    [
                        LeaveRequestController::class,
                        'store'
                    ]
                );

                Route::get(
                    'requests/{leaveRequest}',
                    [
                        LeaveRequestController::class,
                        'show'
                    ]
                );

                Route::post(
                    'requests/{leaveRequest}/approve-manager',
                    [
                        LeaveRequestController::class,
                        'approveManager'
                    ]
                );

                Route::post(
                    'requests/{leaveRequest}/approve-hr',
                    [
                        LeaveRequestController::class,
                        'approveHR'
                    ]
                );

                Route::post(
                    'requests/{leaveRequest}/reject',
                    [
                        LeaveRequestController::class,
                        'reject'
                    ]
                );
            });
    });
     