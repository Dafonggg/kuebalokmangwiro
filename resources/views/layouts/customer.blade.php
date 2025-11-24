<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kue Mang Wiro')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('menu.index') }}" class="flex items-center">
                        <img src="{{ asset('images/logoke1.png') }}" alt="Kue Balok Mang Wiro" class="h-16 w-auto">
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-700 hover:text-[#2e4358]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        @if(session('cart') && count(session('cart')) > 0)
                            <span class="absolute top-0 right-0 bg-[#2e4358] text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ array_sum(array_column(session('cart'), 'quantity')) }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow">
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
    </main>

    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">© 2025 Kue Mang Wiro. All rights reserved.</div>
    </footer>
</body>
</html>

