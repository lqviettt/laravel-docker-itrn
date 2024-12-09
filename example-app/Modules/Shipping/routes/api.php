<?php

use Illuminate\Support\Facades\Route;
use Modules\Shipping\Http\Controllers\PaymentController;
use Modules\Shipping\Http\Controllers\ShippingController;

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
//     Route::apiResource('shipping', ShippingController::class)->names('shipping');
// });

Route::resource('/shipping', ShippingController::class);
Route::post('/shipping-fee', [ShippingController::class, 'calculateFee']);
Route::post('/vnpay-payment', [PaymentController::class, 'create']);
