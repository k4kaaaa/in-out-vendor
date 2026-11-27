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

// TEMP verify fixed store() flow — remove after confirmed
Route::get('/verifyfix/{token}', function ($token) {
    if ($token !== 'X7k9p2mQz4v') {
        abort(404);
    }
    $out = [];

    // X: bare purge + BEGIN; SELECT 1; COMMIT  (reproduces poison)
    DB::purge();
    try {
        DB::beginTransaction();
        DB::selectOne('SELECT 1');
        DB::commit();
        $out['X_bare'] = 'OK';
    } catch (\Throwable $e) {
        try { DB::rollBack(); } catch (\Throwable) {}
        $out['X_bare'] = get_class($e) . ': ' . substr($e->getMessage(), 0, 150);
    }

    // Y: pre-ROLLBACK then purge + BEGIN; SELECT 1; COMMIT  (clears poison?)
    DB::purge();
    try { DB::rollBack(); } catch (\Throwable) {}
    try {
        DB::beginTransaction();
        DB::selectOne('SELECT 1');
        DB::commit();
        $out['Y_prerollback'] = 'OK';
    } catch (\Throwable $e) {
        try { DB::rollBack(); } catch (\Throwable) {}
        $out['Y_prerollback'] = get_class($e) . ': ' . substr($e->getMessage(), 0, 150);
    }

    // Z: loop 8x { purge; prerollback; begin; select1; select products; commit }
    $zok = 0; $zfail = 0; $zerr = null;
    for ($i = 0; $i < 8; $i++) {
        DB::purge();
        try { DB::rollBack(); } catch (\Throwable) {}
        try {
            DB::beginTransaction();
            DB::selectOne('SELECT 1');
            DB::selectOne('select id from products where id = 1 limit 1');
            DB::commit();
            $zok++;
        } catch (\Throwable $e) {
            try { DB::rollBack(); } catch (\Throwable) {}
            $zfail++;
            $zerr = substr($e->getMessage(), 0, 120);
        }
    }
    $out['Z_loop'] = "ok=$zok fail=$zfail last_err=" . ($zerr ?? 'none');

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
