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

// TEMPORARY DIAGNOSTIC 3 — remove after confirmed
Route::get('/diagfix2/{token}', function ($token) {
    if ($token !== 'X7k9p2mQz4v') {
        abort(404);
    }
    $out = [];

    // A: who is stuck (sessions / locks on products row id=1)
    try {
        $acts = DB::select('SELECT pid, state, now() - xact_start AS xact_age, wait_event_type, wait_event, left(query, 100) AS query FROM pg_stat_activity WHERE datname = current_database() AND pid <> pg_backend_pid()');
        $out['sessions'] = array_map(fn($a) => $a->pid . ' | ' . $a->state . ' | age=' . $a->xact_age . ' | wait=' . $a->wait_event_type . '/' . $a->wait_event . ' | ' . $a->query, $acts);
    } catch (\Throwable $e) {
        $out['sessions'] = $e->getMessage();
    }

    // B: terminate stuck idle-in-transaction sessions (they hold FOR UPDATE locks)
    try {
        $term = DB::select("SELECT pid FROM pg_stat_activity WHERE datname = current_database() AND pid <> pg_backend_pid() AND state IN ('idle in transaction', 'idle in transaction (aborted)')");
        $out['terminated_pids'] = array_map(function ($t) {
            $r = DB::selectOne('SELECT pg_terminate_backend(?) AS ok', [$t->pid]);
            return $t->pid . '=' . ($r->ok ? 'terminated' : 'busy');
        }, $term);
    } catch (\Throwable $e) {
        $out['terminated_pids'] = 'ERROR: ' . $e->getMessage();
    }

    // C: exact Eloquent SELECT outside any transaction (autocommit)
    try {
        $r = DB::select('SELECT * FROM products WHERE id = 1 LIMIT 1 FOR UPDATE');
        $out['select_for_update'] = 'OK: ' . ($r[0]->name ?? '?');
    } catch (\Throwable $e) {
        try { DB::rollBack(); } catch (\Throwable) {}
        $out['select_for_update'] = get_class($e) . ': ' . $e->getMessage();
    }

    // D: full flow inside transaction, capturing each step (by-ref array)
    try {
        $out['flow'] = DB::transaction(function () use (&$out) {
            $steps = [];
            $name = DB::table('products')->where('id', 1)->lockForUpdate()->value('name');
            $steps[] = 'lock: ' . var_export($name, true);

            $tid = DB::table('transactions')->insertGetId([
                'date' => '2026-11-27 00:00:00',
                'type' => 'in',
                'product_id' => 1,
                'vendor_id' => null,
                'qty' => 1,
                'customer_name' => 'diag3',
                'receiver_name' => 'diag3',
                'photo_product' => null,
                'photo_delivery' => null,
            ]);
            $steps[] = 'txn: id=' . $tid;

            DB::table('products')->where('id', 1)->update(['current_stock' => 0]);
            $steps[] = 'product update: OK';

            DB::table('stock_movements')->insertGetId([
                'product_id' => 1,
                'transaction_id' => $tid,
                'type' => 'in',
                'qty_before' => 0,
                'qty_after' => 0,
                'date' => now(),
            ]);
            $steps[] = 'sm: OK';

            return implode(' | ', $steps);
        });
    } catch (\Throwable $e) {
        $out['flow'] = 'FAIL: ' . get_class($e) . ': ' . $e->getMessage();
    }

    // Cleanup
    try {
        DB::table('transactions')->where('customer_name', 'diag3')->delete();
        $out['cleanup'] = 'OK';
    } catch (\Throwable $e) {
        $out['cleanup'] = $e->getMessage();
    }

    return response()->json($out);
});

// TEMPORARY DIAGNOSTIC 4 — isolate abort cause
Route::get('/diagfix4/{token}', function ($token) {
    if ($token !== 'X7k9p2mQz4v') {
        abort(404);
    }
    $out = [];
    DB::purge();

    // 1) Autocommit FOR UPDATE
    try {
        $r = DB::select('SELECT name FROM products WHERE id = 1 FOR UPDATE');
        $out['autocommit_for_update'] = 'OK: ' . ($r[0]->name ?? '?');
    } catch (\Throwable $e) {
        $out['autocommit_for_update'] = get_class($e) . ': ' . $e->getMessage();
    }

    DB::purge();

    // 2) Explicit Txn: just BEGIN + SELECT without FOR UPDATE
    try {
        DB::beginTransaction();
        $r = DB::select('SELECT name FROM products WHERE id = 1');
        DB::commit();
        $out['txn_select_no_lock'] = 'OK: ' . ($r[0]->name ?? '?');
    } catch (\Throwable $e) {
        try { DB::rollBack(); } catch (\Throwable) {}
        $out['txn_select_no_lock'] = get_class($e) . ': ' . $e->getMessage();
    }

    DB::purge();

    // 3) Explicit Txn: BEGIN + SELECT WITH FOR UPDATE
    try {
        DB::beginTransaction();
        $r = DB::select('SELECT name FROM products WHERE id = 1 FOR UPDATE');
        DB::commit();
        $out['txn_select_with_lock'] = 'OK: ' . ($r[0]->name ?? '?');
    } catch (\Throwable $e) {
        try { DB::rollBack(); } catch (\Throwable) {}
        $out['txn_select_with_lock'] = get_class($e) . ': ' . $e->getMessage();
    }

    DB::purge();

    // 4) Explicit Txn: BEGIN + INSERT transaction without lock
    try {
        DB::beginTransaction();
        $tid = DB::table('transactions')->insertGetId([
            'date' => now(),
            'type' => 'in',
            'product_id' => 1,
            'qty' => 1,
            'customer_name' => 'diag4',
            'receiver_name' => 'diag4',
        ]);
        DB::commit();
        $out['txn_insert_only'] = 'OK: id=' . $tid;
        DB::table('transactions')->where('customer_name', 'diag4')->delete();
    } catch (\Throwable $e) {
        try { DB::rollBack(); } catch (\Throwable) {}
        $out['txn_insert_only'] = get_class($e) . ': ' . $e->getMessage();
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
