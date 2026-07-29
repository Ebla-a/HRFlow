<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\UserController;
use Modules\User\Http\Controllers\HrAdminController;

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

Route::prefix('v1')->get('/users',[UserController::class,'getAllUsers'])->name('get_all_useres');
Route::prefix('v1')->get('/user/{id}',[UserController::class,'getUserById'])->name('getUserById');

Route::prefix('v1')->post('/user/updateEmail',[UserController::class,'updateEmail'])->name('updateEmail');
Route::prefix('v1')->post('/user/updateProfileImage',[UserController::class,'updateProfileImage'])->name('updateProfileImage');

Route::prefix('v1')->post('/user/disActiveUserAccount/{id}',[UserController::class,'disActiveUserAccount'])->name('disActiveUserAccount');
Route::prefix('v1')->post('/user/activeUserAccount/{id}',[UserController::class,'activeUserAccount'])->name('activeUserAccount');


Route::prefix('v1')->post('/Hr/createRole',[HrAdminController::class,'createRole'])->name('createRole');
Route::prefix('v1')->post('/Hr/deleteRole',[HrAdminController::class,'deleteRole'])->name('deleteRole');


Route::prefix('v1')->post('/Hr/createPermission',[HrAdminController::class,'createPermission'])->name('createPermission');
Route::prefix('v1')->post('/Hr/deletePermission',[HrAdminController::class,'deletePermission'])->name('deletePermission');


Route::prefix('v1')->post('/Hr/GrantRole',[HrAdminController::class,'GrantRole'])->name('GrantRole');
Route::prefix('v1')->post('/Hr/revokeRole',[HrAdminController::class,'revokeRole'])->name('revokeRole');


Route::prefix('v1')->post('/Hr/GrantPermission',[HrAdminController::class,'GrantPermission'])->name('GrantPermission');
Route::prefix('v1')->post('/Hr/revokePermission',[HrAdminController::class,'revokePermission'])->name('revokePermission');


});
