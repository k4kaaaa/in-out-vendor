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

// TEMPORARY DIAGNOSTIC 2 — remove after confirmed
Route::get('/diagfix2/{token}', function ($token) {
    if ($token !== 'X7k9p2mQz4v') {
        abort(404);
    }
    $out = [];

    // Step 0b: NOT NULL columns without default on products / stock_movements
    foreach (['products', 'stock_movements', 'transactions'] as $t) {
        try {
            $cols = DB::select("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name = ? ORDER BY ordinal_position", [$t]);
            $out['columns'][$t] = array_map(fn($c) => $c->column_name . ' ' . $c->data_type . ($c->is_nullable === 'NO' ? ' NOT NULL' : '') . ($c->column_default ? ' default ' . $c->column_default : ''), $cols);
        } catch (\Throwable $e) {
            $out['columns'][$t] = $e->getMessage();
        }
    }

    // Step 1: fresh-connection sanity
    try {
        DB::selectOne('SELECT 1');
        $out['select1'] = 'OK';
    } catch (\Throwable $e) {
        $out['select1'] = get_class($e) . ': ' . $e->getMessage();
    }

    // Step 2: full controller flow inside one transaction (rolled back)
    try {
        DB::transaction(function () use ($out) {
            $name = DB::table('products')->where('id', 1)->lockForUpdate()->value('name');
            $out['step1_lock'] = $name;

            $tid = DB::table('transactions')->insertGetId([
                'date' => '2026-11-27 00:00:00',
                'type' => 'in',
                'product_id' => 1,
                'vendor_id' => null,
                'qty' => 1,
                'customer_name' => 'diag2',
                'receiver_name' => 'diag2',
                'photo_product' => null,
                'photo_delivery' => null,
            ]);
            $out['step2_txn'] = 'id=' . $tid;

            DB::table('products')->where('id', 1)->update(['current_stock' => 0]);
            $out['step3_update'] = 'OK';

            DB::table('stock_movements')->insertGetId([
                'product_id' => 1,
                'transaction_id' => $tid,
                'type' => 'in',
                'qty_before' => 0,
                'qty_after' => 0,
                'date' => now(),
            ]);
            $out['step4_sm'] = 'OK';
        });
        $out['flow'] = 'OK (rolled back)';
    } catch (\Throwable $e) {
        $out['flow'] = get_class($e) . ': ' . $e->getMessage();
    }

    // Step 3: simulate polluted connection (aborted txn state on same connection)
    try {
        DB::beginTransaction();
        DB::statement('INSERT INTO transactions (nonexistent_column_xyz) VALUES (1)');
    } catch (\Throwable $e) {
        $out['pollute'] = get_class($e) . ': ' . $e->getMessage();
    }
    try {
        DB::selectOne('SELECT 1');
        $out['polluted_select1'] = 'OK (connection clean)';
    } catch (\Throwable $e) {
        $out['polluted_select1'] = get_class($e) . ': ' . $e->getMessage();
    }
    // Now fix it like the controller retry does
    try { DB::rollBack(); } catch (\Throwable) {}
    DB::purge();
    try {
        DB::selectOne('SELECT 1');
        $out['after_purge_select1'] = 'OK (clean after purge)';
    } catch (\Throwable $e) {
        $out['after_purge_select1'] = get_class($e) . ': ' . $e->getMessage();
    }

    // Cleanup: delete diag transaction row; stock_movements cascades via FK
    try {
        DB::table('transactions')->where('customer_name', 'diag2')->delete();
    } catch (\Throwable) {}

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
