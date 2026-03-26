@extends('layouts.app')

@section('title', 'Dashboard')

@section('header-title', 'Dashboard')

@section('content')
@php
    $totalProducts = \App\Models\Product::count();
    $totalTypes = \App\Models\ProductType::count();
    $totalPurchases = \App\Models\ProductIncoming::sum('product_total_price');
    $totalSales = \App\Models\ProductOutgoing::sum('product_total_price');
    $lowStock = \App\Models\Product::where('product_quantity', '<', 10)->count();
    $expiredProducts = \App\Models\Product::where('product_expiration_date', '<', now()->format('Y-m-d'))->count();
    $recentSales = \App\Models\ProductOutgoing::with('productType')->orderBy('created_at', 'desc')->limit(5)->get();
    $recentPurchases = \App\Models\ProductIncoming::with('productType')->orderBy('created_at', 'desc')->limit(5)->get();
@endphp

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Products -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-emerald-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Obat</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalProducts }}</p>
            </div>
            <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-pills text-2xl text-emerald-600"></i>
            </div>
        </div>
    </div>

    <!-- Total Types -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Jenis Obat</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalTypes }}</p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-tags text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Stok Rendah</p>
                <p class="text-3xl font-bold text-gray-800">{{ $lowStock }}</p>
            </div>
            <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-2xl text-yellow-600"></i>
            </div>
        </div>
    </div>

    <!-- Expired Products -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Kedaluwarsa</p>
                <p class="text-3xl font-bold text-gray-800">{{ $expiredProducts }}</p>
            </div>
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-2xl text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Total Purchases -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-truck mr-2 text-emerald-600"></i>Total Pembelian
            </h3>
            <span class="text-sm text-gray-500">Semua waktu</span>
        </div>
        <p class="text-3xl font-bold text-emerald-600">{{ number_format($totalPurchases, 0, ',', '.') }}</p>
    </div>

    <!-- Total Sales -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-shopping-cart mr-2 text-blue-600"></i>Total Penjualan
            </h3>
            <span class="text-sm text-gray-500">Semua waktu</span>
        </div>
        <p class="text-3xl font-bold text-blue-600">{{ number_format($totalSales, 0, ',', '.') }}</p>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Sales -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-shopping-bag mr-2 text-blue-600"></i>Penjualan Terbaru
            </h3>
        </div>
        <div class="p-6">
            @if($recentSales->count() > 0)
                <div class="space-y-4">
                    @foreach($recentSales as $sale)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">{{ $sale->product_name }}</p>
                                <p class="text-sm text-gray-500">{{ $sale->customer_name }} &bull; {{ $sale->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-blue-600">{{ number_format($sale->product_total_price, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">{{ $sale->product_quantity }} pcs</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('penjualan.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Lihat semua penjualan <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            @else
                <p class="text-center text-gray-500 py-4">Belum ada penjualan</p>
            @endif
        </div>
    </div>

    <!-- Recent Purchases -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-truck-loading mr-2 text-emerald-600"></i>Pembelian Terbaru
            </h3>
        </div>
        <div class="p-6">
            @if($recentPurchases->count() > 0)
                <div class="space-y-4">
                    @foreach($recentPurchases as $purchase)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">{{ $purchase->product_name }}</p>
                                <p class="text-sm text-gray-500">{{ $purchase->vendor_name }} &bull; {{ $purchase->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-emerald-600">{{ number_format($purchase->product_total_price, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">{{ $purchase->product_quantity }} pcs</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('pembelian.index') }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">
                        Lihat semua pembelian <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            @else
                <p class="text-center text-gray-500 py-4">Belum ada pembelian</p>
            @endif
        </div>
    </div>
</div>
@endsection
