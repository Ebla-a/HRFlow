<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Modules\User\Http\Controllers\v1\HrAdminController as V1HrAdminController;
use Modules\User\Http\Controllers\v1\UserController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->middleware(['role:Hr_admin','auth:sanctum'])->group(function () {


Route::post('/user/updateEmail',[UserController::class,'updateEmail'])->name('updateEmail');
Route::post('/user/updateProfileImage',[UserController::class,'updateProfileImage'])->name('updateProfileImage');

Route::get('/users',[UserController::class,'getAllUsers'])->name('get_all_useres');
Route::get('/user/{id}',[UserController::class,'getUserById'])->name('getUserById');


Route::post('/user/disActiveUserAccount/{id}',[UserController::class,'disActiveUserAccount'])->name('disActiveUserAccount');
Route::post('/user/activeUserAccount/{id}',[UserController::class,'activeUserAccount'])->name('activeUserAccount');


Route::post('/Hr/createRole',[V1HrAdminController::class,'createRole'])->name('createRole');
Route::post('/Hr/deleteRole',[V1HrAdminController::class,'deleteRole'])->name('deleteRole');


Route::post('/Hr/createPermission',[V1HrAdminController::class,'createPermission'])->name('createPermission');
Route::post('/Hr/deletePermission',[V1HrAdminController::class,'deletePermission'])->name('deletePermission');


Route::post('/Hr/GrantRole',[V1HrAdminController::class,'GrantRole'])->name('GrantRole');
Route::post('/Hr/revokeRole',[V1HrAdminController::class,'revokeRole'])->name('revokeRole');


Route::post('/Hr/GrantPermission',[V1HrAdminController::class,'GrantPermission'])->name('GrantPermission');
Route::post('/Hr/revokePermission',[V1HrAdminController::class,'revokePermission'])->name('revokePermission');


});
