@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan</h1>
        <p class="text-sm text-gray-600">Filter data transaksi dan stock movement.</p>
    </div>
    
    <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tanggal Awal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tipe Laporan</label>
                <select name="report_type" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
                    <option value="transaksi" {{ $reportType === 'transaksi' ? 'selected' : '' }}>Transaksi</option>
                    <option value="stok" {{ $reportType === 'stok' ? 'selected' : '' }}>Stock Movement</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-md hover:bg-primary/90 text-sm font-medium transition">
                    Filter Data
                </button>
            </div>
        </form>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="p-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="p-3 text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                        <th class="p-3 text-xs font-semibold text-gray-500 uppercase">Produk</th>
                        @if($reportType === 'transaksi')
                            <th class="p-3 text-xs font-semibold text-gray-500 uppercase">Qty</th>
                            <th class="p-3 text-xs font-semibold text-gray-500 uppercase">PJ / Customer</th>
                            <th class="p-3 text-xs font-semibold text-gray-500 uppercase">Penerima</th>
                        @else
                            <th class="p-3 text-xs font-semibold text-gray-500 uppercase">Qty Sebelum</th>
                            <th class="p-3 text-xs font-semibold text-gray-500 uppercase">Qty Sesudah</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($filtered as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 text-gray-700">{{ $item->date->format('Y-m-d H:i') }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $item->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $item->type === 'in' ? 'Barang Masuk' : 'Barang Keluar' }}
                                </span>
                            </td>
                            <td class="p-3 font-medium text-gray-800">{{ $item->product->name }}</td>
                            @if($reportType === 'transaksi')
                                <td class="p-3 text-gray-700 font-semibold">{{ number_format($item->qty, 2) }}</td>
                                <td class="p-3 text-gray-600">{{ $item->customer_name ?? '-' }}</td>
                                <td class="p-3 text-gray-600">{{ $item->receiver_name ?? '-' }}</td>
                            @else
                                <td class="p-3 text-gray-600">{{ number_format($item->qty_before, 2) }}</td>
                                <td class="p-3 text-gray-600 font-semibold">{{ number_format($item->qty_after, 2) }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-500 text-sm">Tidak ada data laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection