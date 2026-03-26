@extends('layouts.app')

@section('title', 'Tambah Jenis Obat')

@section('header-title', 'Tambah Jenis Obat')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <a href="{{ route('product-types.index') }}" class="text-emerald-600 hover:text-emerald-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-plus-circle mr-2 text-emerald-600"></i>Tambah Jenis Obat Baru
        </h2>
    </div>

    <form method="POST" action="{{ route('product-types.store') }}" class="p-6">
        @csrf

        <div class="mb-6">
            <label for="product_type_name" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-tag mr-2 text-emerald-600"></i>Nama Jenis Obat
            </label>
            <input type="text"
                   id="product_type_name"
                   name="product_type_name"
                   value="{{ old('product_type_name') }}"
                   required
                   autofocus
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                   placeholder="Contoh: Tablet, Sirup, Kapsul, dll">
            @error('product_type_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('product-types.index') }}"
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
