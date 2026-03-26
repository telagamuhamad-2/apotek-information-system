@php
    $user = auth()->user();
    $isOwner = $user->hasRole('owner');
@endphp

<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-emerald-700 to-emerald-900 text-white transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 border-b border-emerald-600">
            <div class="flex items-center space-x-2">
                <i class="fas fa-pills text-2xl text-emerald-300"></i>
                <span class="text-xl font-bold text-white">Apotek System</span>
            </div>
        </div>

        <!-- User Info -->
        <div class="px-6 py-4 border-b border-emerald-600">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center">
                    <i class="fas fa-user text-emerald-200"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-white">{{ $user->name }}</p>
                    <p class="text-xs text-emerald-300">{{ ucfirst($user->getRoleNames()->first()) }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}"
               class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}">
                <i class="fas fa-home w-5"></i>
                <span class="ml-3">Dashboard</span>
            </a>

            @if($isOwner)
                <a href="{{ route('penjualan.index') }}"
                   class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('penjualan.*') ? 'bg-white/10 text-white' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span class="ml-3">Penjualan</span>
                </a>

                <a href="{{ route('pembelian.index') }}"
                   class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('pembelian.*') ? 'bg-white/10 text-white' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-truck w-5"></i>
                    <span class="ml-3">Pembelian</span>
                </a>

                <a href="{{ route('products.index') }}"
                   class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('products.*') ? 'bg-white/10 text-white' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-boxes w-5"></i>
                    <span class="ml-3">Stok Obat</span>
                </a>

                <a href="{{ route('product-types.index') }}"
                   class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('product-types.*') ? 'bg-white/10 text-white' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-tags w-5"></i>
                    <span class="ml-3">Jenis Obat</span>
                </a>

                <a href="{{ route('users.index') }}"
                   class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('users.*') ? 'bg-white/10 text-white' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-users w-5"></i>
                    <span class="ml-3">Manajemen User</span>
                </a>
            @else
                <!-- Pegawai only sees Penjualan -->
                <a href="{{ route('penjualan.index') }}"
                   class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('penjualan.*') ? 'bg-white/10 text-white' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span class="ml-3">Penjualan</span>
                </a>
            @endif
        </nav>

        <!-- Logout -->
        <div class="p-4 border-t border-emerald-600">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full flex items-center px-4 py-3 rounded-lg text-emerald-100 hover:bg-white/10 hover:text-white transition-colors duration-200">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span class="ml-3">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
