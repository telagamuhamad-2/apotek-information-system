@extends('layouts.app')

@section('title', 'Dashboard')

@section('header-title', 'Dashboard')

@section('content')
@php
    $todaySales = \App\Models\ProductOutgoing::whereDate('created_at', today())->get();
    $todayTotal = $todaySales->sum('product_total_price');
    $todayCount = $todaySales->count();
    $monthSales = \App\Models\ProductOutgoing::whereMonth('created_at', now()->month)->get();
    $monthTotal = $monthSales->sum('product_total_price');
    $recentSales = \App\Models\ProductOutgoing::where('last_updated_by', auth()->id())
        ->with('productType')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
@endphp

<!-- Welcome Card -->
<div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl shadow-lg p-8 mb-8 text-white">
    <h2 class="text-2xl font-bold mb-2">
        <i class="fas fa-hand-wave mr-2"></i>Selamat Datang, {{ auth()->user()->name }}!
    </h2>
    <p class="text-emerald-100">Berikut ringkasan penjualan hari ini.</p>
</div>

<!-- Today's Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Today's Sales Count -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Transaksi Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800">{{ $todayCount }}</p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-receipt text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Today's Revenue -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-emerald-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pendapatan Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($todayTotal, 0, ',', '.') }}</p>
            </div>
            <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-2xl text-emerald-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Month Stats -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-chart-line mr-2 text-emerald-600"></i>Pendapatan Bulan Ini
        </h3>
        <span class="text-sm text-gray-500">{{ now()->format('F Y') }}</span>
    </div>
    <p class="text-4xl font-bold text-emerald-600">{{ number_format($monthTotal, 0, ',', '.') }}</p>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <a href="{{ route('penjualan.create') }}"
       class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 text-white hover:from-blue-600 hover:to-blue-700 transition-all transform hover:-translate-y-1">
        <div class="flex items-center">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-plus-circle text-3xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold">Transaksi Baru</h3>
                <p class="text-blue-100">Buat penjualan obat</p>
            </div>
        </div>
    </a>

    <a href="{{ route('penjualan.index') }}"
       class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl shadow-sm p-6 text-white hover:from-emerald-600 hover:to-emerald-700 transition-all transform hover:-translate-y-1">
        <div class="flex items-center">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-history text-3xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold">Riwayat Penjualan</h3>
                <p class="text-emerald-100">Lihat semua transaksi</p>
            </div>
        </div>
    </a>
</div>

<!-- Recent Sales -->
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-clock mr-2 text-blue-600"></i>Penjualan Terbaru Anda
        </h3>
    </div>
    <div class="p-6">
        @if($recentSales->count() > 0)
            <div class="space-y-4">
                @foreach($recentSales as $sale)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">{{ $sale->product_name }}</p>
                            <p class="text-sm text-gray-500">{{ $sale->customer_name }} &bull; {{ $sale->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-blue-600">{{ number_format($sale->product_total_price, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">{{ $sale->product_quantity }} pcs</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 py-4">Belum ada penjualan</p>
        @endif
    </div>
</div>
@endsection
