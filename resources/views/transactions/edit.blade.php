@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-3xl font-bold text-primary mb-4">Edit Transaksi</h1>
        <p class="text-lg text-gray-600">
            Edit detail transaksi ini.
        </p>
    </div>
    
    <div class="mt-6 bg-white p-6 rounded-lg shadow">
        <form method="POST" action="{{ route('transactions.update', $transaction) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">
                    Tanggal
                </label>
                <input type="date" name="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm placeholder-gray-500 focus:border-primary focus:ring-primary focus:outline-none sm:text-sm" value="{{ old('date', $transaction->date->format('Y-m-d')) }}">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">
                    Tipe Transaksi
                </label>
                <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm placeholder-gray-500 focus:border-primary focus:ring-primary focus:outline-none sm:text-sm">
                    <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Barang Masuk</option>
                    <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Barang Keluar</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">
                    Nama Produk
                </label>
                <select name="product_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm placeholder-gray-500 focus:border-primary focus:ring-primary focus:outline-none sm:text-sm">
                    <option value="" {{ old('product_id') == '' ? 'selected' : '' }}>Pilih Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">
                    Jumlah
                </label>
                <input type="number" name="qty" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm placeholder-gray-500 focus:border-primary focus:ring-primary focus:outline-none sm:text-sm" required
                    value="{{ old('qty', $transaction->qty) }}">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">
                    Nama Admin
                </label>
                <input type="text" name="customer_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm placeholder-gray-500 focus:border-primary focus:ring-primary focus:outline-none sm:text-sm" value="{{ old('customer_name', $transaction->customer_name) }}">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">
                    Foto Barang (Opsional)
                </label>
                <input type="file" name="image" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm placeholder-gray-500 focus:border-primary focus:ring-primary focus:outline-none sm:text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">
                    Foto Delivery (Opsional)
                </label>
                <input type="file" name="photo_delivery" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm placeholder-gray-500 focus:border-primary focus:ring-primary focus:outline-none sm:text-sm">
            </div>
            <div class="mb-4">
                <a href="{{ route('transactions.create') }}" class="bg-secondary text-white py-2 px-4 rounded-md hover:bg-secondary/90 focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 sm:text-sm">
                    Batal
                </a>
                <button type="submit" class="bg-primary text-white py-2 px-4 rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 sm:text-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>