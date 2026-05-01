@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('header-title', 'Tambah Pengguna')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <a href="{{ route('users.index') }}" class="text-emerald-600 hover:text-emerald-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-plus-circle mr-2 text-emerald-600"></i>Tambah Pengguna Baru
        </h2>
    </div>

    <form method="POST" action="{{ route('users.store') }}" class="p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user mr-2 text-emerald-600"></i>Nama Lengkap
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       autofocus
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Nama lengkap">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user mr-2 text-emerald-600"></i>Username
                </label>
                <input type="text"
                       id="username"
                       name="username"
                       value="{{ old('username') }}"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Username untuk login">
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-emerald-600"></i>Password
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Minimal 8 karakter">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-emerald-600"></i>Konfirmasi Password
                </label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Ulangi password">
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user-tag mr-2 text-emerald-600"></i>Role
                </label>
                <select id="role"
                        name="role"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Pilih Role</option>
                    <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>
                        <i class="fas fa-crown mr-1"></i>Owner
                    </option>
                    <option value="pegawai" {{ old('role') == 'pegawai' ? 'selected' : '' }}>
                        <i class="fas fa-user-tie mr-1"></i>Pegawai
                    </option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Owner:</strong> Akses penuh ke semua menu termasuk manajemen user.<br>
                        <strong>Pegawai:</strong> Hanya akses menu penjualan.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4 mt-8">
            <a href="{{ route('users.index') }}"
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
