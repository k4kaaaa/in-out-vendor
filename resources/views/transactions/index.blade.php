@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">
    <!-- Header card -->
    <div class="rounded-2xl bg-gradient-to-br from-deepgreen via-accent to-emerald-700 p-5 sm:p-7 shadow-lg shadow-emerald-900/20 border border-emerald-900/10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">{{ request('type', 'in') === 'out' ? 'Barang Keluar' : 'Barang Masuk' }}</h1>
                <p class="text-emerald-100/80 text-xs sm:text-sm mt-1.5">Rekapitulasi {{ request('type', 'in') === 'out' ? 'barang keluar' : 'barang masuk' }} gudang.</p>
            </div>
            <a href="{{ route('transactions.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-white text-emerald-800 font-semibold text-sm px-5 py-3 rounded-xl shadow-md hover:bg-emerald-50 transition w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Transaksi
            </a>
        </div>
    </div>

    <!-- Filter card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5">
        <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_auto_auto] gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Cari Barang / SKU</label>
                <div class="relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang atau SKU..."
                        class="w-full rounded-xl border border-gray-200 bg-white pl-10 pr-3 py-2.5 text-sm focus:border-accent focus:ring-accent shadow-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Pilih Vendor</label>
                <select name="vendor_id" class="w-full rounded-xl border border-gray-200 py-2.5 px-3 text-sm shadow-sm focus:border-accent focus:ring-accent">
                    <option value="">Semua vendor</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Tipe</label>
                <select name="type" class="w-full rounded-xl border border-gray-200 py-2.5 px-3 text-sm shadow-sm focus:border-accent focus:ring-accent">
                    <option value="in" {{ request('type', 'in') === 'in' ? 'selected' : '' }}>Barang Masuk</option>
                    <option value="out" {{ request('type', 'in') === 'out' ? 'selected' : '' }}>Barang Keluar</option>
                    <option value="all" {{ request('type', 'in') === 'all' ? 'selected' : '' }}>Semua</option>
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-accent text-white font-semibold text-sm px-6 py-2.5 shadow-md shadow-emerald-900/10 hover:bg-emerald-700 transition">
                Filter
            </button>
            <a href="{{ route('transactions.index') }}" class="rounded-xl border border-gray-200 bg-white text-gray-700 font-semibold text-sm px-5 py-2.5 hover:bg-gray-50 transition">
                Reset
            </a>
        </form>
    </div>

    <!-- Data card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] text-sm text-left">
                <thead class="bg-gray-50/80 border-b border-gray-100">
                    <tr>
                        <th class="px-3 sm:px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Tanggal</th>
                        <th class="px-3 sm:px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Barang</th>
                        <th class="px-3 sm:px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Vendor</th>
                        <th class="px-3 sm:px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase text-right">Stok</th>
                        <th class="px-3 sm:px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $t)
                        @php $product = $t->product; @endphp
                        <tr class="hover:bg-emerald-50/40 transition-colors">
                            <td class="px-3 sm:px-5 py-3.5 text-gray-600 whitespace-nowrap">{{ $t->date->format('d/m/Y H:i') }}</td>
                            <td class="px-3 sm:px-5 py-3.5">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="relative w-11 h-11 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0 overflow-hidden border border-emerald-100">
                                        <svg class="w-6 h-6 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                            <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
                                            <path d="M12 22.08V12"></path>
                                        </svg>
                                        @if($product?->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="" class="absolute inset-0 w-full h-full object-cover" onerror="this.remove()">
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 truncate max-w-[180px]">{{ $product->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-500 truncate max-w-[180px]">{{ $product->category ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 sm:px-5 py-3.5 hidden md:table-cell">
                                @if($t->vendor)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ mb_substr($t->vendor->name, 0, 1) }}
                                        </div>
                                        <span class="text-gray-700 font-medium">{{ $t->vendor->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-5 py-3.5 text-right font-bold whitespace-nowrap {{ $t->type === 'in' ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $t->type === 'in' ? '+' : '-' }} {{ number_format((float) $t->qty, 0, ',', '.') }} <span class="text-xs font-semibold text-gray-400">{{ $product->unit ?? 'pcs' }}</span>
                            </td>
                            <td class="px-3 sm:px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('transactions.edit', $t) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 012.828 2.828L11 15l-3 3v-3h3z"></path></svg>
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('transactions.destroy', $t) }}" class="inline" onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 hover:text-red-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <div class="w-16 h-16 mx-auto mb-3 flex items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                        <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
                                        <path d="M12 22.08V12"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-600 font-medium">Belum ada barang masuk.</p>
                                <p class="text-gray-400 text-xs mt-1">Klik "Tambah Transaksi" untuk input barang masuk pertama.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
