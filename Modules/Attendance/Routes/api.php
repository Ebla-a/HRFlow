<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendance\Http\Controllers\V1\AttendanceController;


Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    Route::prefix('attendance')->group(function () {


        // Attendance list + filters
        Route::get('/', [AttendanceController::class, 'index']);


        // Check In / Check Out
        Route::post('/', [AttendanceController::class, 'check']);


        // Show single attendance
        Route::get('/{attendance}', [AttendanceController::class, 'show']);


        // HR update correction
        Route::put('/{attendance}', [AttendanceController::class, 'update']);


        // Attendance logs
        Route::get('/logs', [AttendanceController::class, 'logs']);

    });

});