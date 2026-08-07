<?php

use Illuminate\Support\Facades\Route;
use Modules\Leave\Http\Controllers\v1\LeaveRequestController;
use Modules\Leave\Http\Controllers\v1\LeaveTypeController;

Route::prefix('v1')
    ->middleware('auth:sanctum')
    ->group(function () {

        // --- Leave Types Routes ---
        
        Route::get('/leave-types', [LeaveTypeController::class, 'index'])
            ->middleware('permission:leave.types.view.all');

        Route::post('/leave-types', [LeaveTypeController::class, 'store'])
            ->middleware('permission:leave.type.create');

        Route::get('/leave-types/{leaveType}', [LeaveTypeController::class, 'show'])
            ->middleware('permission:leave.types.view.all');

        Route::put('/leave-types/{leaveType}', [LeaveTypeController::class, 'update'])
            ->middleware('permission:leave.type.update');

        Route::delete('/leave-types/{leaveType}', [LeaveTypeController::class, 'destroy'])
            ->middleware('permission:leave.type.delete');


        // --- Leave Requests Routes ---
        
        Route::get('/leave-requests', [LeaveRequestController::class, 'index']);

        Route::post('/leave-requests', [LeaveRequestController::class, 'store'])
            ->middleware('permission:create.leave.request');

        Route::get('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'show']);

        // --- Leave Approvals Routes ---

        Route::post('/leave-requests/{leaveRequest}/approve-manager', [LeaveRequestController::class, 'approveManager'])
            ->middleware('permission:leave.approve');

        Route::post('/leave-requests/{leaveRequest}/approve-hr', [LeaveRequestController::class, 'approveHR'])
            ->middleware('permission:leave.approve');

        Route::post('/leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])
            ->middleware('permission:leave.reject');
    });