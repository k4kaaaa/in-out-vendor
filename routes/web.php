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

    // Triggers on products / stock_movements
    try {
        $tr = DB::select("SELECT relname, tgname FROM pg_trigger JOIN pg_class ON relnamespace=(SELECT oid FROM pg_namespace WHERE nspname='public') AND relname IN ('products','stock_movements','transactions') WHERE NOT tgisinternal AND oid = tgrelid");
        $out['triggers'] = array_map(fn($t) => $t->relname . ':' . $t->tgname, $tr);
    } catch (\Throwable $e) {
        $out['triggers'] = $e->getMessage();
    }

    $out['steps'] = [];
    try {
        DB::beginTransaction();
        try { DB::selectOne('SELECT 1'); $out['steps'][] = 's0_select1 OK'; }
        catch (\Throwable $e) { $out['steps'][] = 's0_select1 FAIL: ' . $e->getMessage(); }

        try { $p = \App\Models\Product::find(1); $out['steps'][] = 's1_find OK stock=' . $p->current_stock; }
        catch (\Throwable $e) { $out['steps'][] = 's1_find FAIL: ' . $e->getMessage(); }

        try { DB::table('products')->where('id', 1)->increment('current_stock', 3); $out['steps'][] = 's2_increment OK'; }
        catch (\Throwable $e) { $out['steps'][] = 's2_increment FAIL: ' . $e->getMessage(); }

        try { $t = \App\Models\Transaction::create(['date' => now()->format('Y-m-d'), 'type' => 'in', 'product_id' => 1, 'qty' => 3, 'customer_name' => 'vf', 'receiver_name' => 'vf']); $out['steps'][] = 's3_txn OK id=' . $t->id; }
        catch (\Throwable $e) { $out['steps'][] = 's3_txn FAIL: ' . $e->getMessage(); }

        try { \App\Models\StockMovement::create(['product_id' => 1, 'transaction_id' => $t->id ?? 0, 'type' => 'in', 'qty_before' => 0, 'qty_after' => 3, 'date' => now()]); $out['steps'][] = 's4_sm OK'; }
        catch (\Throwable $e) { $out['steps'][] = 's4_sm FAIL: ' . $e->getMessage(); }

        try { DB::commit(); $out['steps'][] = 'commit OK'; }
        catch (\Throwable $e) { $out['steps'][] = 'commit FAIL: ' . $e->getMessage(); }
    } catch (\Throwable $e) {
        $out['begin_FAIL'] = $e->getMessage();
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
