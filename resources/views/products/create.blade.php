@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-3xl font-bold text-accent mb-4">Tambah Produk</h1>
        <p class="text-lg text-gray-600">
            Isi form di bawah untuk menambah produk baru.
        </p>
    </div>
    
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori</label>
                    <input type="text" name="category" list="categories"
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border"
                        value="{{ old('category') }}" placeholder="Pilih atau ketik kategori">
                    <datalist id="categories">
                        <option value="Elektronik">
                        <option value="Alat Tulis">
                        <option value="Peralatan Gudang">
                        <option value="Material Bangunan">
                        <option value="ATK">
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">SKU (Opsional)</label>
                    <input type="text" name="sku"
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border"
                        value="{{ old('sku') }}">
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Stok Awal</label>
                    <input type="number" name="current_stock" min="0" step="any" required
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border"
                        value="{{ old('current_stock') }}">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Satuan Stok</label>
                    <select name="unit"
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border">
                        <option value="pcs" {{ old('unit', 'pcs') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                        <option value="box" {{ old('unit', 'pcs') == 'box' ? 'selected' : '' }}>Box</option>
                        <option value="roll" {{ old('unit', 'pcs') == 'roll' ? 'selected' : '' }}>Roll</option>
                        <option value="rim" {{ old('unit', 'pcs') == 'rim' ? 'selected' : '' }}>Rim</option>
                        <option value="kg" {{ old('unit', 'pcs') == 'kg' ? 'selected' : '' }}>Kg</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Minimum Stok (Alert Threshold)</label>
                <input type="number" name="min_stock" min="0" step="any" value="{{ old('min_stock', 5) }}"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border">
                <p class="text-xs text-gray-500 mt-1">Stok akan di-highlight merah jika stok < minimum ini.</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Foto Produk (Opsional)</label>
                <input type="file" name="image" accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border file:border-gray-300 file:text-xs file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
            </div>
            
            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="bg-accent text-white px-7 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-accent/30 hover:bg-accent/90 transition">
                    Simpan Produk
                </button>
                <a href="{{ route('products.index') }}" class="bg-secondary text-white px-7 py-2.5 rounded-xl text-sm font-semibold hover:bg-secondary/90 transition">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection