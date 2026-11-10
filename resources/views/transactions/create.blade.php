@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
            <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center text-accent font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Tambah Transaksi Baru</h2>
        </div>

        <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                        class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipe Transaksi</label>
                    <select name="type" required class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border">
                        <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Barang Masuk</option>
                        <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Barang Keluar</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Barang</label>
                    <select name="product_id" required class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border">
                        <option value="">-- Masukkan nama barang --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} (Stok: {{ $product->current_stock }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jumlah (Qty)</label>
                    <input type="number" name="qty" min="1" step="any" value="{{ old('qty', 1) }}" required
                        class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border" placeholder="1">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Customer / Penanggung Jawab</label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                    class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border" placeholder="Nama Customer / PJ">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Penerima / Lokasi Proyek</label>
                <input type="text" name="receiver_name" value="{{ old('receiver_name') }}"
                    class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border" placeholder="Nama Penerima / Lokasi">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Upload Foto Barang</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border file:border-gray-300 file:text-xs file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Upload Foto Delivery</label>
                    <input type="file" name="photo_delivery" accept="image/*"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border file:border-gray-300 file:text-xs file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('transactions.index') }}" class="px-6 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="bg-accent text-white px-7 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-accent/30 hover:bg-accent/90 transition">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection