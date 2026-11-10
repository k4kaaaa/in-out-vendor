@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-3xl font-bold text-accent mb-4">Laporan Transaksi</h1>
        <p class="text-lg text-gray-600">
            Laporan detail transaksi masuk dan keluar barang.
        </p>
    </div>
    
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <form class="space-y-5">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Awal</label>
                <input type="date" name="start_date"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border"
                    value="{{ old('start_date') }}">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Akhir</label>
                <input type="date" name="end_date"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border"
                    value="{{ old('end_date') }}">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Laporan</label>
                <select name="report_type"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-accent focus:ring-accent py-2.5 px-3 border">
                    <option value="transaksi" {{ old('report_type') == 'transaksi' ? 'selected' : '' }}>Transaksi</option>
                    <option value="stok" {{ old('report_type') == 'stok' ? 'selected' : '' }}>Stok</option>
                </select>
            </div>
            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="bg-accent text-white px-7 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-accent/30 hover:bg-accent/90 transition">
                    Filter Data
                </button>
                <a href="{{ route('reports.index') }}" class="bg-secondary text-white px-7 py-2.5 rounded-xl text-sm font-semibold hover:bg-secondary/90 transition">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection