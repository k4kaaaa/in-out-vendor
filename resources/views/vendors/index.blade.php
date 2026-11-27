@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-accent mb-1">Vendor</h1>
            <p class="text-lg text-gray-600">Kelola vendor sistem.</p>
        </div>
        <a href="{{ route('vendors.create') }}" class="w-full sm:w-auto text-center bg-accent text-white py-2.5 px-5 rounded-xl text-sm font-semibold shadow-lg shadow-accent/30 hover:bg-accent/90 transition">
            Tambah Vendor
        </a>
    </div>
    
    <div class="mt-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 sm:px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase">Nama</th>
                            <th class="px-4 sm:px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Alamat</th>
                            <th class="px-4 sm:px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase">Kontak</th>
                            <th class="px-4 sm:px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($vendors as $vendor)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 sm:px-6 py-3.5 text-gray-800 font-medium">{{ $vendor->name }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-gray-600 hidden md:table-cell">{{ $vendor->address }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-gray-600">{{ $vendor->contact }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-right space-x-3">
                                    <a href="{{ route('vendors.edit', $vendor) }}" class="text-accent hover:underline font-medium">Edit</a>
                                    <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-500 font-medium" 
                                            onclick="return confirm('Yakin ingin menghapus vendor ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection