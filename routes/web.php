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

// TEMP — invoke real store() to verify fix — remove after confirmed
Route::get('/realtest/{token}', function ($token) {
    if ($token !== 'X7k9p2mQz4v') {
        abort(404);
    }
    $out = [];
    try {
        $request = \Illuminate\Http\Request::create('/transactions', 'POST', [
            'date' => now()->format('Y-m-d'),
            'type' => 'in',
            'product_mode' => 'existing',
            'product_id' => '1',
            'qty' => '2',
            'customer_name' => 'realtest',
            'receiver_name' => 'realtest',
            '_token' => 'x',
        ]);
        $request->session()->put('_token', 'x');
        $ctrl = new \App\Http\Controllers\TransactionController;
        $res = $ctrl->store($request);
        $out['store'] = 'returned ' . get_class($res);
        $out['flash'] = $res->getSession()->get('success') ?? $res->getSession()->get('error') ?? 'none';
    } catch (\Throwable $e) {
        $out['store'] = get_class($e) . ': ' . substr($e->getMessage(), 0, 200);
    }
    // verify row
    try {
        $row = \App\Models\Transaction::where('customer_name', 'realtest')->latest('id')->first();
        $out['row'] = $row ? ('id=' . $row->id . ' qty=' . $row->qty . ' type=' . $row->type) : 'NOT FOUND';
        $sm = \App\Models\StockMovement::where('transaction_id', $row->id ?? -1)->first();
        $out['sm'] = $sm ? ('id=' . $sm->id . ' before=' . $sm->qty_before . ' after=' . $sm->qty_after) : 'no sm';
    } catch (\Throwable $e) {
        $out['row_check'] = $e->getMessage();
    }
    // cleanup
    try {
        if ($row) {
            \App\Models\StockMovement::where('transaction_id', $row->id)->delete();
            \Illuminate\Support\Facades\DB::table('products')->where('id', $row->product_id)->decrement('current_stock', (float) $row->qty);
            $row->delete();
            $out['cleanup'] = 'OK';
        }
    } catch (\Throwable $e) {
        $out['cleanup'] = $e->getMessage();
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
