@extends('layouts.app')

@section('title', 'Edit Pembelian')

@section('header-title', 'Edit Pembelian')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <a href="{{ route('pembelian.index') }}" class="text-emerald-600 hover:text-emerald-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-edit mr-2 text-emerald-600"></i>Edit Pembelian
        </h2>
    </div>

    <form method="POST" action="{{ route('pembelian.update', $productIncoming->id) }}" class="p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="product_code" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-barcode mr-2 text-emerald-600"></i>Kode Obat
                </label>
                <input type="text"
                       id="product_code"
                       name="product_code"
                       value="{{ old('product_code', $productIncoming->product_code) }}"
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
                       value="{{ old('product_name', $productIncoming->product_name) }}"
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
                        <option value="{{ $type->id }}" {{ old('product_type_id', $productIncoming->product_type_id) == $type->id ? 'selected' : '' }}>
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
                       value="{{ old('product_purpose', $productIncoming->product_purpose) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Contoh: Demam, Batuk, dll">
                @error('product_purpose')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="product_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-cubes mr-2 text-emerald-600"></i>Jumlah
                </label>
                <input type="number"
                       id="product_quantity"
                       name="product_quantity"
                       value="{{ old('product_quantity', $productIncoming->product_quantity) }}"
                       required
                       min="1"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="0">
                @error('product_quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="product_each_price" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-money-bill mr-2 text-emerald-600"></i>Harga Satuan
                </label>
                <input type="number"
                       id="product_each_price"
                       name="product_each_price"
                       value="{{ old('product_each_price', $productIncoming->product_each_price) }}"
                       required
                       min="0"
                       step="0.01"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="0">
                @error('product_each_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="vendor_name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-store mr-2 text-emerald-600"></i>Nama Vendor
                </label>
                <input type="text"
                       id="vendor_name"
                       name="vendor_name"
                       value="{{ old('vendor_name', $productIncoming->vendor_name) }}"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Nama vendor/distributor">
                @error('vendor_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end space-x-4 mt-8">
            <a href="{{ route('pembelian.index') }}"
               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit"
                    class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                <i class="fas fa-save mr-2"></i>Update
            </button>
        </div>
    </form>
</div>
@endsection
