<?php

use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas para productos
Route::apiResource('products', ProductController::class);

// Rutas para órdenes
Route::post('buy', [OrderController::class, 'store']);
Route::get('buy/{id}', [OrderController::class, 'show']);
Route::get('buy', [OrderController::class, 'index']);
Route::put('buy/{id}/state', [OrderController::class, 'updateState']);
Route::put('buy/{id}/payment', [OrderController::class, 'updatePayment']);
Route::get('user/{userId}/orders', [OrderController::class, 'getByUser']);
