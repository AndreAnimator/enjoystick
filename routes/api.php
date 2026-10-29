<?php

// use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:6,1');
*/

//Route::apiResources(['products', ProductController::class, 'categories', CategoryController::class], ['only' => ['index', 'show']]);


Route::apiResource('/categories', CategoryController::class, [
    'only' => ['index', 'show']
])->whereNumber('category');
Route::apiResource('/products', ProductController::class, [
    'only' => ['index', 'show']
])->whereNumber('product');
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