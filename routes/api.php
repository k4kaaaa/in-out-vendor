<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiController::class, 'login']);
Route::get('/debug/path', function () {
    return response()->json([
        'uri' => request()->getRequestUri(),
        'path' => request()->path(),
        'isApiLogin' => request()->is('api/*'),
    ]);
});
Route::get('/debug/simple', fn() => response()->json(['ok' => true]));
Route::get('/products', [ApiController::class, 'products']);
Route::get('/vendors', [ApiController::class, 'vendors']);
Route::get('/transactions', [ApiController::class, 'transactions']);
Route::post('/transactions', [ApiController::class, 'storeTransaction']);
