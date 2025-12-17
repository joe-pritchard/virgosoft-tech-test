<?php
declare(strict_types=1);

use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\OrderController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (Router $route) {
    $route->get('/profile', ProfileController::class);
    $route->apiResource('orders', OrderController::class)->only(['index', 'store']);
    $route->post('orders/{order}/cancel', [OrderController::class, 'cancel']);
});
