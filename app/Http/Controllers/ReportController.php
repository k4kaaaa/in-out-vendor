<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\StockMovement;
use Illuminate\Http\Request;

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
}