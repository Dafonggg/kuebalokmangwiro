@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@section('content')
<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <button onclick="toggleOrderForm()" class="bg-[#2e4358] text-white px-4 py-2 rounded-lg hover:bg-[#1a2a3a] flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Pesanan Baru
        </button>
    </div>

    <!-- Order Creation Form -->
    <div id="orderForm" class="hidden mb-6 bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Buat Pesanan Baru</h2>
        <form action="{{ route('admin.orders.create') }}" method="POST" id="createOrderForm" onsubmit="return validateOrderForm(event)">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Pemesan *</label>
                    <input type="text" name="customer_name" id="customer_name" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358]">
                </div>
                <div>
                    <label for="order_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Pesanan *</label>
                    <select name="order_type" id="order_type" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358]"
                            onchange="toggleTableNumber()">
                        <option value="">Pilih Tipe</option>
                        <option value="dine-in">Dine In</option>
                        <option value="takeaway">Takeaway</option>
                        <option value="pickup">Pick Up</option>
                    </select>
                </div>
                <div id="tableNumberField" style="display: none;">
                    <label for="table_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Meja</label>
                    <input type="text" name="table_number" id="table_number"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358]">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Produk</label>
                <div class="border border-gray-300 rounded-md p-4 max-h-64 overflow-y-auto">
                    @foreach($categories as $category)
                        <div class="mb-4">
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $category->name }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($products->where('category_id', $category->id) as $product)
                                    <div class="flex items-center justify-between p-2 border border-gray-200 rounded hover:bg-gray-50">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 ml-2">
                                            <button type="button" onclick="decreaseQuantity({{ $product->id }})" class="w-6 h-6 bg-gray-200 rounded text-gray-700 hover:bg-gray-300">-</button>
                                            <input type="number" name="items[{{ $product->id }}][quantity]" 
                                                   id="qty_{{ $product->id }}" 
                                                   value="0" min="0" 
                                                   class="w-12 text-center border border-gray-300 rounded text-sm"
                                                   onchange="updateProductId({{ $product->id }})">
                                            <button type="button" onclick="increaseQuantity({{ $product->id }})" class="w-6 h-6 bg-gray-200 rounded text-gray-700 hover:bg-gray-300">+</button>
                                            <input type="hidden" name="items[{{ $product->id }}][product_id]" id="prod_{{ $product->id }}" value="{{ $product->id }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="toggleOrderForm()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="bg-[#2e4358] text-white px-4 py-2 rounded-lg hover:bg-[#1a2a3a]">
                    Buat Pesanan
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Pesanan</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $totalOrders }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Menunggu Konfirmasi</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $pendingOrders }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Pendapatan</dt>
                            <dd class="text-lg font-medium text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Produk</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $totalProducts }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's Summary -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Hari Ini</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Pesanan Hari Ini</span>
                    <span class="font-semibold text-gray-900">{{ $todayOrders }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pendapatan Hari Ini</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Orders with Quick Actions -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Pesanan Terbaru</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-[#2e4358] hover:text-[#1a2a3a]">
                    Lihat Semua →
                </a>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($recentOrders as $order)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $order->order_code }}</p>
                                <p class="text-sm text-gray-600">{{ $order->customer_name }}</p>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($order->order_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->order_status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->order_status === 'ready') bg-green-100 text-green-800
                                    @elseif($order->order_status === 'completed') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-2">
                            <p class="text-sm text-gray-600">Total: <span class="font-semibold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></p>
                            <p class="text-xs text-gray-500">
                                {{ $order->orderItems->count() }} item(s) • 
                                {{ $order->payment_status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar' }}
                            </p>
                        </div>
                        <div class="flex gap-2 flex-wrap">
                            @if($order->order_status === 'pending')
                                <form action="{{ route('admin.orders.quickStatus', $order) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="order_status" value="processing">
                                    <button type="submit" class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                        Mulai Proses
                                    </button>
                                </form>
                            @elseif($order->order_status === 'processing')
                                <form action="{{ route('admin.orders.quickStatus', $order) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="order_status" value="ready">
                                    <button type="submit" class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                        Tandai Siap
                                    </button>
                                </form>
                            @elseif($order->order_status === 'ready')
                                <form action="{{ route('admin.orders.quickStatus', $order) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="order_status" value="completed">
                                    <button type="submit" class="text-xs bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700">
                                        Selesai
                                    </button>
                                </form>
                            @endif
                            
                            @if($order->payment_status === 'unpaid')
                                <form action="{{ route('admin.orders.quickPayment', $order) }}" method="POST" class="inline" onsubmit="return confirmQuickPayment(event, '{{ $order->order_code }}')">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="payment_method" value="cash">
                                    <button type="submit" class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                        Konfirmasi Bayar
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs bg-[#2e4358] text-white px-3 py-1 rounded hover:bg-[#1a2a3a] inline-block">
                                Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">Tidak ada pesanan.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    function toggleOrderForm() {
        const form = document.getElementById('orderForm');
        form.classList.toggle('hidden');
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

    function increaseQuantity(productId) {
        const input = document.getElementById('qty_' + productId);
        input.value = parseInt(input.value) + 1;
        updateProductId(productId);
    }

    function decreaseQuantity(productId) {
        const input = document.getElementById('qty_' + productId);
        if (parseInt(input.value) > 0) {
            input.value = parseInt(input.value) - 1;
            updateProductId(productId);
        }
    }

    function updateProductId(productId) {
        const qtyInput = document.getElementById('qty_' + productId);
        const prodInput = document.getElementById('prod_' + productId);
        if (parseInt(qtyInput.value) === 0) {
            prodInput.disabled = true;
        } else {
            prodInput.disabled = false;
        }
    }

    function confirmQuickPayment(event, orderCode) {
        if (!confirm('Konfirmasi pembayaran untuk pesanan ' + orderCode + '?')) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    function validateOrderForm(event) {
        let hasItems = false;
        const form = event.target;
        
        // Disable all product inputs first
        @foreach($products as $product)
            const prodInput{{ $product->id }} = document.getElementById('prod_{{ $product->id }}');
            const qtyInput{{ $product->id }} = document.getElementById('qty_{{ $product->id }}');
            const qty{{ $product->id }} = parseInt(qtyInput{{ $product->id }}.value || 0);
            
            if (qty{{ $product->id }} > 0) {
                hasItems = true;
                prodInput{{ $product->id }}.disabled = false;
            } else {
                prodInput{{ $product->id }}.disabled = true;
                // Remove the input from form submission
                qtyInput{{ $product->id }}.disabled = true;
            }
        @endforeach

        if (!hasItems) {
            event.preventDefault();
            alert('Pilih minimal satu produk untuk membuat pesanan.');
            return false;
        }
        return true;
    }

    // Initialize - disable all product inputs
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($products as $product)
            updateProductId({{ $product->id }});
        @endforeach
    });
</script>
@endsection
