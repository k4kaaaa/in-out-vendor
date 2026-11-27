@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, Admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Welcome card -->
    <div class="rounded-2xl bg-gradient-to-br from-accent/10 to-surface p-6 sm:p-8 border border-green-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Selamat Datang,</p>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-1">
                    Admin Manager
                </h2>
                <p class="text-sm text-gray-600">
                    Semoga aktivitas gudang hari ini berjalan lancar.
                </p>
            </div>
            <a href="{{ route('transactions.create') }}" class="bg-accent text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-accent/30 hover:bg-accent/90 transition flex items-center gap-2 w-fit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                + Input Barang Masuk
            </a>
        </div>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-bold uppercase text-gray-400 tracking-wide">Barang Masuk Hari Ini</p>
                <div class="w-10 h-10 flex items-center justify-center shrink-0 text-emerald-600">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
    <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
    <path d="M12 22.08V12"></path>
</svg></div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $thisMonthIn }}</p>
            <p class="text-xs text-gray-500 mt-1">Transaksi tercatat</p>
            <div class="mt-4 pt-3 border-t border-gray-50 flex items-center gap-2 text-xs">
                <span class="w-2 h-2 rounded-full bg-accent"></span>
                <span class="text-accent font-semibold">Data terbaru sistem</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-bold uppercase text-gray-400 tracking-wide">Vendor Aktif</p>
                <div class="w-10 h-10 flex items-center justify-center shrink-0 text-emerald-600">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
    <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
    <path d="M12 22.08V12"></path>
</svg></div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $totalVendors }}</p>
            <p class="text-xs text-gray-500 mt-1">Pemasok terdaftar aktif</p>
            <div class="mt-4 pt-3 border-t border-gray-50 flex items-center gap-2 text-xs">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <span class="text-blue-600 font-semibold">Data terbaru sistem</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-bold uppercase text-gray-400 tracking-wide">Total Stok Tersimpan</p>
                <div class="w-10 h-10 flex items-center justify-center shrink-0 text-emerald-600">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
    <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
    <path d="M12 22.08V12"></path>
</svg></div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ number_format($totalStock, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Unit tersedia di gudang</p>
            <div class="mt-4 pt-3 border-t border-gray-50 flex items-center gap-2 text-xs">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                <span class="text-indigo-600 font-semibold">Persediaan saat ini</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-bold uppercase text-gray-400 tracking-wide">Stok Menipis</p>
                <div class="w-10 h-10 flex items-center justify-center shrink-0 text-emerald-600">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
    <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
    <path d="M12 22.08V12"></path>
</svg></div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $lowStockCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Jenis barang perlu dipantau</p>
            <div class="mt-4 pt-3 border-t border-gray-50 flex items-center gap-2 text-xs">
                <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                <span class="text-orange-600 font-semibold">Perlu perhatian</span>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-accent font-bold text-sm mb-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
                Barang Masuk
            </div>
            <p class="text-xs text-gray-500">Total unit yang diterima setiap hari selama 7 hari terakhir.</p>
        </div>

        <div class="h-72 w-full">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Recent transactions table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Transaksi Terbaru</h3>
                <p class="text-xs text-gray-500">Penerimaan barang terakhir</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-accent hover:underline">Lihat semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Waktu & Tanggal</th>
                    <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Vendor</th>
                    <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Total Item</th>
                    <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Status</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($recentTransactions as $tx)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-700">{{ $tx->date->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-gray-800 font-medium">{{ $tx->vendor->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-700 font-semibold">{{ number_format($tx->qty, 0, ',', '.') }} {{ $tx->product->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-accent">
                                Selesai
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">Belum ada transaksi tercatat.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('trendChart').getContext('2d');
    const chartDates = @json($chartDates);
    const chartValues = @json($chartValues);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartDates,
            datasets: [{
                label: 'Unit Diterima',
                data: chartValues,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22, 163, 74, 0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointBackgroundColor: '#16a34a'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: { font: { size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
</script>
@endsection