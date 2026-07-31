<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\v1\UserController;
use Modules\User\Http\Controllers\v1\HrAdminController;

Route::prefix('v1')->middleware(['auth:sanctum', 'role:Hr_admin'])->group(function () {

    /**
     * User Management
     */
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    Route::put('/users/{user}/email', [UserController::class, 'updateEmail'])->name('users.updateEmail');
    Route::post('/users/{user}/avatar', [UserController::class, 'updateProfileImage'])->name('users.updateAvatar');

    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivateUserAccount'])->name('users.deactivate');
    Route::post('/users/{user}/activate', [UserController::class, 'activateUserAccount'])->name('users.activate');

    /**
     * HR Admin (Roles & Permissions)
     */
    Route::post('/roles', [HrAdminController::class, 'createRole'])->name('roles.create');
    Route::delete('/roles/{role}', [HrAdminController::class, 'deleteRole'])->name('roles.delete');

    Route::post('/permissions', [HrAdminController::class, 'createPermission'])->name('permissions.create');
    Route::delete('/permissions/{permission}', [HrAdminController::class, 'deletePermission'])->name('permissions.delete');

    Route::post('/users/{user}/roles/grant', [HrAdminController::class, 'grantRole'])->name('roles.grant');
    Route::post('/users/{user}/roles/revoke', [HrAdminController::class, 'revokeRole'])->name('roles.revoke');

    Route::post('/users/{user}/permissions/grant', [HrAdminController::class, 'grantPermission'])->name('permissions.grant');
    Route::post('/users/{user}/permissions/revoke', [HrAdminController::class, 'revokePermission'])->name('permissions.revoke');
});
