<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/api/auth/login', [ApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ApiController::class, 'products']);
    Route::get('/vendors', [ApiController::class, 'vendors']);
    Route::get('/transactions', [ApiController::class, 'transactions']);
    Route::post('/transactions', [ApiController::class, 'storeTransaction']);
});