<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiController::class, 'login']);
Route::get('/test', function() {
    return response()->json(['message' => 'API is working']);
});
Route::get('/products', [ApiController::class, 'products']);
Route::get('/vendors', [ApiController::class, 'vendors']);
Route::get('/transactions', [ApiController::class, 'transactions']);
Route::post('/transactions', [ApiController::class, 'storeTransaction']);
