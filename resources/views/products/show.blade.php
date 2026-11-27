@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="py-6 flex items-center gap-3">
        <a href="{{ route('products.index') }}" class="p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-accent">{{ $product->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">Detail produk & riwayat transaksi</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Product Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden sticky top-20">
                <!-- Image -->
                @if($product->image)
                    <div class="aspect-square bg-gray-50 flex items-center justify-center p-4">
                        <img src="{{ asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}"
                             class="max-h-full max-w-full object-contain rounded-lg shadow-sm">
                    </div>
                @else
                    <div class="aspect-square flex items-center justify-center p-20 text-emerald-600">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
    <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
    <path d="M12 22.08V12"></path>
</svg></div>
                @endif
                
                <div class="p-5 space-y-4">
                    <!-- SKU -->
                    @if($product->sku)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">SKU</p>
                        <p class="text-sm font-medium text-gray-700">{{ $product->sku }}</p>
                    </div>
                    @endif
                    
                    <hr class="border-gray-100">
                    
                    <!-- Stock Status -->
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Stok Saat Ini</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black {{ $product->current_stock < 5 ? 'text-orange-500' : 'text-gray-900' }}">
                                {{ number_format($product->current_stock, 0, ',', '.') }}
                            </span>
                            <span class="text-sm text-gray-500">unit</span>
                        </div>
                        @if($product->current_stock < 5)
                            <p class="text-xs text-orange-600 mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                Stok menipis - perlu restock
                            </p>
                        @elseif($product->current_stock >= 20)
                            <p class="text-xs text-green-600 mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Stok aman
                            </p>
                        @endif
                    </div>
                    
                    <hr class="border-gray-100">
                    
                    <!-- Transaction Summary -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-green-50 rounded-xl p-3 border border-green-100">
                            <p class="text-xs font-semibold text-green-600 uppercase mb-1">Total Masuk</p>
                            <p class="text-xl font-black text-green-700">{{ number_format($totalIn, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-red-50 rounded-xl p-3 border border-red-100">
                            <p class="text-xs font-semibold text-red-600 uppercase mb-1">Total Keluar</p>
                            <p class="text-xl font-black text-red-700">{{ number_format($totalOut, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2 pt-2">
                        <a href="{{ route('transactions.create', ['product_id' => $product->id]) }}" 
                           class="flex-1 bg-accent text-white py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-accent/30 hover:bg-accent/90 transition text-center">
                            + Transaksi Baru
                        </a>
                        <a href="{{ route('products.edit', $product) }}" 
                           class="px-4 py-2.5 bg-secondary text-white rounded-xl text-sm font-semibold hover:bg-secondary/90 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" class="px-2 py-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2.5 rounded-xl text-red-500 hover:bg-red-50 transition" title="Hapus Produk" onclick="return confirm('Yakin hapus produk ini?')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-8 8-8-8m8 8V3"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Transactions & History -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Recent Transactions -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Transaksi Terakhir</h3>
                        <p class="text-xs text-gray-500">{{ count($transactions) }} histori pergerakan stok</p>
                    </div>
                    <a href="{{ route('transactions.index') }}?product_id={{ $product->id }}" 
                       class="text-xs font-bold text-accent hover:underline">Lihat semua →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Tipe</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Qty</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">PJ / Customer</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Penerima</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($transactions as $tx)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-gray-700 whitespace-nowrap">{{ $tx->date->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $tx->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $tx->type === 'in' ? 'Masuk' : 'Keluar' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">{{ number_format($tx->qty, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $tx->customer_name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $tx->receiver_name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('transactions.edit', $tx) }}" class="text-accent hover:underline font-medium">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-400 text-sm">Belum ada transaksi untuk produk ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Stock Movement History -->
            @if(count($stockMovements) > 0)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Riwayat Perubahan Stok</h3>
                    <p class="text-xs text-gray-500">5 pergerakan terakhir</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Waktu</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Type</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Sebelum</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase text-gray-500">Sesudah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($stockMovements as $movement)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-gray-700 whitespace-nowrap">{{ $movement->date->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $movement->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $movement->type === 'in' ? 'Masuk' : 'Keluar' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ number_format($movement->qty_before, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">{{ number_format($movement->qty_after, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            
        </div>
    </div>
</div>
@endsection