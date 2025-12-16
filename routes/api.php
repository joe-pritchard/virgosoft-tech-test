<?php
declare(strict_types=1);

use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', ProfileController::class);
    Route::apiResource('order', OrderController::class)->only(['store', 'destroy']);
});
