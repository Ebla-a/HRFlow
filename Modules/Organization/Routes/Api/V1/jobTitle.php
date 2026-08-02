<?php
namespace Modules\Organization\Routes\Api\V1;

use Illuminate\Support\Facades\Route;
use Modules\Organization\Http\Controllers\V1\JobTitleController;

Route::prefix('v1')->group(function () {

   Route::get('/job-titles', [JobTitleController::class, 'index']);

    Route::post('/job-titles', [JobTitleController::class, 'store']);
        // ->middleware('permission:jobtitle.create');

    Route::put('/job-titles/{id}', [JobTitleController::class, 'update']);
        // ->middleware('permission:jobtitle.update');

    Route::delete('/job-titles/{id}', [JobTitleController::class, 'destroy']);
        // ->middleware('permission:jobtitle.delete');

          Route::post('job-titles/{id}/restore', [JobTitleController::class, 'restore']);
        // ->middleware('jobtitles.restore');
});
