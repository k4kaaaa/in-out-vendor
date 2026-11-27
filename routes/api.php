<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [ApiController::class, 'login']);

Route::get('/seed-user', function() {
    try {
        \Illuminate\Support\Facades\DB::statement('CREATE TABLE IF NOT EXISTS users (id SERIAL PRIMARY KEY, name VARCHAR(255) NOT NULL, email VARCHAR(255) UNIQUE NOT NULL, email_verified_at TIMESTAMP NULL, password VARCHAR(255) NOT NULL, role VARCHAR(50) DEFAULT \'admin\', remember_token VARCHAR(100) NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)');
    } catch (\Throwable $e) {}

    try {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(50) DEFAULT \'admin\'');
    } catch (\Throwable $e) {}

    try {
        \Illuminate\Support\Facades\DB::statement('CREATE TABLE IF NOT EXISTS personal_access_tokens (id SERIAL PRIMARY KEY, tokenable_type VARCHAR(255) NOT NULL, tokenable_id BIGINT NOT NULL, name VARCHAR(255) NOT NULL, token VARCHAR(64) UNIQUE NOT NULL, abilities TEXT NULL, last_used_at TIMESTAMP NULL, expires_at TIMESTAMP NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)');
    } catch (\Throwable $e) {}

    try {
        \Illuminate\Support\Facades\DB::statement('CREATE TABLE IF NOT EXISTS products (id SERIAL PRIMARY KEY, name VARCHAR(255) NOT NULL, category VARCHAR(100) NULL, unit VARCHAR(50) NULL, min_stock INT DEFAULT 0, stock INT DEFAULT 0, price DECIMAL(15,2) DEFAULT 0, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)');
    } catch (\Throwable $e) {}

    try {
        \Illuminate\Support\Facades\DB::statement('CREATE TABLE IF NOT EXISTS vendors (id SERIAL PRIMARY KEY, name VARCHAR(255) NOT NULL, phone VARCHAR(50) NULL, address TEXT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)');
    } catch (\Throwable $e) {}

    try {
        \Illuminate\Support\Facades\DB::statement('CREATE TABLE IF NOT EXISTS transactions (id SERIAL PRIMARY KEY, product_id BIGINT NOT NULL, vendor_id BIGINT NULL, type VARCHAR(50) NOT NULL, quantity INT NOT NULL, user_id BIGINT NOT NULL, notes TEXT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)');
    } catch (\Throwable $e) {}

    return response()->json(['status' => 'all tables ensured']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ApiController::class, 'products']);
    Route::get('/vendors', [ApiController::class, 'vendors']);
    Route::get('/transactions', [ApiController::class, 'transactions']);
    Route::post('/transactions', [ApiController::class, 'storeTransaction']);
});