<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\Vendor;
use Illuminate\Support\Carbon;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalVendors = Vendor::count();
        $totalStock = Product::sum('current_stock');
        $lowStockCount = Product::where('current_stock', '<', 5)->count();

        $today = now()->startOfDay();
        $thisMonthIn = Transaction::where('type', 'in')
            ->whereDate('date', '>=', $today)
            ->count();

        $chartDates = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartDates[] = $date->format('d/m');
            $sum = Transaction::where('type', 'in')
                ->whereDate('date', $date->toDateString())
                ->sum('qty');
            $chartValues[] = (float) $sum;
        }

        $recentTransactions = Transaction::with('product')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard_staff', [
            'totalProducts' => $totalProducts,
            'totalVendors' => $totalVendors,
            'totalStock' => $totalStock,
            'lowStockCount' => $lowStockCount,
            'thisMonthIn' => $thisMonthIn,
            'chartDates' => $chartDates,
            'chartValues' => $chartValues,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
