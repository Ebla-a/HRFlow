<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\AI\Http\Controllers\AIController;

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

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
Route::post('/chat', [AIController::class, 'ask']);
    Route::get('/conversations', [AIController::class, 'index']);
    Route::get('/conversations/{id}', [AIController::class, 'show']);
    });
