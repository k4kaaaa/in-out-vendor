@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-accent font-bold border border-emerald-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Tambah Transaksi Baru</h2>
                <p class="text-xs text-gray-500">Input data barang masuk atau keluar gudang.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Tanggal</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                        class="w-full rounded-xl border-gray-200 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Tipe Transaksi</label>
                    <select name="type" required class="w-full rounded-xl border-gray-200 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border font-semibold">
                        <option value="in" {{ old('type', 'in') === 'in' ? 'selected' : '' }}>Barang Masuk (+ Stok)</option>
                        <option value="out" {{ old('type') === 'out' ? 'selected' : '' }}>Barang Keluar (- Stok)</option>
                    </select>
                </div>
            </div>

            @php
                $mode = old('product_mode', old('new_product_name') ? 'new' : 'existing');
            @endphp

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Pilihan Barang</label>
                <div class="flex items-center gap-6 p-3 bg-gray-50 rounded-xl border border-gray-100 text-sm">
                    <label class="flex items-center gap-2 cursor-pointer font-medium text-gray-800">
                        <input type="radio" name="product_mode" value="existing" {{ $mode === 'existing' ? 'checked' : '' }} onchange="toggleProductMode()"
                            class="text-accent focus:ring-accent">
                        <span>Pilih Barang Sudah Ada</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer font-medium text-gray-800">
                        <input type="radio" name="product_mode" value="new" {{ $mode === 'new' ? 'checked' : '' }} onchange="toggleProductMode()"
                            class="text-accent focus:ring-accent">
                        <span>Buat Barang Baru</span>
                    </label>
                </div>
            </div>

            <!-- Pilih barang lama -->
            <div id="existingBlock">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Nama Barang</label>
                <select name="product_id" id="product_id" class="w-full rounded-xl border-gray-200 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border bg-white">
                    <option value="">-- Pilih Barang dari Daftar --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id', request('product_id')) == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} {{ $product->sku ? '('.$product->sku.')' : '' }} — Stok: {{ number_format($product->current_stock, 0, ',', '.') }} {{ $product->unit ?? 'pcs' }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Barang baru -->
            <div id="newBlock" class="hidden space-y-4 p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                <p class="text-xs font-bold text-emerald-800 uppercase tracking-wide mb-1">Informasi Barang Baru</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nama Barang Baru *</label>
                        <input type="text" name="new_product_name" value="{{ old('new_product_name') }}"
                            class="w-full rounded-xl border-gray-200 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border bg-white" placeholder="Contoh: Semen Gresik 50kg">
                        @error('new_product_name')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">SKU / Kode <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <input type="text" name="new_product_sku" value="{{ old('new_product_sku') }}"
                            class="w-full rounded-xl border-gray-200 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border bg-white" placeholder="Contoh: SMN-001">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Kategori</label>
                        <input type="text" name="new_product_category" value="{{ old('new_product_category') }}"
                            class="w-full rounded-xl border-gray-200 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border bg-white" placeholder="Contoh: Material">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Satuan</label>
                        <input type="text" name="new_product_unit" value="{{ old('new_product_unit', 'pcs') }}"
                            class="w-full rounded-xl border-gray-200 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border bg-white" placeholder="pcs / sak / box">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Min. Stok Warning</label>
                        <input type="number" name="new_product_min_stock" min="0" step="any" value="{{ old('new_product_min_stock', 5) }}"
                            class="w-full rounded-xl border-gray-200 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border bg-white">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Jumlah (Qty)</label>
                <input type="number" name="qty" min="1" step="any" value="{{ old('qty', 1) }}" required
                    class="w-full sm:max-w-[200px] rounded-xl border-gray-200 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border font-bold text-gray-900" placeholder="1">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Customer / Penanggung Jawab</label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                    class="w-full rounded-xl border-gray-200 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border" placeholder="Nama Penanggung Jawab / Customer">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Penerima / Lokasi Proyek</label>
                <input type="text" name="receiver_name" value="{{ old('receiver_name') }}"
                    class="w-full rounded-xl border-gray-200 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border" placeholder="Nama Penerima atau Lokasi Proyek">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Upload Foto Barang</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border file:border-gray-200 file:text-xs file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Upload Surat Jalan / Delivery</label>
                    <input type="file" name="photo_delivery" accept="image/*"
                        class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border file:border-gray-200 file:text-xs file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('transactions.index') }}" class="px-6 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="bg-accent text-white px-7 py-2.5 rounded-xl text-sm font-semibold shadow-md hover:bg-emerald-700 transition">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleProductMode() {
        const mode = document.querySelector('input[name="product_mode"]:checked')?.value || 'existing';
        const existingBlock = document.getElementById('existingBlock');
        const newBlock = document.getElementById('newBlock');
        const selectProduct = document.getElementById('product_id');
        
        if (mode === 'new') {
            existingBlock.classList.add('hidden');
            newBlock.classList.remove('hidden');
            selectProduct.value = '';
            selectProduct.removeAttribute('required');
        } else {
            existingBlock.classList.remove('hidden');
            newBlock.classList.add('hidden');
            selectProduct.setAttribute('required', 'required');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleProductMode();
    });
</script>
@endsection
