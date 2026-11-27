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
    DB::purge(); // Ensure fresh connection
    try {
        $trx = DB::transaction(function () use (&$out) {
            $qty = 3.0;
            $product = \App\Models\Product::find(1);
            if (!$product) {
                throw new \RuntimeException('product not found');
            }
            $qtyBefore = (float) $product->current_stock;
            DB::table('products')->where('id', $product->id)->increment('current_stock', $qty);
            $product->refresh();
            $qtyAfter = (float) $product->current_stock;
            $t = \App\Models\Transaction::create([
                'date' => now()->format('Y-m-d'),
                'type' => 'in',
                'product_id' => $product->id,
                'vendor_id' => null,
                'qty' => $qty,
                'customer_name' => 'verifyfix',
                'receiver_name' => 'verifyfix',
            ]);
            \App\Models\StockMovement::create([
                'product_id' => $product->id,
                'transaction_id' => $t->id,
                'type' => 'in',
                'qty_before' => $qtyBefore,
                'qty_after' => $qtyAfter,
                'date' => now(),
            ]);
            $out['stock'] = $qtyBefore . '->' . $qtyAfter;
            return $t;
        });
        $out['store_flow'] = 'OK trx_id=' . $trx->id;
        // cleanup: reverse stock + delete rows
        DB::transaction(function () use ($trx) {
            DB::table('products')->where('id', $trx->product_id)->decrement('current_stock', (float) $trx->qty);
            \App\Models\StockMovement::where('transaction_id', $trx->id)->delete();
            $trx->delete();
        });
        $out['cleanup'] = 'OK';
    } catch (\Throwable $e) {
        $out['store_flow'] = get_class($e) . ': ' . $e->getMessage();
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
