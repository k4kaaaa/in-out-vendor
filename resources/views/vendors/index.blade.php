@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-accent mb-1">Vendor</h1>
            <p class="text-lg text-gray-600">Kelola vendor sistem.</p>
        </div>
        <a href="{{ route('vendors.create') }}" class="w-full sm:w-auto text-center bg-accent text-white py-2.5 px-5 rounded-xl text-sm font-semibold shadow-lg shadow-accent/30 hover:bg-accent/90 transition flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Vendor
        </a>
    </div>
    
    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 mb-5">
        <form method="GET" action="{{ route('vendors.index') }}" class="grid grid-cols-1 sm:grid-cols-[1fr_auto_auto] gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Cari Vendor</label>
                <div class="relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, alamat, atau kontak..."
                        class="w-full rounded-xl border border-gray-200 pl-10 pr-3 py-2.5 text-sm shadow-sm focus:border-accent focus:ring-accent">
                </div>
            </div>
            <button type="submit" class="rounded-xl bg-accent text-white font-semibold text-sm px-6 py-2.5 shadow-md shadow-emerald-900/10 hover:bg-emerald-700 transition">
                Filter
            </button>
            <a href="{{ route('vendors.index') }}" class="rounded-xl border border-gray-200 bg-white text-gray-700 font-semibold text-sm px-5 py-2.5 hover:bg-gray-50 transition text-center">
                Reset
            </a>
        </form>
    </div>

    <!-- Mobile card list -->
    <div class="md:hidden space-y-3">
        @forelse($vendors as $vendor)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 space-y-2">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-extrabold shrink-0">
                            {{ mb_substr($vendor->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-base">{{ $vendor->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $vendor->contact ?: 'Tidak ada kontak' }}</p>
                        </div>
                    </div>
                </div>
                @if($vendor->address)
                    <p class="text-xs text-gray-600 bg-gray-50 p-2.5 rounded-xl">{{ $vendor->address }}</p>
                @endif
                <div class="flex items-center justify-end gap-4 pt-2 border-t border-gray-100">
                    <a href="{{ route('vendors.edit', $vendor) }}" class="text-xs font-semibold text-accent">Edit</a>
                    <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus vendor ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-semibold text-red-600">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-12 text-center text-gray-400 text-sm">
                Belum ada vendor.
            </div>
        @endforelse
    </div>

    <!-- Desktop table -->
    <div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase">Nama Vendor</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase">Alamat</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase">Kontak</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($vendors as $vendor)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ mb_substr($vendor->name, 0, 1) }}
                                    </div>
                                    <span class="text-gray-900 font-semibold">{{ $vendor->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-gray-600 max-w-xs truncate">{{ $vendor->address ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-gray-600">{{ $vendor->contact ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-right space-x-3 whitespace-nowrap">
                                <a href="{{ route('vendors.edit', $vendor) }}" class="text-accent hover:underline font-medium text-xs">Edit</a>
                                <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus vendor ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-500 font-medium text-xs">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                Belum ada vendor. Klik "Tambah Vendor" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
