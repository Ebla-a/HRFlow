<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Performance\Http\Controllers\v1\PerformanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/performance', function (Request $request) {
        return $request->user();
});



Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

Route::get('/performance-cycles',[PerformanceController::class,'ShowCycles'])
        ->name('ShowCycles');

Route::post('/performance-cycles',[PerformanceController::class,'CreateCycle'])
        ->middleware('permission:create.performance.cycle')
        ->name('CreateCycle');

Route::post('/performance-cycles/{id}/activate',[PerformanceController::class,'ActivateCycle'])
        ->middleware('permission:update.performance.cycle')
        ->name('ActivateCycle');

Route::post('/performance-cycles/{id}/close',[PerformanceController::class,'CloseCycle'])
        ->middleware('permission:update.performance.cycle')
        ->name('CloseCycles');



        

Route::get('/performance-reviews',[PerformanceController::class,'ShowReviews'])
        ->middleware('permission:view.reviews.department|view.reviews.all')
        ->name('ShowReviews');





Route::post('/performance-reviews/{id}',[PerformanceController::class,'CreateReview'])
        ->middleware('permission:create.review.employee.own.department')
        ->name('CreateReview');

Route::put('/performance-reviews/{id}',[PerformanceController::class,'UpdateReview'])
        ->middleware('permission:update.review.employee.own.department')
        ->name('UpdateReview');





Route::get('/employees/{id}/performance',[PerformanceController::class,'EmployeeReviews'])
        ->middleware('permission:view.reviews.department|view.reviews.all')
        ->name('EmployeeReviews');





Route::get('/performance-reviews/my',[PerformanceController::class,'MyReviews'])
        ->middleware('permission:view.performance.reviews.own')
        ->name('MyReviews');

});