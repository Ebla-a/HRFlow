<?php

use Illuminate\Support\Facades\Route;
use Modules\Leave\Http\Controllers\v1\LeaveRequestController;
use Modules\Leave\Http\Controllers\v1\LeaveTypeController;

Route::prefix('v1')
->middleware('auth:sanctum')
->group(function(){

    Route::apiResource(
        'leave-types',
        LeaveTypeController::class
    );

    Route::apiResource(
        'leave-requests',
        LeaveRequestController::class
    )
    ->only([
        'index',
        'store',
        'show'
    ]);

    Route::post(
        'leave-requests/{leaveRequest}/approve-manager',
        [
            LeaveRequestController::class,
            'approveManager'
        ]
    );

    Route::post(
        'leave-requests/{leaveRequest}/approve-hr',
        [
            LeaveRequestController::class,
            'approveHR'
        ]
    );

    Route::post(
        'leave/requests/{leaveRequest}/reject',
        [
            LeaveRequestController::class,
            'reject'
        ]
    );
});