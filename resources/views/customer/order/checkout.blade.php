@extends('layouts.customer')

@section('title', 'Checkout - Kue Mang Wiro')

@section('content')
<h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 sm:mb-6 -mx-2 sm:mx-0 px-2 sm:px-0">Checkout</h1>

@php
    $cart = session('cart', []);
    $items = [];
    $total = 0;
    foreach ($cart as $productId => $item) {
        $product = \App\Models\Product::find($productId);
        if ($product && $product->is_active) {
            $item['product'] = $product;
            $item['subtotal'] = $product->price * $item['quantity'];
            $total += $item['subtotal'];
            $items[$productId] = $item;
        }
    }
@endphp

@if(empty($items))
    <div class="text-center py-8 sm:py-12">
        <p class="text-gray-500 text-base sm:text-lg mb-4">Keranjang Anda kosong.</p>
        <a href="{{ route('menu.index') }}" class="inline-block bg-[#2e4358] text-white px-5 sm:px-6 py-2 text-sm sm:text-base rounded-lg hover:bg-[#1a2a3a]">
            Lihat Menu
        </a>
    </div>
@else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 -mx-2 sm:mx-0">
        <div class="lg:col-span-2 px-2 sm:px-0">
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 w-full">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4">Detail Pesanan</h2>
                <div class="divide-y divide-gray-200">
                    @foreach($items as $item)
                        <div class="py-3 sm:py-4 flex justify-between items-start">
                            <div class="flex-1 pr-2">
                                <p class="text-sm sm:text-base font-medium text-gray-900">{{ $item['product']->name }}</p>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1">Qty: {{ $item['quantity'] }}</p>
                            </div>
                            <p class="text-sm sm:text-base font-semibold text-gray-900 whitespace-nowrap">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between text-lg sm:text-xl font-bold">
                        <span>Total:</span>
                        <span class="text-[#2e4358]">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 px-2 sm:px-0">
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-8 w-full">
                <h2 class="text-xl sm:text-xl font-semibold text-gray-900 mb-4">Informasi Pesanan</h2>
                <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4 sm:mb-4">
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Pemesan *</label>
                        <input type="text" name="customer_name" id="customer_name" required
                               class="w-full px-4 sm:px-4 py-2 text-base sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2e4358] focus:border-transparent"
                               value="{{ old('customer_name') }}">
                        @error('customer_name')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 sm:mb-4">
                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="customer_email" id="customer_email" required
                               class="w-full px-4 sm:px-4 py-2 text-base sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2e4358] focus:border-transparent"
                               value="{{ old('customer_email') }}">
                        @error('customer_email')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 sm:mb-4">
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon *</label>
                        <input type="tel" name="customer_phone" id="customer_phone" required
                               class="w-full px-4 sm:px-4 py-2 text-base sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2e4358] focus:border-transparent"
                               value="{{ old('customer_phone') }}">
                        @error('customer_phone')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 sm:mb-4">
                        <label for="order_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Pesanan *</label>
                        <select name="order_type" id="order_type" required
                                class="w-full px-4 sm:px-4 py-2 text-base sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2e4358] focus:border-transparent">
                            <option value="">Pilih Tipe Pesanan</option>
                            <option value="dine-in" {{ old('order_type') == 'dine-in' ? 'selected' : '' }}>Dine In</option>
                            <option value="takeaway" {{ old('order_type') == 'takeaway' ? 'selected' : '' }}>Takeaway</option>
                            <option value="pickup" {{ old('order_type') == 'pickup' ? 'selected' : '' }}>Pick Up</option>
                        </select>
                        @error('order_type')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 sm:mb-4" id="table_number_field" style="display: none;">
                        <label for="table_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor Meja</label>
                        <input type="text" name="table_number" id="table_number"
                               class="w-full px-4 sm:px-4 py-2 text-base sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2e4358] focus:border-transparent"
                               value="{{ old('table_number') }}">
                        @error('table_number')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 sm:mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full px-4 sm:px-4 py-2 text-base sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2e4358] focus:border-transparent"
                                  placeholder="Tambahkan catatan khusus untuk pesanan...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 sm:mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Metode Pembayaran *</label>
                        <div class="space-y-3 sm:space-y-3">
                            @php
                                $qrisMethods = $paymentMethods->where('type', 'qris');
                                $bankTransferMethods = $paymentMethods->where('type', 'bank_transfer');
                            @endphp

                            @if($qrisMethods->count() > 0)
                                <div class="mb-3 sm:mb-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">QR Code</p>
                                    @foreach($qrisMethods as $method)
                                        <label class="flex items-start p-3 sm:p-3 border-2 rounded-lg cursor-pointer hover:border-[#2e4358] transition-colors mb-2 {{ old('payment_method') == 'qris' ? 'border-[#2e4358] bg-blue-50' : 'border-gray-200' }}">
                                            <input type="radio" name="payment_method" value="qris" 
                                                   class="mt-1 mr-2 sm:mr-3 text-[#2e4358] focus:ring-[#2e4358]" required>
                                            <div class="flex-1">
                                                <div class="text-base sm:text-base font-medium text-gray-900">{{ $method->name }}</div>
                                                @if($method->qr_code_image)
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/' . $method->qr_code_image) }}" 
                                                             alt="{{ $method->name }}" 
                                                             class="w-28 h-28 sm:w-32 sm:h-32 object-contain border border-gray-200 rounded">
                                                    </div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            @if($bankTransferMethods->count() > 0)
                                <div class="mb-3 sm:mb-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Transfer Bank</p>
                                    @foreach($bankTransferMethods as $method)
                                        <label class="flex items-start p-3 sm:p-3 border-2 rounded-lg cursor-pointer hover:border-[#2e4358] transition-colors mb-2 {{ old('payment_method') == 'bank_transfer' ? 'border-[#2e4358] bg-blue-50' : 'border-gray-200' }}">
                                            <input type="radio" name="payment_method" value="bank_transfer"
                                                   class="mt-1 mr-2 sm:mr-3 text-[#2e4358] focus:ring-[#2e4358]" required>
                                            <div class="flex-1">
                                                <div class="text-base sm:text-base font-medium text-gray-900">{{ $method->name }}</div>
                                                @if($method->bank_name || $method->account_number || $method->account_name)
                                                    <div class="mt-2 text-xs sm:text-sm text-gray-600">
                                                        @if($method->bank_name)
                                                            <div><strong>Bank:</strong> {{ $method->bank_name }}</div>
                                                        @endif
                                                        @if($method->account_number)
                                                            <div><strong>No. Rekening:</strong> {{ $method->account_number }}</div>
                                                        @endif
                                                        @if($method->account_name)
                                                            <div><strong>Atas Nama:</strong> {{ $method->account_name }}</div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            <label class="flex items-start p-3 sm:p-3 border-2 rounded-lg cursor-pointer hover:border-[#2e4358] transition-colors {{ old('payment_method') == 'cash' ? 'border-[#2e4358] bg-blue-50' : 'border-gray-200' }}">
                                <input type="radio" name="payment_method" value="cash" 
                                       class="mt-1 mr-2 sm:mr-3 text-[#2e4358] focus:ring-[#2e4358]" required>
                                <div class="text-base sm:text-base font-medium text-gray-900">Tunai (Bayar di Tempat)</div>
                            </label>
                        </div>
                        @error('payment_method')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 sm:mb-6" id="proof_of_payment_field" style="display: none;">
                        <label for="proof_of_payment" class="block text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran *</label>
                        <input type="file" name="proof_of_payment" id="proof_of_payment" accept="image/jpeg,image/jpg,image/png,image/gif"
                               class="w-full px-4 sm:px-4 py-2 text-base sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2e4358] focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, atau GIF (Maks. 5MB)</p>
                        @error('proof_of_payment')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-[#2e4358] text-white py-3 sm:py-3 text-base sm:text-base rounded-lg hover:bg-[#1a2a3a] transition-colors font-semibold">
                        Buat Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif

<script>
    document.getElementById('order_type').addEventListener('change', function() {
        const tableField = document.getElementById('table_number_field');
        if (this.value === 'dine-in') {
            tableField.style.display = 'block';
        } else {
            tableField.style.display = 'none';
        }
    });

    // Trigger on page load if value is already set
    if (document.getElementById('order_type').value === 'dine-in') {
        document.getElementById('table_number_field').style.display = 'block';
    }

    // Handle payment method change to show/hide proof of payment field
    const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
    const proofOfPaymentField = document.getElementById('proof_of_payment_field');
    const proofOfPaymentInput = document.getElementById('proof_of_payment');

    function toggleProofOfPaymentField() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        if (selectedMethod && selectedMethod.value !== 'cash') {
            proofOfPaymentField.style.display = 'block';
            proofOfPaymentInput.setAttribute('required', 'required');
        } else {
            proofOfPaymentField.style.display = 'none';
            proofOfPaymentInput.removeAttribute('required');
            proofOfPaymentInput.value = '';
        }
    }

    paymentMethodInputs.forEach(input => {
        input.addEventListener('change', toggleProofOfPaymentField);
    });

    // Trigger on page load if value is already set
    toggleProofOfPaymentField();
</script>
@endsection

