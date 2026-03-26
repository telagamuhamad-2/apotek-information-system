@extends('layouts.app')

@section('title', 'Tambah Obat')

@section('header-title', 'Tambah Obat')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <a href="{{ route('products.index') }}" class="text-emerald-600 hover:text-emerald-800 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
    </a>
    <h2 class="text-xl font-semibold text-gray-800">
        <i class="fas fa-plus-circle mr-2 text-emerald-600"></i>Tambah Obat Baru
    </h2>

    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <p class="text-sm text-blue-800">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Info Harga:</strong>
        </p>
        <ul class="list-disc list-inside ml-2 space-y-1 mt-2">
            <li><strong>Harga Beli:</strong> Harga modal saat pembelian (input di sini)</li>
            <li><strong>Harga Jual:</strong> Harga jual ke pelanggan (input di sini)</li>
            <li><strong>Stok:</strong> Otomatis dikelola. + Pembelian, - Penjualan</li>
        </ul>
    </div>

    <form method="POST" action="{{ route('products.store') }}" class="mt-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="product_code" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-barcode mr-2 text-emerald-600"></i>Kode Obat
                </label>
                <input type="text"
                       id="product_code"
                       name="product_code"
                       value="{{ old('product_code') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Contoh: OB001">
                @error('product_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="product_name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-prescription-bottle mr-2 text-emerald-600"></i>Nama Obat
                </label>
                <input type="text"
                       id="product_name"
                       name="product_name"
                       value="{{ old('product_name') }}"
                       required
                       autofocus
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Nama obat">
                @error('product_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="product_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tag mr-2 text-emerald-600"></i>Jenis Obat
                </label>
                <select id="product_type_id"
                        name="product_type_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Pilih Jenis</option>
                    @foreach($productTypes as $type)
                        <option value="{{ $type->id }}" {{ old('product_type_id') == $type->id ? 'selected' : '' }}">
                            {{ $type->product_type_name }}
                        </option>
                    @endforeach
                </select>
                @error('product_type_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="product_purpose" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-info-circle mr-2 text-emerald-600"></i>Kegunaan
                </label>
                <input type="text"
                       id="product_purpose"
                       name="product_purpose"
                       value="{{ old('product_purpose') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Contoh: Demam, Batuk, dll">
                @error('product_purpose')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Harga Beli -->
            <div>
                <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-money-bill mr-2 text-blue-600"></i>Harga Beli (Modal)
                </label>
                <input type="number"
                       id="purchase_price"
                       name="purchase_price"
                       value="{{ old('purchase_price') }}"
                       required
                       min="0"
                       step="0.01"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="0">
                <p class="mt-1 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Harga saat pembelian dari vendor
                </p>
                @error('purchase_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Harga Jual -->
            <div>
                <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-money-bill mr-2 text-emerald-600"></i>Harga Jual (ke Pelanggan)
                </label>
                <input type="number"
                       id="selling_price"
                       name="selling_price"
                       value="{{ old('selling_price') }}"
                       required
                       min="0"
                       step="0.01"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="0">
                <p class="mt-1 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Harga saat jual ke pelanggan
                </p>
                @error('selling_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="product_expiration_date" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-2 text-emerald-600"></i>Tanggal Kedaluwarsa
                </label>
                <input type="date"
                       id="product_expiration_date"
                       name="product_expiration_date"
                       value="{{ old('product_expiration_date') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                @error('product_expiration_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Stock Display (Read Only) -->
        <div class="md:col-span-2 p-4 bg-gray-50 border border-gray-200 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-cubes mr-2 text-emerald-600"></i>Stok Awal
            </label>
            <div class="text-2xl font-bold text-emerald-600">0</div>
            <p class="mt-2 text-sm text-gray-500">
                <i class="fas fa-arrow-up mr-1 text-blue-600"></i>
                Stok akan bertambah saat ada <strong>pembelian</strong>
            </p>
            <p class="text-sm text-gray-500">
                <i class="fas fa-arrow-down mr-1 text-red-600"></i>
                Stok akan berkurang saat ada <strong>penjualan</strong>
            </p>
        </div>

        <div class="flex justify-end space-x-4 mt-8">
            <a href="{{ route('products.index') }}"
               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit"
                    class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                <i class="fas fa-save mr-2"></i>Simpan
            </button>
        </div>
    </form>
</div>
@endsection
