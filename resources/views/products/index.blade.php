@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-accent mb-1">Data Barang</h1>
            <p class="text-lg text-gray-600">Kelola produk dan stok gudang.</p>
        </div>
        <a href="{{ route('products.create') }}" class="bg-accent text-white py-2.5 px-5 rounded-xl text-sm font-semibold shadow-lg shadow-accent/30 hover:bg-accent/90 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Produk
        </a>
    </div>
    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 w-20">Foto</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Nama Produk</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Kategori</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">SKU</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center">Stok</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center">Min</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($products as $product)
                        @php
                            $status = $product->current_stock < ($product->min_stock ?? 5) ? 'low' : 'ok';
                            $unit = $product->unit ?? 'pcs';
                        @endphp
                        <tr class="hover:bg-gray-50 transition group">
                            <!-- Photo thumbnail -->
                            <td class="px-6 py-4">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         alt="{{ $product->name }}"
                                         class="w-12 h-12 object-cover rounded-lg border border-gray-200 shadow-sm">
                                @else
                                    <div class="w-10 h-10 flex items-center justify-center shrink-0 text-emerald-600">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
    <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
    <path d="M12 22.08V12"></path>
</svg></div>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4">
                                <a href="{{ route('products.show', $product) }}" class="font-semibold text-gray-800 hover:text-accent transition">
                                    {{ $product->name }}
                                </a>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $product->category ?? '-' }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-gray-600">{{ $product->sku ?? '-' }}</td>
                            
                            <td class="px-6 py-4 text-center">
                                <span class="text-base font-bold text-gray-800">
                                    {{ number_format($product->current_stock, 0, ',', '.') }}
                                    <span class="text-xs font-normal text-gray-400 ml-1">{{ ucfirst($unit) }}</span>
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-500">{{ number_format($product->min_stock ?? 5, 0, ',', '.') }} {{ ucfirst($unit) }}</span>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @if($status === 'low')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        Kritis
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Aman
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-right space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:underline font-medium" title="Lihat Detail">👁</a>
                                <a href="{{ route('products.edit', $product) }}" class="text-accent hover:underline font-medium" title="Edit">✏️</a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline" onsubmit="return confirm('Yakin hapus produk ini?')">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:underline font-medium" title="Hapus">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <div class="w-20 h-20 mx-auto mb-3 flex items-center justify-center text-emerald-600">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
    <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
    <path d="M12 22.08V12"></path>
</svg></div>
                                Belum ada produk. Klik tombol "Tambah Produk" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection