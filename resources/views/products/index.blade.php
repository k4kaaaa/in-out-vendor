@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-accent mb-1">Data Barang</h1>
            <p class="text-lg text-gray-600">Kelola produk dan stok gudang.</p>
        </div>
        <a href="{{ route('products.create') }}" class="bg-accent text-white py-2.5 px-5 rounded-xl text-sm font-semibold shadow-lg shadow-accent/30 hover:bg-accent/90 transition flex items-center justify-center gap-2 w-full sm:w-auto">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Produk
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 mb-5">
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_auto_auto] gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Cari Barang</label>
                <div class="relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau SKU..."
                        class="w-full rounded-xl border border-gray-200 pl-10 pr-3 py-2.5 text-sm shadow-sm focus:border-accent focus:ring-accent">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Kategori</label>
                <select name="category" class="w-full rounded-xl border border-gray-200 py-2.5 px-3 text-sm shadow-sm focus:border-accent focus:ring-accent">
                    <option value="">Semua kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Status</label>
                <select name="status" class="w-full rounded-xl border border-gray-200 py-2.5 px-3 text-sm shadow-sm focus:border-accent focus:ring-accent">
                    <option value="">Semua status</option>
                    <option value="ok" {{ request('status') === 'ok' ? 'selected' : '' }}>Aman</option>
                    <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Kritis</option>
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-accent text-white font-semibold text-sm px-6 py-2.5 shadow-md shadow-emerald-900/10 hover:bg-emerald-700 transition">
                Filter
            </button>
            <a href="{{ route('products.index') }}" class="rounded-xl border border-gray-200 bg-white text-gray-700 font-semibold text-sm px-5 py-2.5 hover:bg-gray-50 transition">
                Reset
            </a>
        </form>
    </div>

    <!-- Mobile card list -->
    <div class="md:hidden space-y-3">
        @forelse($products as $product)
            @php
                $isLow = $product->current_stock < ($product->min_stock ?? 5);
                $unit = $product->unit ?? 'pcs';
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-center gap-3.5">
                    <div class="relative w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0 overflow-hidden border border-emerald-100">
                        <svg class="w-6 h-6 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
                            <path d="M12 22.08V12"></path>
                        </svg>
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="absolute inset-0 w-full h-full object-cover" onerror="this.remove()">
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-gray-900 truncate">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $product->category ?? '-' }}{{ $product->sku ? ' · ' . $product->sku : '' }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="font-extrabold text-gray-900 leading-tight">{{ number_format($product->current_stock, 0, ',', '.') }}</p>
                        <p class="text-[11px] text-gray-400 font-semibold">{{ ucfirst($unit) }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                    @if($isLow)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Kritis</span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aman</span>
                    @endif
                    <div class="flex items-center gap-4">
                        <a href="{{ route('products.show', $product) }}" class="text-xs font-semibold text-blue-600">Lihat</a>
                        <a href="{{ route('products.edit', $product) }}" class="text-xs font-semibold text-accent">Edit</a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline" onsubmit="return confirm('Yakin hapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-red-600">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-12 text-center text-gray-400 text-sm">
                Belum ada produk.
            </div>
        @endforelse
    </div>

    <!-- Desktop table -->
    <div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold uppercase text-gray-500">Foto</th>
                        <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold uppercase text-gray-500">Nama Produk</th>
                        <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold uppercase text-gray-500 hidden lg:table-cell">Kategori</th>
                        <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold uppercase text-gray-500 hidden lg:table-cell">SKU</th>
                        <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold uppercase text-gray-500 text-center">Stok</th>
                        <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold uppercase text-gray-500 text-center hidden lg:table-cell">Min</th>
                        <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold uppercase text-gray-500 text-center">Status</th>
                        <th class="px-4 sm:px-5 py-3.5 text-xs font-semibold uppercase text-gray-500 text-right">Aksi</th>
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
                            <td class="px-4 sm:px-5 py-3.5">
                                <div class="relative w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0 overflow-hidden border border-emerald-100">
                                    <svg class="w-6 h-6 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                        <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
                                        <path d="M12 22.08V12"></path>
                                    </svg>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="absolute inset-0 w-full h-full object-cover" onerror="this.remove()">
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 sm:px-5 py-3.5">
                                <a href="{{ route('products.show', $product) }}" class="font-semibold text-gray-800 hover:text-accent transition">{{ $product->name }}</a>
                            </td>

                            <td class="px-4 sm:px-5 py-3.5 hidden lg:table-cell">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $product->category ?? '-' }}
                                </span>
                            </td>

                            <td class="px-4 sm:px-5 py-3.5 text-gray-600 hidden lg:table-cell">{{ $product->sku ?? '-' }}</td>

                            <td class="px-4 sm:px-5 py-3.5 text-center">
                                <span class="text-base font-bold text-gray-800">{{ number_format($product->current_stock, 0, ',', '.') }}</span>
                                <span class="text-xs font-normal text-gray-400 ml-1">{{ ucfirst($unit) }}</span>
                            </td>

                            <td class="px-4 sm:px-5 py-3.5 text-center hidden lg:table-cell text-gray-500">{{ number_format($product->min_stock ?? 5, 0, ',', '.') }} {{ ucfirst($unit) }}</td>

                            <td class="px-4 sm:px-5 py-3.5 text-center">
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

                            <td class="px-4 sm:px-5 py-3.5 text-right space-x-2">
                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:underline font-medium text-xs" title="Lihat Detail">Lihat</a>
                                <a href="{{ route('products.edit', $product) }}" class="text-accent hover:underline font-medium text-xs" title="Edit">Edit</a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline" onsubmit="return confirm('Yakin hapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline font-medium text-xs" title="Hapus">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
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
