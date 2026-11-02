<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

/*
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:6,1');
*/

// ************************** /
// ROTAS PÚBLICAS
// ************************** /

Route::apiResource('/categories', CategoryController::class, [
    'only' => ['index', 'show']
])->whereNumber('category');

Route::apiResource('/products', ProductController::class, [
    'only' => ['index', 'show']
])->whereNumber('product');

Route::apiResource('/cart', CartController::class, [
    'only' => 'index'
]);
Route::post   ('/cart/items',            [CartController::class, 'store']);
// ...

/*
Route::middleware(['auth:sanctum'])
 ->group(function () {
        // ... cart GET

});

Route::prefix('admin')
 ->middleware(['auth:sanctum', 'admin'])
 ->group(function () {
        Route::apiResource('categories', CategoryController::class)
                ->whereNumber('category');
        Route::apiResource('products', ProductController::class)
                ->whereNumber('product');
        // ...
});
*/