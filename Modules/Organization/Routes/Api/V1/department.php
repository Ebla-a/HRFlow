<?php

declare(strict_types=1);

namespace Modules\Organization\Routes\Api\V1;

use Illuminate\Support\Facades\Route;
use Modules\Organization\Http\Controllers\V1\DepartmentController;

Route::middleware('auth:sanctum')
    ->prefix('v1')
    ->group(function () {

        /*
         * =========================================================
         * Department Listing
         * =========================================================
         */

        Route::get('/departments', [
            DepartmentController::class,
            'index',
        ])->middleware(
            'permission:departments.view|departments.view.all'
        );


        /*
         * =========================================================
         * Department Creation
         * =========================================================
         */

        Route::post('/departments', [
            DepartmentController::class,
            'store',
        ])->middleware(
            'permission:departments.create'
        );


        /*
         * =========================================================
         * Department Details
         * =========================================================
         */

        Route::get('/departments/{id}', [
            DepartmentController::class,
            'show',
        ]);


        /*
         * =========================================================
         * Department Update
         * =========================================================
         */

        Route::put('/departments/{id}', [
            DepartmentController::class,
            'update',
        ])->middleware(
            'permission:departments.update'
        );


        /*
         * =========================================================
         * Department Delete
         * =========================================================
         */

        Route::delete('/departments/{id}', [
            DepartmentController::class,
            'destroy',
        ])->middleware(
            'permission:departments.delete'
        );


        /*
         * =========================================================
         * Department Restore
         * =========================================================
         */

        Route::post('/departments/{id}/restore', [
            DepartmentController::class,
            'restore',
        ])->middleware(
            'permission:departments.restore'
        );


        /*
         * =========================================================
         * Assign Manager
         * =========================================================
         *
         */

        Route::put('/departments/{id}/assign-manager', [
            DepartmentController::class,
            'assignManager',
        ])->middleware(
            'permission:departments.assign-manager'
        );
    });