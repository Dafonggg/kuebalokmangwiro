<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin - Kue Mang Wiro')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col flex-grow pt-5 overflow-y-auto bg-white border-r border-gray-200">
                    <div class="flex items-center flex-shrink-0 px-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Kue Balok Mang Wiro" class="h-10 w-auto">
                    </div>
                    <div class="mt-5 flex-grow flex flex-col">
                        <nav class="flex-1 px-2 space-y-1">
                            <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-[#2e4358]/10 text-[#2e4358]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Dashboard
                            </a>
                            <a href="{{ route('admin.categories.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.categories.*') ? 'bg-[#2e4358]/10 text-[#2e4358]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Kategori
                            </a>
                            <a href="{{ route('admin.cashier.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.cashier.*') ? 'bg-[#2e4358]/10 text-[#2e4358]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                Kasir
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.products.*') && !request()->routeIs('admin.products.stock*') ? 'bg-[#2e4358]/10 text-[#2e4358]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Produk
                            </a>
                            <a href="{{ route('admin.packages.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.packages.*') ? 'bg-[#2e4358]/10 text-[#2e4358]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Paket Produk
                            </a>
                            <a href="{{ route('admin.products.stock') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.products.stock*') ? 'bg-[#2e4358]/10 text-[#2e4358]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Kelola Stok
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.orders.*') ? 'bg-[#2e4358]/10 text-[#2e4358]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Pesanan
                            </a>
                            <a href="{{ route('admin.payment-methods.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.payment-methods.*') ? 'bg-[#2e4358]/10 text-[#2e4358]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                Metode Pembayaran
                            </a>
                        </nav>
                    </div>
                    <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="group flex items-center w-full px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                                <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top header -->
            <header class="bg-white shadow-sm">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center lg:hidden">
                                <img src="{{ asset('images/logo.png') }}" alt="Kue Balok Mang Wiro" class="h-8 w-auto">
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </div>
            </header>


            <!-- Page content -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none pb-20 lg:pb-6">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        @if(session('success'))
                            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>
            </main>
        </div>

        <!-- Bottom Navigation (Mobile Only) -->
        <nav id="mobileNav" class="lg:hidden fixed bottom-0 left-0 right-0 z-50 pb-4 px-4">
            <div class="bg-gray-900 border border-gray-800 rounded-xl shadow-lg px-2 py-3">
                <div class="flex justify-around items-center gap-1">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex flex-col items-center justify-center flex-1 min-w-0 px-2 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : 'hover:bg-gray-800' }}">
                        <svg class="h-6 w-6 mb-1.5 flex-shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400' }}" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="text-xs font-semibold leading-tight text-center {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400' }}">Dashboard</span>
                    </a>

                    <!-- Produk -->
                    <a href="{{ route('admin.products.index') }}" 
                       class="flex flex-col items-center justify-center flex-1 min-w-0 px-2 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.products.*') && !request()->routeIs('admin.products.stock*') ? 'bg-gray-700' : 'hover:bg-gray-800' }}">
                        <svg class="h-6 w-6 mb-1.5 flex-shrink-0 {{ request()->routeIs('admin.products.*') && !request()->routeIs('admin.products.stock*') ? 'text-white' : 'text-gray-400' }}" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="text-xs font-semibold leading-tight text-center {{ request()->routeIs('admin.products.*') && !request()->routeIs('admin.products.stock*') ? 'text-white' : 'text-gray-400' }}">Produk</span>
                    </a>

                    <!-- Pesanan -->
                    <a href="{{ route('admin.orders.index') }}" 
                       class="flex flex-col items-center justify-center flex-1 min-w-0 px-2 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.orders.*') ? 'bg-gray-700' : 'hover:bg-gray-800' }}">
                        <svg class="h-6 w-6 mb-1.5 flex-shrink-0 {{ request()->routeIs('admin.orders.*') ? 'text-white' : 'text-gray-400' }}" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="text-xs font-semibold leading-tight text-center {{ request()->routeIs('admin.orders.*') ? 'text-white' : 'text-gray-400' }}">Pesanan</span>
                    </a>

                    <!-- Kasir -->
                    <a href="{{ route('admin.cashier.index') }}" 
                       class="flex flex-col items-center justify-center flex-1 min-w-0 px-2 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.cashier.*') ? 'bg-gray-700' : 'hover:bg-gray-800' }}">
                        <svg class="h-6 w-6 mb-1.5 flex-shrink-0 {{ request()->routeIs('admin.cashier.*') ? 'text-white' : 'text-gray-400' }}" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <span class="text-xs font-semibold leading-tight text-center {{ request()->routeIs('admin.cashier.*') ? 'text-white' : 'text-gray-400' }}">Kasir</span>
                    </a>

                    <!-- Lainnya -->
                    <button onclick="toggleMoreMenu()" 
                            class="flex flex-col items-center justify-center flex-1 min-w-0 px-2 py-2 rounded-lg transition-all duration-200 hover:bg-gray-800">
                        <svg class="h-6 w-6 mb-1.5 flex-shrink-0 text-gray-400" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                        <span class="text-xs font-semibold leading-tight text-center text-gray-400">Lainnya</span>
                    </button>
                </div>
            </div>

            <!-- More Menu Dropdown -->
            <div id="moreMenu" class="hidden absolute bottom-full left-4 right-4 mb-2 bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
                <div class="py-2">
                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center px-4 py-3 transition-colors duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <span class="text-sm font-medium">Kategori</span>
                    </a>
                    <a href="{{ route('admin.packages.index') }}" 
                       class="flex items-center px-4 py-3 transition-colors duration-200 {{ request()->routeIs('admin.packages.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="text-sm font-medium">Paket Produk</span>
                    </a>
                    <a href="{{ route('admin.products.stock') }}" 
                       class="flex items-center px-4 py-3 transition-colors duration-200 {{ request()->routeIs('admin.products.stock*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="text-sm font-medium">Kelola Stok</span>
                    </a>
                    <a href="{{ route('admin.payment-methods.index') }}" 
                       class="flex items-center px-4 py-3 transition-colors duration-200 {{ request()->routeIs('admin.payment-methods.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <span class="text-sm font-medium">Metode Pembayaran</span>
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center px-4 py-3 text-left transition-colors duration-200 text-gray-300 hover:bg-gray-700">
                            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="text-sm font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </div>

    <script>
        function toggleMoreMenu() {
            const menu = document.getElementById('moreMenu');
            menu.classList.toggle('hidden');
        }

        // Close more menu when clicking outside
        document.addEventListener('click', function(event) {
            const moreMenu = document.getElementById('moreMenu');
            const moreButton = event.target.closest('button[onclick="toggleMoreMenu()"]');
            
            if (!moreButton && !moreMenu.contains(event.target)) {
                moreMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>

