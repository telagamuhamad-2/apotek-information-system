@extends('layouts.app')

@section('title', 'Buat Penjualan')

@section('header-title', 'Buat Penjualan')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <a href="{{ route('penjualan.index') }}" class="text-emerald-600 hover:text-emerald-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-plus-circle mr-2 text-emerald-600"></i>Buat Penjualan Baru
        </h2>
    </div>

    <div class="p-6 bg-green-50 border-b border-green-200">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-green-600 mt-1 mr-2"></i>
            <div class="text-sm text-green-800">
                <p class="font-semibold mb-1">Harga Jual Otomatis:</p>
                <ul class="list-disc list-inside ml-2 space-y-1">
                    <li>Harga jual diambil dari harga yang diatur di menu <strong>Stok</strong></li>
                    <li>Untuk mengubah harga jual, edit obat di menu Stok</li>
                    <li>Harga beli (modal) hanya diinput di menu Pembelian</li>
                </ul>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('penjualan.store') }}" id="saleForm" class="p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="product_code" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-barcode mr-2 text-emerald-600"></i>Kode Obat
                </label>
                <div class="flex gap-2">
                    <input type="text"
                           id="product_code"
                           name="product_code"
                           required
                           autofocus
                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Scan atau masukkan kode obat">
                    <button type="button" onclick="searchProduct()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                @error('product_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Product Details (Auto-filled) -->
            <div id="product_details" class="md:col-span-2 p-4 bg-gray-50 rounded-lg hidden">
                <h4 class="font-medium text-gray-800 mb-3"><i class="fas fa-info-circle mr-2 text-emerald-600"></i>Detail Obat</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Nama Obat</p>
                        <p id="detail_name" class="font-medium text-gray-800">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Harga Jual</p>
                        <p id="detail_price" class="font-medium text-emerald-600">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Stok Tersedia</p>
                        <p id="detail_stock" class="font-medium text-gray-800">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Jenis</p>
                        <p id="detail_type" class="font-medium text-gray-800">-</p>
                    </div>
                </div>
                <input type="hidden" id="product_price_hidden" name="product_price_hidden">
            </div>

            <div>
                <label for="product_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-cubes mr-2 text-emerald-600"></i>Jumlah
                </label>
                <input type="number"
                       id="product_quantity"
                       name="product_quantity"
                       value="{{ old('product_quantity', 1) }}"
                       required
                       min="1"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="1">
                @error('product_quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p id="stock_warning" class="mt-1 text-sm text-yellow-600 hidden">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Stok tidak mencukupi
                </p>
            </div>

            <div class="md:col-span-2">
                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user mr-2 text-emerald-600"></i>Nama Pelanggan
                </label>
                <input type="text"
                       id="customer_name"
                       name="customer_name"
                       value="{{ old('customer_name') }}"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Nama pelanggan">
                @error('customer_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Total Calculation Preview -->
        <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200">
            <div class="flex justify-between items-center">
                <p class="text-sm text-green-800">
                    <i class="fas fa-calculator mr-2"></i>Total Penjualan:
                </p>
                <p class="text-xl font-bold text-green-700" id="total_price">0</p>
            </div>
        </div>

        <div class="flex justify-end space-x-4 mt-8">
            <a href="{{ route('penjualan.index') }}"
               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit" id="submitBtn"
                    class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                <i class="fas fa-save mr-2"></i>Simpan
            </button>
        </div>
    </form>
</div>

<script>
    let currentProduct = null;
    let maxStock = 0;

    function searchProduct() {
        const code = document.getElementById('product_code').value.trim();

        if (!code) {
            alert('Masukkan kode obat terlebih dahulu');
            return;
        }

        $.ajax({
            url: '{{ route("penjualan.get-product-details") }}',
            method: 'GET',
            data: { code: code },
            success: function(response) {
                if (response.success) {
                    currentProduct = response.product;
                    maxStock = response.product.stock;

                    // Show product details
                    document.getElementById('product_details').classList.remove('hidden');
                    document.getElementById('detail_name').textContent = response.product.name;
                    document.getElementById('detail_price').textContent = parseFloat(response.product.price).toLocaleString('id-ID');
                    document.getElementById('detail_stock').textContent = response.product.stock + ' pcs';
                    document.getElementById('detail_type').textContent = response.product.type_name || '-';

                    // Check stock
                    checkStock();
                    calculateTotal();
                } else {
                    alert(response.message);
                    document.getElementById('product_details').classList.add('hidden');
                    currentProduct = null;
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mencari obat');
            }
        });
    }

    function checkStock() {
        const quantity = parseInt(document.getElementById('product_quantity').value) || 0;
        const warning = document.getElementById('stock_warning');

        if (currentProduct && quantity > maxStock) {
            warning.classList.remove('hidden');
            warning.textContent = `Stok tidak mencukupi! Tersedia: ${maxStock}`;
            return false;
        } else {
            warning.classList.add('hidden');
            return true;
        }
    }

    function calculateTotal() {
        const quantity = parseInt(document.getElementById('product_quantity').value) || 0;
        const price = currentProduct ? parseFloat(currentProduct.price) : 0;
        const total = quantity * price;
        document.getElementById('total_price').textContent = total.toLocaleString('id-ID');
    }

    // Event listeners
    document.getElementById('product_code').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchProduct();
        }
    });

    document.getElementById('product_quantity').addEventListener('input', function() {
        checkStock();
        calculateTotal();
    });

    // Form submission
    document.getElementById('saleForm').addEventListener('submit', function(e) {
        if (!currentProduct) {
            e.preventDefault();
            alert('Cari obat terlebih dahulu dengan memasukkan kode obat');
            return;
        }

        if (!checkStock()) {
            e.preventDefault();
            return;
        }
    });
</script>
@endsection
