<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center">
            <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="ml-4 text-xl font-semibold text-gray-800">
                @yield('header-title', 'Dashboard')
            </h1>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Date/Time -->
            <div class="hidden sm:block text-sm text-gray-600">
                <i class="fas fa-calendar-alt mr-2"></i>
                {{ now()->format('l, d F Y') }}
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-3">
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->getRoleNames()->first()) }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-user text-emerald-600"></i>
                </div>
            </div>
        </div>
    </div>
</header>
