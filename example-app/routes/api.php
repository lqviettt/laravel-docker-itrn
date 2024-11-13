<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\CategoryController;
use Modules\Employee\Http\Controllers\EmployeeController;
use Modules\Employee\Http\Controllers\PermissionController;
use Modules\Order\Http\Controllers\OrderController;
use Modules\Product\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify', [AuthController::class, 'verify']);
});

Route::group([
    'middleware' => 'auth:api'
], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::resource('/product', ProductController::class);
    Route::resource('/order', OrderController::class);
    Route::resource('/category', CategoryController::class);
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('/employee', EmployeeController::class);
    Route::get('/permission', [PermissionController::class, 'getPermission']);
    Route::get('/employee/{employee}/permission', [PermissionController::class, 'showPermissions']);
    Route::post('/employee/{employee}/permission', [PermissionController::class, 'editPermission']);
    Route::delete('/employee/{employee}/permission', [PermissionController::class, 'removePermission']);
});
