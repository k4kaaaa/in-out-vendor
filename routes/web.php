<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ReportController;

Route::get('/', function() {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// TEMPORARY DIAGNOSTIC — remove after Neon schema fixed
Route::get('/diag/{token}', function ($token) {
    if ($token !== 'X7k9p2mQz4v') {
        abort(404);
    }

    $report = ['connection' => \Illuminate\Support\Facades\DB::connection()->getName()];

    // 1) Real columns in Neon
    foreach (['transactions', 'products', 'stock_movements', 'vendors', 'users'] as $table) {
        try {
            $cols = \Illuminate\Support\Facades\DB::select(
                "SELECT column_name, data_type, is_nullable, column_default
                 FROM information_schema.columns
                 WHERE table_name = ?
                 ORDER BY ordinal_position",
                [$table]
            );
            $report['columns'][$table] = array_map(function ($c) {
                $s = $c->column_name . ' ' . $c->data_type;
                if ($c->is_nullable === 'NO') $s .= ' NOT NULL';
                if ($c->column_default !== null) $s .= ' DEFAULT ' . $c->column_default;
                return $s;
            }, $cols);
        } catch (\Throwable $e) {
            $report['columns'][$table] = 'ERROR: ' . $e->getMessage();
        }
    }

    // 2) Plain SELECT on fresh state
    try {
        \Illuminate\Support\Facades\DB::selectOne('SELECT 1 AS ok');
        $report['select1'] = 'OK';
    } catch (\Throwable $e) {
        $report['select1'] = get_class($e) . ': ' . $e->getMessage();
    }

    // 3) Test the exact INSERT (rolled back) to surface the real PG error
    try {
        \Illuminate\Support\Facades\DB::beginTransaction();
        \Illuminate\Support\Facades\DB::table('transactions')->insert([
            'date' => '2026-11-27 00:00:00',
            'type' => 'in',
            'product_id' => 1,
            'vendor_id' => null,
            'qty' => 1,
            'customer_name' => 'diag',
            'receiver_name' => 'diag',
            'photo_product' => null,
            'photo_delivery' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \Illuminate\Support\Facades\DB::rollBack();
        $report['insert_test'] = 'OK (rolled back)';
    } catch (\Throwable $e) {
        try { \Illuminate\Support\Facades\DB::rollBack(); } catch (\Throwable) {}
        $report['insert_test'] = get_class($e) . ': ' . $e->getMessage();
        if ($e->getPrevious()) {
            $report['insert_prev'] = get_class($e->getPrevious()) . ': ' . $e->getPrevious()->getMessage();
        }
    }

    // 4) Repeat INSERT on same connection (catches aborted-state pooler issue)
    try {
        \Illuminate\Support\Facades\DB::table('transactions')->insert([
            'date' => '2026-11-27 00:00:00',
            'type' => 'in',
            'product_id' => 1,
            'vendor_id' => null,
            'qty' => 1,
            'customer_name' => 'diag2',
            'receiver_name' => 'diag2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // cleanup the real row we just inserted
        \Illuminate\Support\Facades\DB::table('transactions')->where('customer_name', 'diag2')->delete();
        $report['insert_autocommit'] = 'OK (row cleaned up)';
    } catch (\Throwable $e) {
        $report['insert_autocommit'] = get_class($e) . ': ' . $e->getMessage();
    }

    return response()
        ->json($report, 200)
        ->header('Content-Type', 'application/json');
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
