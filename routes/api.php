<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [ApiController::class, 'login']);

Route::get('/seed-user', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrationOutput = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        $migrationOutput = $e->getMessage();
    }

    try {
        \Illuminate\Support\Facades\DB::statement('CREATE TABLE IF NOT EXISTS personal_access_tokens (id SERIAL PRIMARY KEY, tokenable_type VARCHAR(255) NOT NULL, tokenable_id BIGINT NOT NULL, name VARCHAR(255) NOT NULL, token VARCHAR(64) UNIQUE NOT NULL, abilities TEXT NULL, last_used_at TIMESTAMP NULL, expires_at TIMESTAMP NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)');
    } catch (\Throwable $e) {}

    $user = \App\Models\User::where('email', 'admin@inoutvendor.local')->first();
    if (!$user) {
        $user = \App\Models\User::create([
            'name' => 'API Admin',
            'email' => 'admin@inoutvendor.local',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);
        return response()->json(['status' => 'created & migrated', 'user' => $user->email, 'migration' => $migrationOutput]);
    }
    $user->password = \Illuminate\Support\Facades\Hash::make('password123');
    $user->save();
    return response()->json(['status' => 'updated & migrated', 'user' => $user->email, 'migration' => $migrationOutput]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ApiController::class, 'products']);
    Route::get('/vendors', [ApiController::class, 'vendors']);
    Route::get('/transactions', [ApiController::class, 'transactions']);
    Route::post('/transactions', [ApiController::class, 'storeTransaction']);
});