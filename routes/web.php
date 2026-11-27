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

    $w = function (string $label, int $n, callable $fn) use (&$out) {
        $ok = 0; $fail = 0; $err = null;
        for ($i = 0; $i < $n; $i++) {
            DB::purge();
            try { DB::rollBack(); } catch (\Throwable) {}
            try {
                $fn();
                $ok++;
            } catch (\Throwable $e) {
                try { DB::rollBack(); } catch (\Throwable) {}
                $fail++;
                $err = substr($e->getMessage(), 0, 90);
            }
        }
        $out[$label] = "ok=$ok fail=$fail" . ($err ? " err=$err" : '');
    };

    // 2 writes inside one txn
    $w('W1_2writes', 5, function () {
        DB::beginTransaction();
        $a = DB::table('transactions')->insertGetId(['date' => now()->format('Y-m-d'), 'type' => 'in', 'product_id' => 1, 'qty' => 1, 'customer_name' => 'w1', 'receiver_name' => 'w1']);
        DB::table('stock_movements')->insertGetId(['product_id' => 1, 'transaction_id' => $a, 'type' => 'in', 'qty_before' => 0, 'qty_after' => 1, 'date' => now()]);
        DB::commit();
        DB::table('stock_movements')->where('product_id', 1)->where('type', 'in')->where('qty_after', 1)->orderByDesc('id')->limit(1)->delete();
        DB::table('transactions')->where('customer_name', 'w1')->delete();
    });

    // 2 autocommit writes (no explicit txn)
    $w('W2_2autocommit', 5, function () {
        DB::purge();
        $a = DB::table('transactions')->insertGetId(['date' => now()->format('Y-m-d'), 'type' => 'in', 'product_id' => 1, 'qty' => 1, 'customer_name' => 'w2', 'receiver_name' => 'w2']);
        DB::table('stock_movements')->insertGetId(['product_id' => 1, 'transaction_id' => $a, 'type' => 'in', 'qty_before' => 0, 'qty_after' => 1, 'date' => now()]);
        DB::table('stock_movements')->where('product_id', 1)->where('type', 'in')->where('qty_after', 1)->orderByDesc('id')->limit(1)->delete();
        DB::table('transactions')->where('customer_name', 'w2')->delete();
    });

    // full store flow as autocommit: read, stock update, txn, sm
    $w('W3_store_autocommit', 5, function () {
        DB::purge();
        $p = DB::selectOne('select id, current_stock from products where id = 1');
        DB::statement('update products set current_stock = current_stock + 1 where id = 1');
        $a = DB::table('transactions')->insertGetId(['date' => now()->format('Y-m-d'), 'type' => 'in', 'product_id' => 1, 'qty' => 1, 'customer_name' => 'w3', 'receiver_name' => 'w3']);
        DB::table('stock_movements')->insertGetId(['product_id' => 1, 'transaction_id' => $a, 'type' => 'in', 'qty_before' => $p->current_stock, 'qty_after' => $p->current_stock + 1, 'date' => now()]);
        // cleanup
        DB::statement('update products set current_stock = current_stock - 1 where id = 1');
        DB::table('stock_movements')->where('product_id', 1)->where('type', 'in')->orderByDesc('id')->limit(1)->delete();
        DB::table('transactions')->where('customer_name', 'w3')->delete();
    });

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
