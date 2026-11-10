@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-3xl font-bold text-accent mb-4">Edit Vendor</h1>
        <p class="text-lg text-gray-600">
            Edit detail vendor ini.
        </p>
    </div>
    
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('vendors.update', $vendor) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Vendor</label>
                <input type="text" name="name" required
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border"
                    value="{{ old('name', $vendor->name) }}">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat</label>
                <input type="text" name="address"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border"
                    value="{{ old('address', $vendor->address) }}">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kontak</label>
                <input type="text" name="contact"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border"
                    value="{{ old('contact', $vendor->contact) }}">
            </div>
            
            <div class="flex items-center gap-3">
                <button type="submit" class="bg-accent text-white px-7 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-accent/30 hover:bg-accent/90 transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('vendors.index') }}" class="bg-secondary text-white px-7 py-2.5 rounded-xl text-sm font-semibold hover:bg-secondary/90 transition">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection