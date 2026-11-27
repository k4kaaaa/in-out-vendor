@extends('layouts.app')

@section('page-title', 'Laporan')
@section('page-subtitle', 'Filter data transaksi dan stock movement')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Laporan</h1>
            <p class="text-sm text-gray-600 mt-1">Filter data transaksi dan stock movement. Unduh hasil laporan ke PDF atau Excel.</p>
        </div>
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <a href="{{ route('reports.download', ['format' => 'pdf', 'report_type' => $reportType, 'start_date' => $startDate ?: null, 'end_date' => $endDate ?: null]) }}"
               class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 bg-white border border-red-500 text-red-600 hover:bg-red-50 px-4 py-2.5 rounded-xl text-sm font-semibold transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M20 20H4v-6l4-4 8 8z"></path>
                </svg>
                Unduh PDF
            </a>
            <a href="{{ route('reports.download', ['format' => 'excel', 'report_type' => $reportType, 'start_date' => $startDate ?: null, 'end_date' => $endDate ?: null]) }}"
               class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 bg-accent text-white hover:bg-accent/90 px-4 py-2.5 rounded-xl text-sm font-semibold transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M20 20H4v-6l4-4 8 8z"></path>
                </svg>
                Unduh Excel
            </a>
        </div>
    </div>

    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Tanggal Awal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Tipe Laporan</label>
                <select name="report_type" class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3">
                    <option value="transaksi" {{ $reportType === 'transaksi' ? 'selected' : '' }}>Transaksi</option>
                    <option value="stok" {{ $reportType === 'stok' ? 'selected' : '' }}>Stock Movement</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="w-full bg-accent text-white py-2.5 px-4 rounded-xl hover:bg-accent/90 text-sm font-semibold transition">
                    Filter Data
                </button>
                <a href="{{ route('reports.index') }}" class="shrink-0 bg-gray-100 text-gray-600 hover:bg-gray-200 py-2.5 px-4 rounded-xl text-sm font-semibold transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm text-gray-600"><span class="font-bold text-gray-900">{{ $filtered->count() }}</span> data {{ $reportType === 'transaksi' ? 'transaksi' : 'stock movement' }} ditemukan.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[560px]">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                        <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Produk</th>
                        @if($reportType === 'transaksi')
                            <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase text-right">Qty</th>
                            <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">PJ / Customer</th>
                            <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Penerima</th>
                        @else
                            <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase text-right">Qty Sebelum</th>
                            <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase text-right">Qty Sesudah</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($filtered as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 sm:px-5 py-3.5 text-gray-700 whitespace-nowrap">{{ $item->date->format('d/m/Y H:i') }}</td>
                            <td class="px-4 sm:px-5 py-3.5">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $item->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $item->type === 'in' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="px-4 sm:px-5 py-3.5 font-medium text-gray-800">{{ $item->product->name }}</td>
                            @if($reportType === 'transaksi')
                                <td class="px-4 sm:px-5 py-3.5 text-gray-700 font-semibold text-right">{{ number_format($item->qty, 2) }}</td>
                                <td class="px-4 sm:px-5 py-3.5 text-gray-600">{{ $item->customer_name ?? '-' }}</td>
                                <td class="px-4 sm:px-5 py-3.5 text-gray-600">{{ $item->receiver_name ?? '-' }}</td>
                            @else
                                <td class="px-4 sm:px-5 py-3.5 text-gray-600 text-right">{{ number_format($item->qty_before, 2) }}</td>
                                <td class="px-4 sm:px-5 py-3.5 text-gray-600 font-semibold text-right">{{ number_format($item->qty_after, 2) }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">Tidak ada data laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection