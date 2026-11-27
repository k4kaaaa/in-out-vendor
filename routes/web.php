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

// TEMPORARY DIAGNOSTIC 3 — per-step capture
Route::get('/diagfix3/{token}', function ($token) {
    if ($token !== 'X7k9p2mQz4v') {
        abort(404);
    }
    $out = [];

    // 0) polluted-connection check BEFORE anything
    try {
        DB::selectOne('SELECT 1');
        $out['pre'] = 'clean';
    } catch (\Throwable $e) {
        $out['pre'] = get_class($e) . ': ' . $e->getMessage();
    }

    // 1) session timeouts Neon may enforce
    try {
        $params = DB::select("SELECT name, setting, unit FROM pg_settings WHERE name IN ('statement_timeout','lock_timeout','idle_in_transaction_session_timeout','idle_session_timeout','default_transaction_timeout')");
        $out['params'] = array_map(fn($p) => $p->name . '=' . $p->setting . $p->unit, $params);
    } catch (\Throwable $e) {
        $out['params'] = $e->getMessage();
    }

    // 2) triggers on transactions
    try {
        $triggers = DB::select("SELECT tgname FROM pg_trigger WHERE tgrelid = 'transactions'::regclass AND NOT tgisinternal");
        $out['triggers'] = array_map(fn($t) => $t->tgname, $triggers);
    } catch (\Throwable $e) {
        $out['triggers'] = $e->getMessage();
    }

    // 3) the exact Eloquent SELECT outside txn
    try {
        $r = DB::select("SELECT \"name\" FROM \"products\" WHERE \"id\" = 1 LIMIT 1 FOR UPDATE");
        $out['eloquent_select_outside'] = 'OK: ' . ($r[0]->name ?? '?');
    } catch (\Throwable $e) {
        try { DB::rollBack(); } catch (\Throwable) {}
        $out['eloquent_select_outside'] = get_class($e) . ': ' . $e->getMessage();
    }

    // 4) manual transaction, per-step errors
    try {
        DB::beginTransaction();
        $out['begin'] = 'OK';
        try {
            DB::selectOne('SELECT 1');
            $out['after_begin'] = 'clean';
        } catch (\Throwable $e) {
            $out['after_begin'] = get_class($e) . ': ' . $e->getMessage();
        }
    } catch (\Throwable $e) {
        $out['begin'] = get_class($e) . ': ' . $e->getMessage();
    }

    try {
        $name = DB::table('products')->where('id', 1)->lockForUpdate()->value('name');
        $out['s1_select'] = 'OK: ' . var_export($name, true);
    } catch (\Throwable $e) {
        $out['s1_select'] = get_class($e) . ': ' . $e->getMessage();
    }

    try {
        $tid = DB::table('transactions')->insertGetId([
            'date' => '2026-11-27 00:00:00',
            'type' => 'in',
            'product_id' => 1,
            'vendor_id' => null,
            'qty' => 1,
            'customer_name' => 'diag3',
            'receiver_name' => 'diag3',
        ]);
        $out['s2_insert'] = 'OK: id=' . $tid;
        $out['_tid'] = $tid;
    } catch (\Throwable $e) {
        $out['s2_insert'] = get_class($e) . ': ' . $e->getMessage();
    }

    try {
        DB::table('products')->where('id', 1)->update(['current_stock' => 0]);
        $out['s3_update'] = 'OK';
    } catch (\Throwable $e) {
        $out['s3_update'] = get_class($e) . ': ' . $e->getMessage();
    }

    try {
        $sid = DB::table('stock_movements')->insertGetId([
            'product_id' => 1,
            'transaction_id' => $out['_tid'] ?? 0,
            'type' => 'in',
            'qty_before' => 0,
            'qty_after' => 0,
            'date' => now(),
        ]);
        $out['s4_sm'] = 'OK: id=' . $sid;
    } catch (\Throwable $e) {
        $out['s4_sm'] = get_class($e) . ': ' . $e->getMessage();
    }

    try {
        DB::commit();
        $out['commit'] = 'OK';
    } catch (\Throwable $e) {
        $out['commit'] = get_class($e) . ': ' . $e->getMessage();
        try { DB::rollBack(); } catch (\Throwable) {}
    }

    // cleanup committed row
    try {
        DB::table('transactions')->where('customer_name', 'diag3')->delete();
        $out['cleanup'] = 'OK';
    } catch (\Throwable $e) {
        $out['cleanup'] = $e->getMessage();
    }

    // 5) Laporan: report rows + Excel generation (phpspreadsheet 5.x API) + PDF
    try {
        $rc = app(\App\Http\Controllers\ReportController::class);
        $m = new \ReflectionMethod($rc, 'reportRows');
        [$title, $headers, $rows] = $m->invoke($rc, 'transaksi', null, null);
        $out['report_rows'] = 'OK rows=' . count($rows);
        [, , $rows2] = $m->invoke($rc, 'stok', null, null);
        $out['report_rows_stok'] = 'OK rows=' . count($rows2);
    } catch (\Throwable $e) {
        $out['report_rows'] = get_class($e) . ': ' . $e->getMessage();
    }

    try {
        $ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sh = $ss->getActiveSheet();
        $sh->setTitle('Test');
        $sh->setCellValue('A1', 'Laporan Transaksi');
        $sh->getStyle('A1')->getFont()->setBold(true)->setSize(13)->getColor()->setRGB('064E3B');
        $sh->setCellValue('A3', 'Tanggal');
        $sh->getStyle('A3')->applyFromArray([
            'borders' => ['allBorders' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
        $sh->getColumnDimension('A')->setWidth(20);
        $sh->freezePane('A4');
        $writer = \PhpOffice\PhpSpreadsheet\Writer\Xlsx::createForSpreadsheet($ss);
        $tmp = tempnam(sys_get_temp_dir(), 'xl');
        $writer->save($tmp);
        $out['excel_gen'] = 'OK size=' . filesize($tmp);
        @unlink($tmp);
    } catch (\Throwable $e) {
        $out['excel_gen'] = get_class($e) . ': ' . $e->getMessage();
    }

    try {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.download-pdf', [
            'title' => 'Test',
            'period' => '',
            'headers' => ['Tanggal', 'Tipe'],
            'rows' => [['01/11/2026', 'Masuk']],
        ])->setPaper('A4', 'landscape');
        $out['pdf_gen'] = 'OK len=' . strlen($pdf->output());
    } catch (\Throwable $e) {
        $out['pdf_gen'] = get_class($e) . ': ' . $e->getMessage();
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
