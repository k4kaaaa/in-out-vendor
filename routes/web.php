<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\DB;

Route::get('/', function() {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// TEMPORARY SCHEMA FIX + VERIFY — unauthenticated, token-guarded; remove after confirmed
Route::get('/diagfix/{token}', function ($token) {
    if ($token !== 'X7k9p2mQz4v') {
        abort(404);
    }
    $out = [];

    try {
        $cols = DB::select("SELECT column_name, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'transactions' AND column_name IN ('quantity','qty')");
        $out['before'] = array_map(fn($c) => $c->column_name . ' nullable=' . $c->is_nullable . ' default=' . $c->column_default, $cols);
    } catch (\Throwable $e) {
        $out['before'] = $e->getMessage();
    }

    try {
        DB::statement('ALTER TABLE transactions ALTER COLUMN quantity DROP NOT NULL');
        DB::statement('ALTER TABLE transactions ALTER COLUMN quantity SET DEFAULT 0');
        $out['alter'] = 'OK';
    } catch (\Throwable $e) {
        $out['alter'] = get_class($e) . ': ' . $e->getMessage();
    }

    try {
        $id = DB::table('transactions')->insertGetId([
            'type' => 'in',
            'product_id' => 1,
            'qty' => 1,
            'date' => '2026-11-27 00:00:00',
        ]);
        DB::table('transactions')->where('id', $id)->delete();
        $out['insert_test'] = 'OK (id=' . $id . ')';
    } catch (\Throwable $e) {
        $out['insert_test'] = get_class($e) . ': ' . $e->getMessage();
    }

    return response()->json($out);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', function() {
        if (auth()->user()->role === 'staff') {
            return app(StaffDashboardController::class)->index();
        }
        return app(DashboardController::class)->index();
    })->name('dashboard');

    Route::get('/dashboard', function() {
        if (auth()->user()->role === 'staff') {
            return app(StaffDashboardController::class)->index();
        }
        return app(DashboardController::class)->index();
    });
    
    Route::resource('products', ProductController::class);
    Route::resource('vendors', VendorController::class);
    Route::resource('transactions', TransactionController::class);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/download', [ReportController::class, 'download'])->name('reports.download');
});
