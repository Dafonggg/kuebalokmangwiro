@extends('layouts.admin')

@section('title', 'Kasir - Admin')

@section('content')
<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 mb-4">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Kasir</h1>
        <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <!-- Cart Toggle Button (Mobile) -->
            <button onclick="toggleCart()" 
                    class="lg:hidden bg-[#2e4358] text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-[#1a2a3a] transition-all flex items-center gap-2 relative text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="hidden sm:inline">Keranjang</span>
                <span id="headerCartBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
            </button>
            <button onclick="clearCart()" class="text-xs sm:text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span>Reset</span>
            </button>
        </div>
    </div>

    <div class="relative" style="min-height: calc(100vh - 250px);">
        <!-- Mobile Cart Toggle Button -->
        <button id="cartToggleBtn" onclick="toggleCart()" 
                class="lg:hidden fixed bottom-6 right-6 z-40 bg-[#2e4358] text-white p-4 rounded-full shadow-lg hover:bg-[#1a2a3a] transition-all">
            <svg id="cartIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <svg id="closeIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span id="cartBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
        </button>

        <!-- Mobile Cart Overlay -->
        <div id="cartOverlay" onclick="toggleCart()" 
             class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-20 hidden transition-opacity"></div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
            <!-- Product Selection Panel -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md overflow-hidden flex flex-col min-h-0">
                <!-- Search Bar -->
                <div class="flex-shrink-0 p-3 sm:p-4 border-b">
                    <input type="text" id="productSearch" placeholder="Cari produk..." 
                           class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2e4358] focus:border-[#2e4358]"
                           onkeyup="filterProducts()">
                </div>

                <!-- Category Tabs -->
                <div class="flex-shrink-0 px-3 sm:px-4 py-2 border-b bg-gray-50 overflow-x-auto">
                    <div class="flex space-x-2 min-w-max" id="categoryTabs">
                        <button onclick="selectCategory('all')" 
                                class="category-tab px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap bg-[#2e4358] text-white" 
                                data-category="all">
                            Semua
                        </button>
                        <button onclick="selectCategory('package')" 
                                class="category-tab px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap bg-white text-gray-700 hover:bg-gray-100 border border-gray-300" 
                                data-category="package">
                            Paket
                        </button>
                        @foreach($categories as $category)
                            <button onclick="selectCategory({{ $category->id }})" 
                                    class="category-tab px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap bg-white text-gray-700 hover:bg-gray-100 border border-gray-300" 
                                    data-category="{{ $category->id }}">
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Products Grid - Scrollable -->
                <div class="flex-1 overflow-y-auto overflow-x-hidden p-3 sm:p-4 min-h-0">
                    <div id="productsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3">
                    @foreach($packages as $package)
                        <div class="product-card package-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow relative cursor-pointer" 
                             data-category="package"
                             data-name="{{ strtolower($package->name) }}"
                             data-stock="999"
                             data-category-id="package"
                             onclick="addPackageToCart({{ $package->id }}, {{ json_encode($package->name) }}, {{ $package->price }}, {{ json_encode($package->photo_url) }}, {{ $package->items->count() }})">
                            <div class="absolute top-2 right-2 bg-[#2e4358] text-white text-xs font-semibold px-2 py-1 rounded z-10">
                                Paket
                            </div>
                            
                            <div class="p-2 sm:p-3">
                                @if($package->photo_url)
                                    <img src="{{ asset('storage/' . $package->photo_url) }}" alt="{{ $package->name }}" 
                                         class="w-full h-24 sm:h-32 object-cover rounded-lg bg-white">
                                @else
                                    <div class="w-full h-24 sm:h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="px-2 sm:px-3 pb-2 sm:pb-3 relative">
                                <h3 class="text-xs sm:text-sm font-bold text-gray-900 mb-1 line-clamp-2 min-h-[2.5rem] sm:min-h-0">{{ $package->name }}</h3>
                                <p class="text-xs sm:text-sm font-bold text-[#2e4358] mb-1">Rp {{ number_format($package->price, 0, ',', '.') }}</p>
                                <div class="text-xs text-gray-600 mb-2 space-y-0.5">
                                    <p class="font-semibold text-gray-700">{{ $package->items->count() }} item:</p>
                                    <ul class="list-disc list-inside ml-1 space-y-0.5 max-h-16 overflow-y-auto">
                                        @foreach($package->items->take(3) as $item)
                                            <li class="truncate">{{ $item->product->name }} x{{ $item->qty }}</li>
                                        @endforeach
                                        @if($package->items->count() > 3)
                                            <li class="text-gray-500">+{{ $package->items->count() - 3 }} lainnya</li>
                                        @endif
                                    </ul>
                                </div>
                                
                                <div class="absolute bottom-2 sm:bottom-3 right-2 sm:right-3 w-8 h-8 sm:w-10 sm:h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition-colors">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @foreach($products as $product)
                        <div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow relative {{ $product->category->stock <= 0 ? 'opacity-60' : 'cursor-pointer' }}" 
                             data-category="{{ $product->category_id }}"
                             data-name="{{ strtolower($product->name) }}"
                             data-stock="{{ $product->category->stock ?? 0 }}"
                             data-category-id="{{ $product->category_id }}"
                             onclick="{{ $product->category->stock > 0 ? 'addToCart(' . $product->id . ', ' . json_encode($product->name) . ', ' . $product->price . ', ' . json_encode($product->photo_url) . ', ' . ($product->category->stock ?? 0) . ', ' . $product->category_id . ')' : '' }}">
                            @if($product->category->stock <= 0)
                                <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded z-10">
                                    Stok Habis
                                </div>
                            @endif
                            
                            <div class="p-2 sm:p-3">
                                @if($product->photo_url)
                                    <img src="{{ asset('storage/' . $product->photo_url) }}" alt="{{ $product->name }}" 
                                         class="w-full h-24 sm:h-32 object-cover rounded-lg bg-white">
                                @else
                                    <div class="w-full h-24 sm:h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="px-2 sm:px-3 pb-2 sm:pb-3 relative">
                                <h3 class="text-xs sm:text-sm font-bold text-gray-900 mb-1 line-clamp-2 min-h-[2.5rem] sm:min-h-0">{{ $product->name }}</h3>
                                <p class="text-xs sm:text-sm font-bold text-[#2e4358] mb-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-900 mb-2">Stok: {{ $product->category->stock ?? 0 }}</p>
                                
                                @if($product->category->stock > 0)
                                    <div class="absolute bottom-2 sm:bottom-3 right-2 sm:right-3 w-8 h-8 sm:w-10 sm:h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition-colors">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Cart Panel -->
        <div id="cartPanel" class="lg:col-span-1 bg-white rounded-none lg:rounded-lg shadow-md overflow-hidden flex flex-col
            fixed lg:relative top-0 right-0 h-screen lg:h-auto w-full sm:w-80 lg:w-auto max-w-sm lg:max-w-none z-30 lg:z-auto
            transform translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            
            <!-- Cart Header -->
            <div class="flex-shrink-0 p-4 border-b flex justify-between items-center bg-white">
                <h2 class="text-lg font-semibold text-gray-900">Keranjang</h2>
                <button onclick="toggleCart()" class="lg:hidden text-gray-500 hover:text-gray-700 p-1">
                </button>
            </div>

            <!-- Scrollable Content Container -->
            <div class="flex-1 overflow-y-auto overflow-x-hidden flex flex-col min-h-0">
                <!-- Customer Info Form -->
                <div class="flex-shrink-0 p-4 border-b bg-gray-50">
                    <form id="orderForm">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Pemesan *</label>
                                <input type="text" id="customer_name" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-[#2e4358] focus:border-[#2e4358]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tipe Pesanan *</label>
                                <select id="order_type" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-[#2e4358] focus:border-[#2e4358]"
                                        onchange="toggleTableNumber()">
                                    <option value="">Pilih Tipe</option>
                                    <option value="dine-in">Dine In</option>
                                    <option value="takeaway">Takeaway</option>
                                    <option value="pickup">Pick Up</option>
                                </select>
                            </div>
                            <div id="tableNumberField" style="display: none;">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Meja</label>
                                <input type="text" id="table_number"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-[#2e4358] focus:border-[#2e4358]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Metode Pembayaran *</label>
                                <select id="payment_method" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-[#2e4358] focus:border-[#2e4358]">
                                    <option value="">Pilih Metode</option>
                                    <option value="cash">Tunai</option>
                                    <option value="qris">QRIS</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                                <textarea id="notes" rows="2"
                                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-[#2e4358] focus:border-[#2e4358] resize-none"
                                          placeholder="Tambahkan catatan khusus..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Cart Items - Scrollable -->
                <div class="p-4">
                    <div id="cartItems" class="space-y-2">
                        <p class="text-center text-gray-500 text-sm py-8">Keranjang kosong</p>
                    </div>
                </div>
            </div>

            <!-- Cart Summary - Fixed at Bottom -->
            <div class="flex-shrink-0 p-4 border-t bg-white shadow-lg lg:shadow-none">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-medium text-gray-700">Total:</span>
                    <span id="cartTotal" class="text-xl font-bold text-[#2e4358]">Rp 0</span>
                </div>
                <button onclick="checkout()" 
                        id="checkoutBtn"
                        disabled
                        class="w-full bg-[#2e4358] text-white py-3 rounded-lg hover:bg-[#1a2a3a] font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors">
                    Buat Pesanan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let selectedCategory = 'all';
    let cartOpen = false;
    const packages = @json($packages);

    function toggleCart() {
        const cartPanel = document.getElementById('cartPanel');
        const cartOverlay = document.getElementById('cartOverlay');
        const cartIcon = document.getElementById('cartIcon');
        const closeIcon = document.getElementById('closeIcon');
        const mobileNav = document.getElementById('mobileNav');
        
        cartOpen = !cartOpen;
        
        if (cartOpen) {
            cartPanel.classList.remove('translate-x-full');
            cartOverlay.classList.remove('hidden');
            cartIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
            // Hide mobile nav when cart is open
            if (mobileNav) {
                mobileNav.style.display = 'none';
            }
        } else {
            cartPanel.classList.add('translate-x-full');
            cartOverlay.classList.add('hidden');
            cartIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
            // Show mobile nav when cart is closed
            if (mobileNav) {
                mobileNav.style.display = '';
            }
        }
    }

    function selectCategory(categoryId) {
        selectedCategory = categoryId;
        
        // Update tab styles
        document.querySelectorAll('.category-tab').forEach(tab => {
            if (tab.dataset.category == categoryId) {
                tab.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                tab.classList.add('bg-[#2e4358]', 'text-white');
            } else {
                tab.classList.remove('bg-[#2e4358]', 'text-white');
                tab.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
            }
        });

        filterProducts();
    }

    function filterProducts() {
        const searchTerm = document.getElementById('productSearch').value.toLowerCase();
        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach(card => {
            const category = card.dataset.category;
            const name = card.dataset.name;
            let matchesCategory = false;
            
            if (selectedCategory === 'all') {
                matchesCategory = true;
            } else if (selectedCategory === 'package') {
                matchesCategory = category === 'package';
            } else {
                matchesCategory = category == selectedCategory;
            }
            
            const matchesSearch = name.includes(searchTerm);

            if (matchesCategory && matchesSearch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function addPackageToCart(packageId, packageName, price, photoUrl, itemCount) {
        const existingItem = cart.find(item => item.item_type === 'package' && item.package_id === packageId);
        
        if (existingItem) {
            existingItem.quantity += 1;
            existingItem.subtotal = existingItem.quantity * existingItem.price;
        } else {
            cart.push({
                item_type: 'package',
                package_id: packageId,
                name: packageName,
                price: price,
                quantity: 1,
                subtotal: price,
                photo_url: photoUrl,
                item_count: itemCount
            });
        }

        updateCartDisplay();
    }

    function addToCart(productId, productName, price, photoUrl, stock, categoryId) {
        // Check stock availability
        if (stock <= 0) {
            alert('Produk sedang tidak tersedia (stok habis).');
            return;
        }
        
        // Calculate total quantity for this category in cart
        let categoryTotalQuantity = 0;
        cart.forEach(item => {
            if (item.item_type === 'product' && item.category_id === categoryId) {
                categoryTotalQuantity += item.quantity;
            } else if (item.item_type === 'package') {
                // Check packages for products in this category
                const package = packages.find(p => p.id === item.package_id);
                if (package) {
                    package.items.forEach(pkgItem => {
                        if (pkgItem.product.category_id === categoryId) {
                            categoryTotalQuantity += item.quantity * pkgItem.qty;
                        }
                    });
                }
            }
        });
        
        const existingItem = cart.find(item => item.item_type === 'product' && item.product_id === productId);
        
        if (existingItem) {
            const newQuantity = existingItem.quantity + 1;
            const newCategoryTotal = categoryTotalQuantity + 1;
            if (newCategoryTotal > stock) {
                alert('Stok tidak cukup untuk kategori. Stok tersedia: ' + stock);
                return;
            }
            existingItem.quantity = newQuantity;
            existingItem.subtotal = existingItem.quantity * existingItem.price;
            existingItem.stock = stock; // Update stock value
        } else {
            const newCategoryTotal = categoryTotalQuantity + 1;
            if (newCategoryTotal > stock) {
                alert('Stok tidak cukup untuk kategori. Stok tersedia: ' + stock);
                return;
            }
            cart.push({
                item_type: 'product',
                product_id: productId,
                name: productName,
                price: price,
                quantity: 1,
                subtotal: price,
                photo_url: photoUrl,
                stock: stock,
                category_id: categoryId
            });
        }

        updateCartDisplay();
    }

    function removeFromCart(itemId, itemType) {
        if (itemType === 'package') {
            cart = cart.filter(item => !(item.item_type === 'package' && item.package_id === itemId));
        } else {
            cart = cart.filter(item => !(item.item_type === 'product' && item.product_id === itemId));
        }
        updateCartDisplay();
    }

    function updateQuantity(itemId, change, itemType) {
        let item;
        if (itemType === 'package') {
            item = cart.find(item => item.item_type === 'package' && item.package_id === itemId);
        } else {
            item = cart.find(item => item.item_type === 'product' && item.product_id === itemId);
        }
        
        if (item) {
            const newQuantity = item.quantity + change;
            if (newQuantity <= 0) {
                removeFromCart(itemId, itemType);
                return;
            }
            
            if (itemType === 'product') {
                // Calculate total quantity for this category in cart
                let categoryTotalQuantity = 0;
                cart.forEach(cartItem => {
                    if (cartItem.item_type === 'product' && cartItem.category_id === item.category_id) {
                        categoryTotalQuantity += cartItem.quantity;
                    } else if (cartItem.item_type === 'package') {
                        // Check packages for products in this category
                        const package = packages.find(p => p.id === cartItem.package_id);
                        if (package) {
                            package.items.forEach(pkgItem => {
                                if (pkgItem.product.category_id === item.category_id) {
                                    categoryTotalQuantity += cartItem.quantity * pkgItem.qty;
                                }
                            });
                        }
                    }
                });
                
                // Adjust for the current item's quantity change
                const newCategoryTotal = categoryTotalQuantity - item.quantity + newQuantity;
                
                // Check stock availability for category
                if (item.stock && newCategoryTotal > item.stock) {
                    alert('Stok tidak cukup untuk kategori. Stok tersedia: ' + item.stock);
                    return;
                }
            }
            
            item.quantity = newQuantity;
            item.subtotal = item.quantity * item.price;
            updateCartDisplay();
        }
    }

    function updateCartDisplay() {
        const cartItemsDiv = document.getElementById('cartItems');
        const cartTotalSpan = document.getElementById('cartTotal');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const cartBadge = document.getElementById('cartBadge');
        const headerCartBadge = document.getElementById('headerCartBadge');

        // Update cart badges
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        if (totalItems > 0) {
            cartBadge.textContent = totalItems;
            cartBadge.classList.remove('hidden');
            if (headerCartBadge) {
                headerCartBadge.textContent = totalItems;
                headerCartBadge.classList.remove('hidden');
            }
        } else {
            cartBadge.classList.add('hidden');
            if (headerCartBadge) {
                headerCartBadge.classList.add('hidden');
            }
        }

        if (cart.length === 0) {
            cartItemsDiv.innerHTML = '<p class="text-center text-gray-500 text-sm py-8">Keranjang kosong</p>';
            cartTotalSpan.textContent = 'Rp 0';
            checkoutBtn.disabled = true;
            return;
        }

        let total = 0;
        let html = '';

        cart.forEach(item => {
            total += item.subtotal;
            const storageBase = '{{ asset("storage") }}';
            const photoUrl = item.photo_url ? storageBase + '/' + item.photo_url : '';
            const itemId = item.item_type === 'package' ? item.package_id : item.product_id;
            const itemType = item.item_type || 'product';
            const badge = item.item_type === 'package' ? '<span class="text-xs bg-[#2e4358] text-white px-1.5 py-0.5 rounded">Paket</span>' : '';
            
            html += `
                <div class="flex items-start gap-3 p-2 border border-gray-200 rounded">
                    ${item.photo_url ? 
                        `<img src="${photoUrl}" alt="${item.name}" class="w-12 h-12 object-cover rounded">` :
                        `<div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>`
                    }
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1 mb-1">
                            <p class="text-sm font-medium text-gray-900 truncate">${item.name}</p>
                            ${badge}
                        </div>
                        <p class="text-xs text-gray-600">Rp ${item.price.toLocaleString('id-ID')}</p>
                        ${item.item_count ? `<p class="text-xs text-gray-500">${item.item_count} item</p>` : ''}
                        <div class="flex items-center gap-2 mt-1">
                            <button onclick="updateQuantity(${itemId}, -1, '${itemType}')" class="w-6 h-6 bg-gray-200 rounded text-gray-700 hover:bg-gray-300 text-xs">-</button>
                            <span class="text-sm font-medium w-8 text-center">${item.quantity}</span>
                            <button onclick="updateQuantity(${itemId}, 1, '${itemType}')" class="w-6 h-6 bg-gray-200 rounded text-gray-700 hover:bg-gray-300 text-xs">+</button>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">Rp ${item.subtotal.toLocaleString('id-ID')}</p>
                        <button onclick="removeFromCart(${itemId}, '${itemType}')" class="text-xs text-red-600 hover:text-red-800 mt-1">
                            Hapus
                        </button>
                    </div>
                </div>
            `;
        });

        cartItemsDiv.innerHTML = html;
        cartTotalSpan.textContent = 'Rp ' + total.toLocaleString('id-ID');
        checkoutBtn.disabled = false;
    }

    function toggleTableNumber() {
        const orderType = document.getElementById('order_type').value;
        const tableField = document.getElementById('tableNumberField');
        if (orderType === 'dine-in') {
            tableField.style.display = 'block';
        } else {
            tableField.style.display = 'none';
        }
    }

    function clearCart() {
        if (confirm('Reset keranjang?')) {
            cart = [];
            updateCartDisplay();
            document.getElementById('orderForm').reset();
            document.getElementById('tableNumberField').style.display = 'none';
        }
    }

    function checkout() {
        const customerName = document.getElementById('customer_name').value;
        const orderType = document.getElementById('order_type').value;
        const tableNumber = document.getElementById('table_number').value;
        const paymentMethod = document.getElementById('payment_method').value;
        const notes = document.getElementById('notes').value;

        if (!customerName || !orderType || !paymentMethod) {
            alert('Lengkapi informasi pemesan, tipe pesanan, dan metode pembayaran.');
            return;
        }

        if (cart.length === 0) {
            alert('Keranjang kosong.');
            return;
        }

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.cashier.store") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const customerInput = document.createElement('input');
        customerInput.type = 'hidden';
        customerInput.name = 'customer_name';
        customerInput.value = customerName;
        form.appendChild(customerInput);

        const orderTypeInput = document.createElement('input');
        orderTypeInput.type = 'hidden';
        orderTypeInput.name = 'order_type';
        orderTypeInput.value = orderType;
        form.appendChild(orderTypeInput);

        const paymentMethodInput = document.createElement('input');
        paymentMethodInput.type = 'hidden';
        paymentMethodInput.name = 'payment_method';
        paymentMethodInput.value = paymentMethod;
        form.appendChild(paymentMethodInput);

        if (tableNumber) {
            const tableInput = document.createElement('input');
            tableInput.type = 'hidden';
            tableInput.name = 'table_number';
            tableInput.value = tableNumber;
            form.appendChild(tableInput);
        }

        if (notes) {
            const notesInput = document.createElement('input');
            notesInput.type = 'hidden';
            notesInput.name = 'notes';
            notesInput.value = notes;
            form.appendChild(notesInput);
        }

        cart.forEach((item, index) => {
            const itemTypeInput = document.createElement('input');
            itemTypeInput.type = 'hidden';
            itemTypeInput.name = `items[${index}][item_type]`;
            itemTypeInput.value = item.item_type || 'product';
            form.appendChild(itemTypeInput);

            if (item.item_type === 'package') {
                const packageIdInput = document.createElement('input');
                packageIdInput.type = 'hidden';
                packageIdInput.name = `items[${index}][package_id]`;
                packageIdInput.value = item.package_id;
                form.appendChild(packageIdInput);
            } else {
                const productIdInput = document.createElement('input');
                productIdInput.type = 'hidden';
                productIdInput.name = `items[${index}][product_id]`;
                productIdInput.value = item.product_id;
                form.appendChild(productIdInput);
            }

            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `items[${index}][quantity]`;
            quantityInput.value = item.quantity;
            form.appendChild(quantityInput);
        });

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection

