@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Transaksi</h1>
            <p class="text-sm text-gray-500">Kelola barang masuk dan keluar gudang.</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="bg-accent text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-accent/30 hover:bg-accent/90 transition flex items-center gap-2 w-fit">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Transaksi
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Produk</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Qty</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Penanggung Jawab</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-700">{{ $transaction->date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $transaction->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $transaction->type === 'in' ? 'Barang Masuk' : 'Barang Keluar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ $transaction->product->name }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ number_format($transaction->qty, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $transaction->customer_name ?? $transaction->receiver_name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('transactions.edit', $transaction) }}" class="text-accent hover:underline font-medium">Edit</a>
                                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline font-medium" onclick="return confirm('Hapus transaksi ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 text-sm">Belum ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection