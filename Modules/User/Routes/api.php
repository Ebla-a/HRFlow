<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\v1\UserController;
use Modules\User\Http\Controllers\v1\HrAdminController;

Route::prefix('v1')->middleware(['auth:sanctum', 'role:Hr_admin'])->group(function () {

    /**
     * User Management
     */
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:view.users.all')
        ->name('users.index');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->middleware('permission:view.users.all')
        ->name('users.show');

    Route::put('/users/{user}/email', [UserController::class, 'updateEmail'])
        ->middleware('permission:update.user')
        ->name('users.updateEmail');

    Route::post('/users/{user}/avatar', [UserController::class, 'updateProfileImage'])
        ->middleware('permission:update.user')
        ->name('users.updateAvatar');

    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivateUserAccount'])
        ->middleware('permission:user.inActive')
        ->name('users.deactivate');

    Route::post('/users/{user}/activate', [UserController::class, 'activateUserAccount'])
        ->middleware('permission:user.active')
        ->name('users.activate');

    /**
     * HR Admin (Roles & Permissions)
     */
    Route::post('/roles', [HrAdminController::class, 'createRole'])
        ->middleware('permission:roles.create')
        ->name('roles.create');

    Route::delete('/roles/{role}', [HrAdminController::class, 'deleteRole'])
        ->middleware('permission:roles.delete')
        ->name('roles.delete');

    Route::post('/permissions', [HrAdminController::class, 'createPermission'])
        ->middleware('permission:permissions.create')
        ->name('permissions.create');

    Route::delete('/permissions/{permission}', [HrAdminController::class, 'deletePermission'])
        ->middleware('permission:permissions.delete')
        ->name('permissions.delete');

    Route::post('/users/{user}/roles/grant', [HrAdminController::class, 'grantRole'])
        ->middleware('permission:roles.grant')
        ->name('roles.grant');

    Route::post('/users/{user}/roles/revoke', [HrAdminController::class, 'revokeRole'])
        ->middleware('permission:roles.revoke')
        ->name('roles.revoke');

    Route::post('/users/{user}/permissions/grant', [HrAdminController::class, 'grantPermission'])
        ->middleware('permission:permissions.grant')
        ->name('permissions.grant');

    Route::post('/users/{user}/permissions/revoke', [HrAdminController::class, 'revokePermission'])
        ->middleware('permission:permissions.revoke')
        ->name('permissions.revoke');
});