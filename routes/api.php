<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [ApiController::class, 'login']);

Route::get('/seed-user', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    } catch (\Throwable $e) {}

    $user = \App\Models\User::where('email', 'admin@inoutvendor.local')->first();
    if (!$user) {
        $user = \App\Models\User::create([
            'name' => 'API Admin',
            'email' => 'admin@inoutvendor.local',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);
        return response()->json(['status' => 'created & migrated', 'user' => $user->email]);
    }
    $user->password = \Illuminate\Support\Facades\Hash::make('password123');
    $user->save();
    return response()->json(['status' => 'updated & migrated', 'user' => $user->email]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ApiController::class, 'products']);
    Route::get('/vendors', [ApiController::class, 'vendors']);
    Route::get('/transactions', [ApiController::class, 'transactions']);
    Route::post('/transactions', [ApiController::class, 'storeTransaction']);
});