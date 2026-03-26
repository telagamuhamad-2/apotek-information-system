@extends('layouts.app')

@section('title', 'Edit Obat')

@section('header-title', 'Edit Obat')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <a href="{{ route('products.index') }}" class="text-emerald-600 hover:text-emerald-800 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
    </a>
    <h2 class="text-xl font-semibold text-gray-800">
        <i class="fas fa-edit mr-2 text-emerald-600"></i>Edit Obat
    </h2>

    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <p class="text-sm text-blue-800">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Info:</strong> Klik tombol Edit di sebelah harga untuk mengubah harga jual/beli secara terpisah.
        </p>
    </div>

    <form method="POST" action="{{ route('products.update', $product->id) }}" id="mainForm" class="mt-6">
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
                       value="{{ old('product_code', $product->product_code) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Contoh: OB001"
                       readonly>
                <p class="mt-1 text-xs text-gray-500">Kode obat tidak dapat diubah</p>
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
                       value="{{ old('product_name', $product->product_name) }}"
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
                        <option value="{{ $type->id }}" {{ old('product_type_id', $product->product_type_id) == $type->id ? 'selected' : '' }}">
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
                       value="{{ old('product_purpose', $product->product_purpose) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Contoh: Demam, Batuk, dll">
                @error('product_purpose')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Harga Beli -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-money-bill mr-2 text-blue-600"></i>Harga Beli (Modal)
                    </label>
                </div>
                <div class="relative">
                    <input type="number"
                           id="purchase_price"
                           name="purchase_price"
                           value="{{ old('purchase_price', $product->purchase_price ?? $product->product_price ?? 0) }}"
                           min="0"
                           step="0.01"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0">
                    <button type="button" onclick="editPurchasePrice()" class="absolute right-0 top-1/2 translate-y-1/2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Harga beli saat pembelian (otomatis update dari pembelian)
                </p>
                @error('purchase_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Harga Jual -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="selling_price" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-money-bill mr-2 text-emerald-600"></i>Harga Jual (ke Pelanggan)
                    </label>
                </div>
                <div class="relative">
                    <input type="number"
                           id="selling_price"
                           name="selling_price"
                           value="{{ old('selling_price', $product->selling_price ?? $product->product_price ?? 0) }}"
                           min="0"
                           step="0.01"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="0">
                    <button type="button" onclick="editSellingPrice()" class="absolute right-0 top-1/2 translate-y-1/2 px-3 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Harga jual saat penjualan
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
                       value="{{ old('product_expiration_date', $product->product_expiration_date ? $product->product_expiration_date->format('Y-m-d') : '') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                @error('product_expiration_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Stock Display (Read Only) -->
        <div class="md:col-span-2 p-4 bg-gray-50 border border-gray-200 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-cubes mr-2 text-emerald-600"></i>Stok Saat Ini
            </label>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <span class="text-4xl font-bold {{ $product->product_quantity == 0 ? 'text-red-600' : 'text-emerald-600' }}">
                        {{ $product->product_quantity }}
                    </span>
                    <span class="text-sm ml-2 {{ $product->product_quantity == 0 ? 'text-gray-500' : 'text-gray-600' }}">
                        pcs
                    </span>
                </div>
            </div>
            @if($product->product_quantity == 0)
                <div class="text-sm text-yellow-600 bg-yellow-100 p-2 rounded">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Belum ada stok! Tambah lewat menu Pembelian.
                </div>
            @endif
            <div class="border-t border-gray-300 pt-4 mt-4">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-arrow-up mr-1 text-blue-600"></i>
                    <strong>Harga Beli:</strong> {{ number_format($product->purchase_price ?? $product->product_price ?? 0, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500">
                    <i class="fas fa-arrow-down mr-1 text-red-600"></i>
                    <strong>Harga Jual:</strong> {{ number_format($product->selling_price ?? $product->product_price ?? 0, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Stok otomatis dikelola melalui menu Pembelian (+) dan Penjualan (-)
                </p>
            </div>
        </div>

        <div class="flex justify-end space-x-4 mt-8">
            <a href="{{ route('products.index') }}"
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

<!-- Hidden Forms for Price Updates -->
<form id="purchasePriceForm" method="POST" action="{{ route('products.update-purchase-price', $product->id) }}" style="display: none;">
    @csrf
    <input type="hidden" name="purchase_price" id="hidden_purchase_price">
</form>

<form id="sellingPriceForm" method="POST" action="{{ route('products.update-selling-price', $product->id) }}" style="display: none;">
    @csrf
    <input type="hidden" name="selling_price" id="hidden_selling_price">
</form>

<script>
    function editPurchasePrice() {
        const currentPrice = document.getElementById('purchase_price').value;
        const newPrice = prompt('Masukkan harga beli baru:', currentPrice);

        if (newPrice !== null && newPrice !== '' && parseFloat(newPrice) !== currentPrice) {
            const form = document.getElementById('purchasePriceForm');
            const input = document.getElementById('hidden_purchase_price');
            input.value = newPrice;
            form.submit();
        }
    }

    function editSellingPrice() {
        const currentPrice = document.getElementById('selling_price').value;
        const newPrice = prompt('Masukkan harga jual baru:', currentPrice);

        if (newPrice !== null && newPrice !== '' && parseFloat(newPrice) !== currentPrice) {
            const form = document.getElementById('sellingPriceForm');
            const input = document.getElementById('hidden_selling_price');
            input.value = newPrice;
            form.submit();
        }
    }
</script>
@endsection
