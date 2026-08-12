<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendance\Http\Controllers\V1\AttendanceController;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    Route::prefix('attendance')->group(function () {

        // GET /attendance - HR Admin (attendence.view.all) / Manager (view.attendence.department)
        Route::get('/', [AttendanceController::class, 'index'])
            ->middleware('permission:attendence.view.all|view.attendence.department,sanctum');

        // GET /attendance/my - Employee (attendence.view.own)
        Route::get('/my', [AttendanceController::class, 'myAttendance'])
            ->middleware('permission:attendence.view.own,sanctum');

        // GET /attendance/summary - Manager / HR Admin
        Route::get('/summary', [AttendanceController::class, 'summary'])
            ->middleware('permission:view.attendence.department|attendence.view.all,sanctum');

        // POST /attendance - HR Admin
        Route::post('/', [AttendanceController::class, 'check'])
            ->middleware('permission:attendence.check.in,sanctum');

        // PUT /attendance/{attendance} - HR Admin
        Route::put('/{attendance}', [AttendanceController::class, 'update'])
            ->middleware('permission:attendence.correct,sanctum');

    });

});