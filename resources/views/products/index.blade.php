@extends('layouts.app')

@section('title', 'Stok Obat')

@section('header-title', 'Stok Obat')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center">
                <i class="fas fa-boxes text-2xl text-emerald-600 mr-3"></i>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Daftar Stok Obat</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Stok otomatis dikelola: <span class="text-emerald-600 font-medium">+ Pembelian</span> | <span class="text-red-600 font-medium">- Penjualan</span>
                    </p>
                </div>
            </div>
            <a href="{{ route('products.export', request()->query()) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </a>
        </div>

        <!-- Filters -->
        <div class="mt-6">
            <form method="GET" action="{{ route('products.index') }}" class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari obat..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex-1">
                    <select name="product_type_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Jenis</option>
                        @foreach($productTypes as $type)
                            <option value="{{ $type->id }}" {{ request('product_type_id') == $type->id ? 'selected' : '' }}">
                                {{ $type->product_type_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                    @if(request()->anyFilled(['search', 'product_type_id']))
                        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            <i class="fas fa-times mr-2"></i>Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Quick Filters -->
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('products.index') }}"
                   class="px-3 py-1 {{ empty(request()->except('page')) ? 'bg-blue-100 text-blue-800 border-blue-300' : 'bg-gray-100 text-gray-600 border-gray-300' }} rounded-full text-xs border hover:opacity-80">
                    <i class="fas fa-boxes mr-1"></i>Semua Obat
                </a>
                <a href="{{ route('products.index', ['low_stock' => 1]) }}"
                   class="px-3 py-1 {{ request('low_stock') ? 'bg-yellow-100 text-yellow-800 border-yellow-300' : 'bg-gray-100 text-gray-600 border-gray-300' }} rounded-full text-xs border hover:opacity-80">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Stok Rendah (< 10)
                </a>
                <a href="{{ route('products.index', ['expired' => 1]) }}"
                   class="px-3 py-1 {{ request('expired') ? 'bg-red-100 text-red-800 border-red-300' : 'bg-gray-100 text-gray-600 border-gray-300' }} rounded-full text-xs border hover:opacity-80">
                    <i class="fas fa-clock mr-1"></i>Kadaluwarsa
                </a>
                <a href="{{ route('products.index', ['max_quantity' => 0]) }}"
                   class="px-3 py-1 {{ request()->has('max_quantity') && request('max_quantity') == '0' ? 'bg-red-100 text-red-800 border-red-300' : 'bg-gray-100 text-gray-600 border-gray-300' }} rounded-full text-xs border hover:opacity-80">
                    <i class="fas fa-cube mr-1"></i>Stok Habis (0)
                </a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Obat</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kadaluwarsa</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($products as $product)
                    @php
                        $isLowStock = $product->product_quantity < 10;
                        $isExpired = $product->product_expiration_date && $product->product_expiration_date < now()->format('Y-m-d');
                        $isNearExpired = $product->product_expiration_date && $product->product_expiration_date > now()->format('Y-m-d') && $product->product_expiration_date <= now()->addDays(30)->format('Y-m-d');
                        $sellingPrice = $product->selling_price ?? $product->product_price ?? 0;
                        $purchasePrice = $product->purchase_price ?? $product->product_price ?? 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors {{ $isExpired ? 'bg-red-50' : ($isLowStock ? 'bg-yellow-50' : '') }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->product_code ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-pills text-emerald-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $product->product_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->product_purpose ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($product->productType)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">{{ $product->productType->product_type_name }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($product->product_quantity == 0)
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                    0
                                </span>
                            @elseif($isLowStock)
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                    {{ $product->product_quantity }}
                                </span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                    {{ $product->product_quantity }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($purchasePrice, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <span class="font-semibold">{{ number_format($sellingPrice, 0, ',', '.') }}</span>
                                <form method="POST" action="{{ route('products.update-selling-price', $product->id) }}" class="ml-2">
                                    @csrf
                                    <input type="hidden" name="selling_price" value="{{ $sellingPrice }}" id="selling_price_{{ $product->id }}">
                                    <button type="button" onclick="editSellingPrice({{ $product->id }})" class="text-emerald-600 hover:text-emerald-800 text-xs" title="Edit Harga Jual">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($product->product_expiration_date)
                                @if($isExpired)
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                        {{ $product->product_expiration_date }}
                                    </span>
                                @elseif($isNearExpired)
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs">
                                        {{ $product->product_expiration_date }}
                                    </span>
                                @else
                                    <span class="text-gray-600">{{ $product->product_expiration_date }}</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs">
                                {{ $product->vendor_name  ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('products.edit', $product->id) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus obat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                            <p class="mb-2">Tidak ada obat yang ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="p-6 border-t border-gray-200">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Modal for editing selling price -->
<div id="sellingPriceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-tag mr-2 text-emerald-600"></i>Edit Harga Jual
        </h3>
        <form method="POST" action="{{ route('products.update-selling-price', ':id') }}" id="sellingPriceForm">
            @csrf
            <input type="hidden" name="selling_price" id="modal_selling_price">
            <div>
                <label for="modal_selling_price_input" class="block text-sm font-medium text-gray-700 mb-2">Harga Jual</label>
                <input type="number"
                       id="modal_selling_price_input"
                       name="selling_price"
                       required
                       min="0"
                       step="0.01"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="0">
            </div>
            <div class="mt-4 flex gap-2">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function editSellingPrice(id) {
        const sellingPrice = document.getElementById('selling_price_' + id).value;
        const form = document.getElementById('sellingPriceForm');
        const action = form.getAttribute('action').replace(':id', id);
        form.setAttribute('action', action);
        document.getElementById('modal_selling_price').value = sellingPrice;
        document.getElementById('sellingPriceModal').classList.remove('hidden');
        document.getElementById('sellingPriceModal').classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('sellingPriceModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close modal on outside click
    document.getElementById('sellingPriceModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection
