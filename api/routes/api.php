<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['web']], function () {
    Route::post('register', [AuthController::class, 'register'])
    ->middleware('throttle:6,1');
    Route::post('login', [AuthController::class, 'login'])
    ->middleware('throttle:6,1');
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout'])
    ->middleware('throttle:6,1');
});

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
Route::post   ('/cart/items', [CartController::class, 'store']);
Route::patch  ('/cart/items/{cartItem}', [CartController::class, 'update']);
Route::delete ('/cart/items/{cartItem}', [CartController::class, 'destroy']);
// ...


Route::middleware(['auth:sanctum'])
 ->group(function () {
    Route::patch('user', [AuthController::class, 'update'])
    ->middleware('throttle:6,1');
    Route::get    ('/addresses', [AddressController::class, 'index']);
    Route::post   ('/addresses', [AddressController::class, 'store']);
    Route::get    ('/addresses/{address}', [AddressController::class, 'show']);
    Route::patch  ('/addresses/{address}', [AddressController::class, 'update']);
    Route::delete ('/addresses/{address}', [AddressController::class, 'destroy']);
    
    Route::post   ('/orders', [OrderController::class, 'store']);
    Route::get    ('/orders', [OrderController::class, 'index']);
    Route::get    ('/orders/{order}', [OrderController::class, 'show']);
    Route::post  ('/orders/{order}/pay', [OrderController::class, 'pay']);
    Route::post ('/orders/{order}/cancel', [OrderController::class, 'cancel']);
});

Route::prefix('admin')
 ->middleware(['auth:sanctum'])
 ->group(function () {
        Route::apiResource('categories', CategoryController::class)
                ->whereNumber('category');
        Route::apiResource('products', ProductController::class)
                ->whereNumber('product');
        Route::get ('/orders', [OrderController::class, 'listAll']);
        Route::get    ('/orders/{order}', [OrderController::class, 'show']);
        Route::post  ('/orders/{order}/pay', [OrderController::class, 'pay']);
        Route::post ('/orders/{order}/cancel', [OrderController::class, 'cancel']);
        Route::patch ('/orders/{order}/status', [OrderController::class, 'update']);
});