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
                <!-- Transaksi Collapsible -->
                <div class="space-y-1">
                    <button type="button"
                            onclick="toggleTransaksi()"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-emerald-100 hover:bg-white/10 hover:text-white transition-colors duration-200 focus:outline-none {{ request()->routeIs(['penjualan.*', 'pembelian.*']) ? 'bg-white/5' : '' }}">
                        <div class="flex items-center">
                            <i class="fas fa-exchange-alt w-5"></i>
                            <span class="ml-3">Transaksi</span>
                        </div>
                        <i id="transaksi-chevron" class="fas fa-chevron-down text-xs transition-transform duration-200 {{ request()->routeIs(['penjualan.*', 'pembelian.*']) ? 'rotate-180' : '' }}"></i>
                    </button>

                    <div id="transaksi-menu" class="{{ request()->routeIs(['penjualan.*', 'pembelian.*']) ? 'block' : 'hidden' }} pl-11 space-y-1">
                        <a href="{{ route('penjualan.index') }}"
                           class="flex items-center px-4 py-2 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('penjualan.*') ? 'text-white font-medium' : 'text-emerald-200 hover:text-white' }}">
                            <span>Penjualan</span>
                        </a>

                        <a href="{{ route('pembelian.index') }}"
                           class="flex items-center px-4 py-2 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('pembelian.*') ? 'text-white font-medium' : 'text-emerald-200 hover:text-white' }}">
                            <span>Pembelian</span>
                        </a>
                    </div>
                </div>

                <!-- Master Data Collapsible -->
                <div class="space-y-1">
                    <button type="button"
                            onclick="toggleMasterData()"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-emerald-100 hover:bg-white/10 hover:text-white transition-colors duration-200 focus:outline-none {{ request()->routeIs(['products.*', 'product-types.*', 'users.*']) ? 'bg-white/5' : '' }}">
                        <div class="flex items-center">
                            <i class="fas fa-database w-5"></i>
                            <span class="ml-3">Master Data</span>
                        </div>
                        <i id="master-data-chevron" class="fas fa-chevron-down text-xs transition-transform duration-200 {{ request()->routeIs(['products.*', 'product-types.*', 'users.*']) ? 'rotate-180' : '' }}"></i>
                    </button>

                    <div id="master-data-menu" class="{{ request()->routeIs(['products.*', 'product-types.*', 'users.*']) ? 'block' : 'hidden' }} pl-11 space-y-1">
                        <a href="{{ route('products.index') }}"
                           class="flex items-center px-4 py-2 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('products.*') ? 'text-white font-medium' : 'text-emerald-200 hover:text-white' }}">
                            <span>Stok Obat</span>
                        </a>

                        <a href="{{ route('product-types.index') }}"
                           class="flex items-center px-4 py-2 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('product-types.*') ? 'text-white font-medium' : 'text-emerald-200 hover:text-white' }}">
                            <span>Jenis Obat</span>
                        </a>

                        <a href="{{ route('users.index') }}"
                           class="flex items-center px-4 py-2 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('users.*') ? 'text-white font-medium' : 'text-emerald-200 hover:text-white' }}">
                            <span>Manajemen User</span>
                        </a>
                    </div>
                </div>
            @else
                <!-- Pegawai only sees Penjualan -->
                <a href="{{ route('penjualan.index') }}"
                   class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('penjualan.*') ? 'bg-white/10 text-white' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span class="ml-3">Penjualan</span>
                </a>
            @endif
        </nav>

        <script>
            function toggleMasterData() {
                const menu = document.getElementById('master-data-menu');
                const chevron = document.getElementById('master-data-chevron');

                if (menu.classList.contains('hidden')) {
                    menu.classList.remove('hidden');
                    chevron.classList.add('rotate-180');
                } else {
                    menu.classList.add('hidden');
                    chevron.classList.remove('rotate-180');
                }
            }

            function toggleTransaksi() {
                const menu = document.getElementById('transaksi-menu');
                const chevron = document.getElementById('transaksi-chevron');

                if (menu.classList.contains('hidden')) {
                    menu.classList.remove('hidden');
                    chevron.classList.add('rotate-180');
                } else {
                    menu.classList.add('hidden');
                    chevron.classList.remove('rotate-180');
                }
            }
        </script>

        <!-- Logout -->
        <div class="p-4 border-t border-emerald-600">
            <button type="button"
                    onclick="document.getElementById('logout-modal').classList.remove('hidden')"
                    class="w-full flex items-center px-4 py-3 rounded-lg text-emerald-100 hover:bg-white/10 hover:text-white transition-colors duration-200">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span class="ml-3">Logout</span>
            </button>
        </div>
    </div>
</aside>

<!-- Logout Confirmation Modal -->
<div id="logout-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
        <div class="flex items-center justify-center w-14 h-14 bg-red-100 rounded-full mx-auto mb-4">
            <i class="fas fa-sign-out-alt text-2xl text-red-600"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 text-center mb-2">Konfirmasi Logout</h3>
        <p class="text-sm text-gray-500 text-center mb-6">Apakah Anda yakin ingin keluar dari sistem?</p>
        <div class="flex gap-3">
            <button type="button"
                    onclick="document.getElementById('logout-modal').classList.add('hidden')"
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                Batal
            </button>
            <form action="{{ route('logout') }}" method="POST" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    Ya, Logout
                </button>
            </form>
        </div>
    </div>
</div>
