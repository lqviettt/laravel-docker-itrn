<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\EmployeeController;
use Modules\Employee\Http\Controllers\PermissionController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('/employee', EmployeeController::class);
    Route::get('/permission', [PermissionController::class, 'getPermission']);
    Route::get('/employee/{employee}/permission', [PermissionController::class, 'showPermissions']);
    Route::post('/employee/{employee}/permission', [PermissionController::class, 'editPermission']);
    Route::delete('/employee/{employee}/permission', [PermissionController::class, 'removePermission']);
});
