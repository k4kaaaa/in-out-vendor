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

Route::get('/debug-dash', function () {
    return app(DashboardController::class)->index();
});

Route::get('/schema-fix', function () {
    $db = \Illuminate\Support\Facades\DB::connection();
    $stmts = [
        // products
        "alter table products add column if not exists current_stock numeric(15,2) default 0",
        "alter table products add column if not exists category varchar",
        "alter table products add column if not exists sku varchar",
        "alter table products add column if not exists unit varchar default 'pcs'",
        "alter table products add column if not exists min_stock numeric(15,2) default 5",
        "alter table products add column if not exists image varchar",
        "alter table products add column if not exists created_at timestamptz",
        "alter table products add column if not exists updated_at timestamptz",
        // vendors
        "alter table vendors add column if not exists name varchar",
        "alter table vendors add column if not exists address text",
        "alter table vendors add column if not exists contact varchar",
        "alter table vendors add column if not exists created_at timestamptz",
        "alter table vendors add column if not exists updated_at timestamptz",
        // transactions
        "alter table transactions add column if not exists date timestamptz",
        "alter table transactions add column if not exists type varchar default 'in'",
        "alter table transactions add column if not exists product_id bigint",
        "alter table transactions add column if not exists vendor_id bigint",
        "alter table transactions add column if not exists qty numeric(15,2)",
        "alter table transactions add column if not exists customer_name varchar",
        "alter table transactions add column if not exists receiver_name varchar",
        "alter table transactions add column if not exists photo_product varchar",
        "alter table transactions add column if not exists photo_delivery varchar",
        "alter table transactions add column if not exists created_at timestamptz",
        "alter table transactions add column if not exists updated_at timestamptz",
        // stock_movements
        "alter table stock_movements add column if not exists product_id bigint",
        "alter table stock_movements add column if not exists transaction_id bigint",
        "alter table stock_movements add column if not exists type varchar default 'in'",
        "alter table stock_movements add column if not exists qty_before numeric(15,2)",
        "alter table stock_movements add column if not exists qty_after numeric(15,2)",
        "alter table stock_movements add column if not exists date timestamptz",
        "alter table stock_movements add column if not exists created_at timestamptz",
        "alter table stock_movements add column if not exists updated_at timestamptz",
        // users
        "alter table users add column if not exists role varchar default 'admin'",
        "alter table users add column if not exists name varchar",
        "alter table users add column if not exists email_verified_at timestamptz",
    ];
    $actions = [];
    foreach ($stmts as $s) {
        try {
            $db->statement($s);
            $actions[] = 'ok: ' . $s;
        } catch (\Throwable $e) {
            $actions[] = 'ERR: ' . $s . ' :: ' . $e->getMessage();
        }
    }
    $cols = [];
    foreach (['products', 'vendors', 'transactions', 'stock_movements', 'users'] as $t) {
        $cols[$t] = $db->select(
            'select column_name, data_type from information_schema.columns where table_name = ? order by ordinal_position',
            [$t]
        );
    }
    return response()->json(['actions' => $actions, 'columns' => $cols]);
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
});
