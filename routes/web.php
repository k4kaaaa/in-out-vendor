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

// TEMP — confirm Excel + PDF generation in deployed env — remove
Route::get('/repgen/{token}', function ($token) {
    if ($token !== 'X7k9p2mQz4v') {
        abort(404);
    }
    $out = [];
    try {
        $rc = app(\App\Http\Controllers\ReportController::class);
        $m = new \ReflectionMethod($rc, 'reportRows');
        [, , $rows] = $m->invoke($rc, 'transaksi', null, null);
        [, , $rows2] = $m->invoke($rc, 'stok', null, null);
        $out['rows_txn'] = count($rows);
        $out['rows_stok'] = count($rows2);
    } catch (\Throwable $e) {
        $out['rows'] = get_class($e) . ': ' . substr($e->getMessage(), 0, 150);
    }
    try {
        $ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sh = $ss->getActiveSheet();
        $sh->setCellValue('A1', 'Test');
        $sh->getStyle('A1')->getFont()->setBold(true)->setSize(13)->getColor()->setRGB('064E3B');
        $sh->getColumnDimension('A')->setWidth(20);
        $sh->freezePane('A2');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($ss);
        $tmp = tempnam(sys_get_temp_dir(), 'xl');
        $writer->save($tmp);
        $out['excel'] = 'OK size=' . filesize($tmp);
        @unlink($tmp);
    } catch (\Throwable $e) {
        $out['excel'] = get_class($e) . ': ' . substr($e->getMessage(), 0, 150);
    }
    try {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.download-pdf', [
            'title' => 'Test', 'period' => '', 'headers' => ['A', 'B'], 'rows' => [['1', '2']],
        ])->setPaper('A4', 'landscape');
        $out['pdf'] = 'OK len=' . strlen($pdf->output());
    } catch (\Throwable $e) {
        $out['pdf'] = get_class($e) . ': ' . substr($e->getMessage(), 0, 150);
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
