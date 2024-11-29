<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\CategoryController;
use Modules\Product\Http\Controllers\ProductController;
use Modules\Product\Http\Controllers\ProductVariantController;
use Modules\Product\Http\Controllers\VariantOptionController;

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

// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
//     Route::apiResource('product', ProductController::class)->names('product');
// });

Route::group([
    'middleware' => 'auth:api'
], function () {
    Route::post('/product/{productId}/variant', [ProductVariantController::class, 'store']);
    Route::put('/product-variant/{pvariantId}', [ProductVariantController::class, 'update']);
    Route::get('/product-variant/{pvariantId}', [ProductVariantController::class, 'show']);
    Route::delete('/product-variant/{pvariantId}', [ProductVariantController::class, 'delete']);
});

Route::resource('/product', ProductController::class);
Route::resource('/category', CategoryController::class);
Route::resource('/variant', VariantOptionController::class);
Route::get('/product-variant', [ProductVariantController::class, 'index']);
