@extends('layouts.admin')

@section('title', 'Kasir - Admin')

@section('content')
<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-900">Kasir</h1>
        <button onclick="clearCart()" class="text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Reset
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" style="min-height: calc(100vh - 250px);">
        <!-- Product Selection Panel -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
            <!-- Search Bar -->
            <div class="p-4 border-b">
                <input type="text" id="productSearch" placeholder="Cari produk..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2e4358] focus:border-[#2e4358]"
                       onkeyup="filterProducts()">
            </div>

            <!-- Category Tabs -->
            <div class="px-4 py-2 border-b bg-gray-50 overflow-x-auto">
                <div class="flex space-x-2" id="categoryTabs">
                    <button onclick="selectCategory('all')" 
                            class="category-tab px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap bg-[#2e4358] text-white" 
                            data-category="all">
                        Semua
                    </button>
                    @foreach($categories as $category)
                        <button onclick="selectCategory({{ $category->id }})" 
                                class="category-tab px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap bg-white text-gray-700 hover:bg-gray-100 border border-gray-300" 
                                data-category="{{ $category->id }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Products Grid -->
            <div class="flex-1 overflow-y-auto p-4">
                <div id="productsGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
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
                            
                            <div class="p-3">
                                @if($product->photo_url)
                                    <img src="{{ asset('storage/' . $product->photo_url) }}" alt="{{ $product->name }}" 
                                         class="w-full h-32 object-cover rounded-lg bg-white">
                                @else
                                    <div class="w-full h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="px-3 pb-3 relative">
                                <h3 class="text-sm font-bold text-gray-900 mb-1 line-clamp-1">{{ $product->name }}</h3>
                                <p class="text-sm font-bold text-[#2e4358] mb-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-900 mb-2">Stok: {{ $product->category->stock ?? 0 }}</p>
                                
                                @if($product->category->stock > 0)
                                    <div class="absolute bottom-3 right-3 w-10 h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="lg:col-span-1 bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Keranjang</h2>
            </div>

            <!-- Customer Info Form -->
            <div class="p-4 border-b bg-gray-50">
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
                                      class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-[#2e4358] focus:border-[#2e4358]"
                                      placeholder="Tambahkan catatan khusus..."></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4">
                <div id="cartItems" class="space-y-2">
                    <p class="text-center text-gray-500 text-sm py-8">Keranjang kosong</p>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="p-4 border-t bg-gray-50">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-medium text-gray-700">Total:</span>
                    <span id="cartTotal" class="text-xl font-bold text-[#2e4358]">Rp 0</span>
                </div>
                <button onclick="checkout()" 
                        id="checkoutBtn"
                        disabled
                        class="w-full bg-[#2e4358] text-white py-3 rounded-lg hover:bg-[#1a2a3a] font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed">
                    Buat Pesanan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let selectedCategory = 'all';

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
            const matchesCategory = selectedCategory === 'all' || category == selectedCategory;
            const matchesSearch = name.includes(searchTerm);

            if (matchesCategory && matchesSearch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
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
            if (item.category_id === categoryId) {
                categoryTotalQuantity += item.quantity;
            }
        });
        
        const existingItem = cart.find(item => item.product_id === productId);
        
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

    function removeFromCart(productId) {
        cart = cart.filter(item => item.product_id !== productId);
        updateCartDisplay();
    }

    function updateQuantity(productId, change) {
        const item = cart.find(item => item.product_id === productId);
        if (item) {
            const newQuantity = item.quantity + change;
            if (newQuantity <= 0) {
                removeFromCart(productId);
                return;
            }
            
            // Calculate total quantity for this category in cart
            let categoryTotalQuantity = 0;
            cart.forEach(cartItem => {
                if (cartItem.category_id === item.category_id) {
                    categoryTotalQuantity += cartItem.quantity;
                }
            });
            
            // Adjust for the current item's quantity change
            const newCategoryTotal = categoryTotalQuantity - item.quantity + newQuantity;
            
            // Check stock availability for category
            if (item.stock && newCategoryTotal > item.stock) {
                alert('Stok tidak cukup untuk kategori. Stok tersedia: ' + item.stock);
                return;
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
                        <p class="text-sm font-medium text-gray-900 truncate">${item.name}</p>
                        <p class="text-xs text-gray-600">Rp ${item.price.toLocaleString('id-ID')}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <button onclick="updateQuantity(${item.product_id}, -1)" class="w-6 h-6 bg-gray-200 rounded text-gray-700 hover:bg-gray-300 text-xs">-</button>
                            <span class="text-sm font-medium w-8 text-center">${item.quantity}</span>
                            <button onclick="updateQuantity(${item.product_id}, 1)" class="w-6 h-6 bg-gray-200 rounded text-gray-700 hover:bg-gray-300 text-xs">+</button>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">Rp ${item.subtotal.toLocaleString('id-ID')}</p>
                        <button onclick="removeFromCart(${item.product_id})" class="text-xs text-red-600 hover:text-red-800 mt-1">
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
            const productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = `items[${index}][product_id]`;
            productIdInput.value = item.product_id;
            form.appendChild(productIdInput);

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

