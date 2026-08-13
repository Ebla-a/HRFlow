<?php

declare(strict_types=1);

namespace Modules\Organization\Routes\Api\V1;

use Illuminate\Support\Facades\Route;
use Modules\Organization\Http\Controllers\V1\JobTitleController;

Route::middleware('auth:sanctum')
    ->prefix('v1')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Job Titles - Read
        |--------------------------------------------------------------------------
        */

        Route::get('/job-titles', [
            JobTitleController::class,
            'index',
        ])->middleware(
            'permission:jobtitles.view.all'
        );

        /*
        |--------------------------------------------------------------------------
        | Job Titles - Create
        |--------------------------------------------------------------------------
        */

        Route::post('/job-titles', [
            JobTitleController::class,
            'store',
        ])->middleware(
            'permission:jobtitle.create'
        );

        /*
        |--------------------------------------------------------------------------
        | Job Titles - Update
        |--------------------------------------------------------------------------
        */

        Route::put('/job-titles/{id}', [
            JobTitleController::class,
            'update',
        ])->middleware(
            'permission:jobtitle.update'
        );

        /*
        |--------------------------------------------------------------------------
        | Job Titles - Delete
        |--------------------------------------------------------------------------
        */

        Route::delete('/job-titles/{id}', [
            JobTitleController::class,
            'destroy',
        ])->middleware(
            'permission:jobtitle.delete'
        );

        /*
        |--------------------------------------------------------------------------
        | Job Titles - Restore
        |--------------------------------------------------------------------------
        */

        Route::post('/job-titles/{id}/restore', [
            JobTitleController::class,
            'restore',
        ])->middleware(
            'permission:jobtitle.restore'
        );
    });