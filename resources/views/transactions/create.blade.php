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

            @php
                $oldProduct = $products->firstWhere('id', old('product_id'));
                $mode = old('product_mode', old('new_product_name') ? 'new' : 'existing');
            @endphp

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Barang</label>
                <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm text-gray-700">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="product_mode" value="existing" {{ $mode === 'existing' ? 'checked' : '' }} onchange="toggleProductMode()"
                            class="text-accent focus:ring-accent">
                        <span>Barang Sudah Ada</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="product_mode" value="new" {{ $mode === 'new' ? 'checked' : '' }} onchange="toggleProductMode()"
                            class="text-accent focus:ring-accent">
                        <span>Barang Baru</span>
                    </label>
                </div>
            </div>

            <!-- Pilih barang lama (searchable) -->
            <div id="existingBlock">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Cari Barang</label>
                <input type="hidden" name="product_id" id="product_id" value="{{ old('product_id') }}">
                <div class="relative">
                    <input type="text" id="product_search" autocomplete="off" placeholder="Ketik nama barang untuk mencari..."
                        value="{{ old('product_search', $oldProduct->name ?? '') }}"
                        class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 pl-10 pr-3 border">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                    </svg>
                    <ul id="product_list" class="absolute z-20 mt-1 w-full max-h-56 overflow-y-auto bg-white border border-gray-200 rounded-xl shadow-lg hidden">
                        @forelse($products as $product)
                            <li class="product-item px-3.5 py-2.5 text-sm cursor-pointer hover:bg-green-50 transition-colors"
                                data-id="{{ $product->id }}" data-name="{{ $product->name }}">
                                <span class="font-medium text-gray-800">{{ $product->name }}</span>
                                <span class="text-xs text-gray-400">(Stok: {{ $product->current_stock }} {{ $product->unit ?? 'pcs' }})</span>
                            </li>
                        @empty
                            <li class="px-3.5 py-3 text-sm text-gray-400">Belum ada barang. Pilih "Barang Baru".</li>
                        @endforelse
                    </ul>
                </div>
                @error('product_id')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Barang baru -->
            <div id="newBlock" class="hidden space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Barang Baru</label>
                        <input type="text" name="new_product_name" value="{{ old('new_product_name') }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border" placeholder="Nama barang baru">
                        @error('new_product_name')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">SKU <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <input type="text" name="new_product_sku" value="{{ old('new_product_sku') }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border" placeholder="Kode/SKU barang">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori</label>
                        <input type="text" name="new_product_category" value="{{ old('new_product_category') }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border" placeholder="Kategori">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Satuan</label>
                        <input type="text" name="new_product_unit" value="{{ old('new_product_unit', 'pcs') }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border" placeholder="pcs">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Min. Stok</label>
                        <input type="number" name="new_product_min_stock" min="0" step="any" value="{{ old('new_product_min_stock', 5) }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border">
                    </div>
                </div>
                <p class="text-xs text-gray-500">Stok awal barang baru 0. Stok bertambah otomatis bila tipe transaksi "Barang Masuk".</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jumlah (Qty)</label>
                <input type="number" name="qty" min="1" step="any" value="{{ old('qty', 1) }}" required
                    class="w-full sm:max-w-[50%] rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border" placeholder="1">
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

<script>
    function toggleProductMode() {
        const mode = document.querySelector('input[name="product_mode"]:checked')?.value || 'existing';
        const existingBlock = document.getElementById('existingBlock');
        const newBlock = document.getElementById('newBlock');
        if (mode === 'new') {
            existingBlock.classList.add('hidden');
            newBlock.classList.remove('hidden');
            document.getElementById('product_id').value = '';
        } else {
            existingBlock.classList.remove('hidden');
            newBlock.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleProductMode();

        const searchInput = document.getElementById('product_search');
        const list = document.getElementById('product_list');
        const hidden = document.getElementById('product_id');

        searchInput.addEventListener('focus', () => list.classList.remove('hidden'));
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.trim().toLowerCase();
            list.querySelectorAll('.product-item').forEach(function (li) {
                li.classList.toggle('hidden', !li.dataset.name.toLowerCase().includes(q));
            });
            hidden.value = '';
            list.classList.remove('hidden');
        });

        list.addEventListener('click', function (e) {
            const li = e.target.closest('.product-item');
            if (!li) return;
            hidden.value = li.dataset.id;
            searchInput.value = li.dataset.name;
            list.classList.add('hidden');
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#existingBlock')) {
                list.classList.add('hidden');
            }
        });

        // validate mode on submit
        document.querySelector('form').addEventListener('submit', function (e) {
            const mode = document.querySelector('input[name="product_mode"]:checked')?.value || 'existing';
            if (mode === 'existing' && !hidden.value) {
                e.preventDefault();
                searchInput.focus();
                list.classList.remove('hidden');
            }
        });
    });
</script>
@endsection