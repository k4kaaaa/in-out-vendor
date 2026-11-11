<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiController::class, 'login']);
Route::get('/debug/path', [DebugController::class, 'path']);
Route::get('/debug/simple', [DebugController::class, 'simple']);
Route::get('/products', [ApiController::class, 'products']);
Route::get('/vendors', [ApiController::class, 'vendors']);
Route::get('/transactions', [ApiController::class, 'transactions']);
Route::post('/transactions', [ApiController::class, 'storeTransaction']);
