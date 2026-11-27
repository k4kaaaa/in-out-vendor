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

    $t = function (string $label, callable $fn) use (&$out) {
        DB::purge();
        try {
            $r = $fn();
            $out[$label] = 'OK' . ($r !== null ? ': ' . (is_string($r) ? $r : json_encode($r)) : '');
        } catch (\Throwable $e) {
            try { DB::rollBack(); } catch (\Throwable) {}
            $out[$label] = get_class($e) . ': ' . substr($e->getMessage(), 0, 220);
        }
        return $out[$label];
    };

    $t('A_autocommit_select_products', fn() => DB::selectOne('select * from products where id = 1 limit 1'));

    $t('B_txn_select_products', function () {
        DB::beginTransaction();
        $r = DB::selectOne('select * from products where id = 1 limit 1');
        DB::commit();
        return $r;
    });

    $t('C_txn_select1_then_products', function () {
        DB::beginTransaction();
        DB::selectOne('SELECT 1');
        $r = DB::selectOne('select * from products where id = 1 limit 1');
        DB::commit();
        return $r;
    });

    $t('D_txn_update_products', function () {
        DB::beginTransaction();
        $r = DB::statement('update products set current_stock = current_stock + 0 where id = 1');
        DB::commit();
        return $r;
    });

    $t('E_txn_increment', function () {
        DB::beginTransaction();
        $r = DB::table('products')->where('id', 1)->increment('current_stock', 0);
        DB::commit();
        return $r;
    });

    $t('F_eloquent_find_no_txn', fn() => \App\Models\Product::find(1));

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
