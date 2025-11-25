@extends('layouts.customer')

@section('title', 'Menu - Kue Mang Wiro')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900 mb-4 text-center">Menu Kue Balok Mang Wiro</h1>
    
    <div class="flex flex-col md:flex-row gap-4 mb-6 justify-center items-center">
        <div class="w-full md:w-64">
            <input type="text" id="search" placeholder="Cari menu..." 
                   class="w-full px-6 py-3 border border-gray-300 rounded-full focus:ring-2 focus:ring-[#2e4358] focus:border-transparent"
                   value="{{ request('search') }}">
        </div>
        <div class="w-full md:w-64">
            <select id="category" class="w-full px-6 py-3 border border-gray-300 rounded-full focus:ring-2 focus:ring-[#2e4358] focus:border-transparent appearance-none bg-white">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

@if($products->isEmpty() && $packages->isEmpty())
    <div class="text-center py-12">
        <p class="text-gray-500 text-lg">Tidak ada produk ditemukan.</p>
    </div>
@else
    @if($packages->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Paket Spesial</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($packages as $package)
                    <article class="bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-shadow p-6 flex flex-col gap-4 relative">
                        <div class="absolute top-4 left-4 bg-[#2e4358] text-white text-xs font-semibold px-3 py-1.5 rounded-full z-10">
                            Paket
                        </div>
                        
                        <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden">
                            @if($package->photo_url)
                                <img src="{{ asset('storage/' . $package->photo_url) }}" 
                                     alt="{{ $package->name }}" 
                                     class="w-full h-full object-cover"
                                     loading="lazy"
                                     decoding="async">
                            @else
                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex flex-col gap-2 relative pb-12">
                            <h3 class="text-xl font-bold text-gray-900">{{ $package->name }}</h3>
                            @if($package->description)
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $package->description }}</p>
                            @endif
                            <div class="text-xs text-gray-500 mb-2">
                                <p>Isi paket:</p>
                                <ul class="list-disc list-inside">
                                    @foreach($package->items->take(3) as $item)
                                        <li>{{ $item->product->name }} x{{ $item->qty }}</li>
                                    @endforeach
                                    @if($package->items->count() > 3)
                                        <li>+{{ $package->items->count() - 3 }} item lainnya</li>
                                    @endif
                                </ul>
                            </div>
                            <p class="text-xl font-bold text-blue-600">Rp {{ number_format($package->price, 0, ',', '.') }}</p>
                            
                            <form action="{{ route('cart.addPackage', $package) }}" method="POST" class="absolute bottom-0 right-0 add-to-cart-form" data-package-id="{{ $package->id }}">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" 
                                        class="add-to-cart-btn w-11 h-11 md:w-12 md:h-12 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 active:bg-gray-700 transition-colors shadow-md hover:shadow-lg touch-manipulation"
                                        aria-label="Tambah {{ $package->name }} ke keranjang">
                                    <svg class="add-to-cart-icon w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <svg class="add-to-cart-spinner hidden w-5 h-5 md:w-6 md:h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif

    @if($products->isNotEmpty())
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Produk</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
            <article class="bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-shadow p-6 flex flex-col gap-4 relative">
                @if($product->category->stock <= 0)
                    <div class="absolute top-4 right-4 bg-red-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full z-10">
                        Stok Habis
                    </div>
                @endif
                
                <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden">
                    @if($product->photo_url)
                        <img src="{{ asset('storage/' . $product->photo_url) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover"
                             loading="lazy"
                             decoding="async">
                    @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <div class="flex flex-col gap-2 relative pb-12">
                    <h3 class="text-xl font-bold text-gray-900">{{ $product->name }}</h3>
                    <p class="text-xl font-bold text-blue-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-600">
                        Stok: <span class="font-bold">{{ $product->category->stock ?? 0 }}</span>
                    </p>
                    
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="absolute bottom-0 right-0 add-to-cart-form" data-product-id="{{ $product->id }}">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" 
                                class="add-to-cart-btn w-11 h-11 md:w-12 md:h-12 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 active:bg-gray-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed shadow-md hover:shadow-lg disabled:shadow-none touch-manipulation"
                                {{ $product->category->stock <= 0 ? 'disabled' : '' }}
                                aria-label="Tambah {{ $product->name }} ke keranjang">
                            <svg class="add-to-cart-icon w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <svg class="add-to-cart-spinner hidden w-5 h-5 md:w-6 md:h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </article>
                @endforeach
            </div>
        </div>
    @endif
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const categorySelect = document.getElementById('category');
        let searchTimeout;

        // Debounce untuk search input - tunggu 500ms setelah user berhenti mengetik
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    filterProducts();
                }, 500);
            });
        }

        // Filter langsung saat category berubah
        if (categorySelect) {
            categorySelect.addEventListener('change', function() {
                filterProducts();
            });
        }

        function filterProducts() {
            const search = searchInput ? searchInput.value : '';
            const category = categorySelect ? categorySelect.value : '';
            const url = new URL(window.location.href);
            
            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }
            
            if (category) {
                url.searchParams.set('category', category);
            } else {
                url.searchParams.delete('category');
            }
            
            window.location.href = url.toString();
        }
    });

    // Handle AJAX add to cart (for both products and packages)
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.add-to-cart-form');
        
        forms.forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const button = form.querySelector('.add-to-cart-btn');
                const icon = form.querySelector('.add-to-cart-icon');
                const spinner = form.querySelector('.add-to-cart-spinner');
                const formData = new FormData(form);
                const url = form.action;
                
                // Check if button was originally disabled
                const wasDisabled = button.disabled;
                
                // Disable button and show loading
                button.disabled = true;
                icon.classList.add('hidden');
                spinner.classList.remove('hidden');
                
                try {
                    const response = await axios.post(url, formData, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (response.data.success) {
                        showToast(response.data.message, 'success');
                        if (response.data.cart_count !== undefined) {
                            updateCartBadge(response.data.cart_count);
                        }
                    } else {
                        showToast(response.data.message || 'Terjadi kesalahan', 'error');
                    }
                } catch (error) {
                    let errorMessage = 'Terjadi kesalahan saat menambahkan item ke keranjang.';
                    
                    if (error.response && error.response.data && error.response.data.message) {
                        errorMessage = error.response.data.message;
                    } else if (error.message) {
                        errorMessage = error.message;
                    }
                    
                    showToast(errorMessage, 'error');
                    
                    if (error.response && error.response.data && error.response.data.cart_count !== undefined) {
                        updateCartBadge(error.response.data.cart_count);
                    }
                } finally {
                    // Re-enable button only if it wasn't originally disabled, and hide loading
                    if (!wasDisabled) {
                        button.disabled = false;
                    }
                    icon.classList.remove('hidden');
                    spinner.classList.add('hidden');
                }
            });
        });
    });
</script>
@endsection

