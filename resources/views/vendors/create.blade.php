@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-3xl font-bold text-accent mb-4">Tambah Vendor</h1>
        <p class="text-lg text-gray-600">
            Isi form di bawah untuk menambah vendor baru.
        </p>
    </div>
    
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('vendors.store') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Vendor</label>
                <input type="text" name="name" required
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border"
                    value="{{ old('name') }}">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat</label>
                <input type="text" name="address"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border"
                    value="{{ old('address') }}">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kontak</label>
                <input type="text" name="contact"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border"
                    value="{{ old('contact') }}">
            </div>
            
            <button type="submit" class="bg-accent text-white px-7 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-accent/30 hover:bg-accent/90 transition">
                Simpan Vendor
            </button>
        </form>
    </div>
</div>
@endsection