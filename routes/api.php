<?php
declare(strict_types=1);

use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (Route $route) {
    $route->get('/profile', ProfileController::class);
    $route->apiResource('order', OrderController::class)->only(['index', 'store']);
    $route->post('order/{order}/cancel', [OrderController::class, 'cancel']);

});
