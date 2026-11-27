<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [ApiController::class, 'login']);

// Optional browser-friendly GET fallback (web-only). Curl/Android should use POST.
Route::get('/auth/login', function() {
    return response()->json([
        'success' => false,
        'message' => 'Gunakan metode POST. Body JSON: {"email":"...","password":"..."}',
        'method' => 'POST only',
    ], 405);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ApiController::class, 'products']);
    Route::get('/vendors', [ApiController::class, 'vendors']);
    Route::get('/transactions', [ApiController::class, 'transactions']);
    Route::post('/transactions', [ApiController::class, 'storeTransaction']);
});