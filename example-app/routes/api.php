<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;

use Illuminate\Support\Facades\Route;



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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::resource('/product', ProductController::class);
Route::resource('/category', CategoryController::class);
Route::resource('/order', OrderController::class);


// Route::get('/category/{id}/product', function ($id) {
//     $products = Product::where('category_id', $id)->get();
//     return response()->json($products);
// });

// Route::post('/product', [ProductController::class, 'store']);
// Route::get('/product', [ProductController::class, 'show']);
// Route::put('/product/{product}', [ProductController::class, 'update']);
// Route::delete('/product/{product}', [ProductController::class, 'destroy']);
// Route::get('/product/{product}', [ProductController::class, 'getById']);
