<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::all();
        $reportType = $request->input('report_type', 'transaksi');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $filtered = null;

        if ($reportType === 'transaksi') {
            $query = Transaction::with('product', 'vendor');
            if ($startDate) $query->whereDate('date', '>=', $startDate);
            if ($endDate) $query->whereDate('date', '<=', $endDate);
            $filtered = $query->latest()->get();
        } else {
            $query = StockMovement::with('product', 'transaction');
            if ($startDate) $query->whereDate('date', '>=', $startDate);
            if ($endDate) $query->whereDate('date', '<=', $endDate);
            $filtered = $query->latest()->get();
        }

        return view('reports.index', [
            'products' => $products,
            'reportType' => $reportType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filtered' => $filtered,
        ]);
    }

    public function download(Request $request)
    {
        $request->validate([
            'format' => ['required', 'in:pdf,excel'],
            'report_type' => ['nullable', 'in:transaksi,stok'],
        ]);

        $reportType = $request->input('report_type', 'transaksi');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        [$title, $headers, $rows] = $this->reportRows($reportType, $startDate, $endDate);
        $base = 'laporan-' . ($reportType === 'transaksi' ? 'transaksi' : 'stok') . '-' . now()->format('Y-m-d-His');

        if ($request->input('format') === 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($reportType === 'transaksi' ? 'Laporan Transaksi' : 'Stock Movement');

            $lastCol = Coordinate::stringFromColumnIndex(count($headers));
            $startRow = $startDate || $endDate ? 4 : 3;

            $sheet->setCellValue('A1', $title);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13)->getColor()->setRGB('064E3B');
            if ($startDate || $endDate) {
                $sheet->setCellValue('A2', 'Periode: ' . ($startDate ?: 'awal') . ' s.d. ' . ($endDate ?: now()->format('Y-m-d')));
                $sheet->getStyle('A2')->getFont()->setItalic(true)->getColor()->setRGB('6B7280');
            }

            foreach (array_keys($headers) as $i => $h) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . $startRow, $h);
            }
            $headerRange = 'A' . $startRow . ':' . $lastCol . $startRow;
            $sheet->getStyle($headerRange)
                ->getFont()->setBold(true)->getColor()->setRGB('FFFFFF')
                ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('16A34A')
                ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            foreach ($rows as $rIdx => $row) {
                foreach ($row as $cIdx => $val) {
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($cIdx + 1) . ($startRow + 1 + $rIdx), $val);
                }
            }
            if (count($rows)) {
                $dataRange = 'A' . ($startRow + 1) . ':' . $lastCol . ($startRow + count($rows));
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['style' => Border::BORDER_THIN]
                    ]
                ]);
            }

            $widths = [20, 14, 24, 12, 20, 20, 24];
            for ($i = 1; $i <= count($headers); $i++) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setWidth($widths[$i - 1] ?? 16);
            }
            $sheet->freezePane('A' . ($startRow + 1));

            $writer = Xlsx::createForSpreadsheet($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $base . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            exit;
        }

        return Pdf::loadView('reports.download-pdf', [
            'title' => $title,
            'period' => ($startDate || $endDate) ? 'Periode: ' . ($startDate ?: 'awal') . ' s.d. ' . ($endDate ?: now()->format('Y-m-d')) : '',
            'headers' => $headers,
            'rows' => $rows,
        ])->setPaper('A4', 'landscape')->stream($base . '.pdf');
    }

    private function reportRows(string $reportType, ?string $startDate, ?string $endDate): array
    {
        if ($reportType === 'stok') {
            $query = StockMovement::with('product');
            if ($startDate) $query->whereDate('date', '>=', $startDate);
            if ($endDate) $query->whereDate('date', '<=', $endDate);
            $rows = $query->latest()->get()->map(fn ($m) => [
                $m->date?->format('d/m/Y H:i') ?? '-',
                $m->type === 'in' ? 'Masuk' : 'Keluar',
                $m->product->name ?? '-',
                (float) $m->qty_before,
                (float) $m->qty_after,
            ])->all();

            return [
                'Laporan Stock Movement',
                ['Tanggal', 'Tipe', 'Produk', 'Qty Sebelum', 'Qty Sesudah'],
                $rows,
            ];
        }

        $query = Transaction::with('product', 'vendor');
        if ($startDate) $query->whereDate('date', '>=', $startDate);
        if ($endDate) $query->whereDate('date', '<=', $endDate);
        $rows = $query->latest()->get()->map(fn ($t) => [
            $t->date?->format('d/m/Y H:i') ?? '-',
            $t->type === 'in' ? 'Masuk' : 'Keluar',
            $t->product->name ?? '-',
            (float) $t->qty,
            $t->customer_name ?? '-',
            $t->receiver_name ?? '-',
            $t->vendor->name ?? '-',
        ])->all();

        return [
            'Laporan Transaksi',
            ['Tanggal', 'Tipe', 'Produk', 'Qty', 'PJ / Customer', 'Penerima', 'Vendor'],
            $rows,
        ];
    }
}