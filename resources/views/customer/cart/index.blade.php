@extends('layouts.customer')

@section('title', 'Keranjang - Kue Mang Wiro')

@section('content')
<h1 class="text-3xl font-bold text-gray-900 mb-6">Keranjang Belanja</h1>

@if(empty($items))
    <div class="text-center py-12">
        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <p class="mt-4 text-gray-500 text-lg">Keranjang Anda kosong.</p>
        <a href="{{ route('menu.index') }}" class="mt-4 inline-block bg-[#2e4358] text-white px-6 py-2 rounded-lg hover:bg-[#1a2a3a]">
            Lihat Menu
        </a>
    </div>
@else
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="divide-y divide-gray-200">
            @foreach($items as $key => $item)
                <div class="p-4 flex items-center gap-4">
                    <div class="flex-1">
                        @if(isset($item['package']))
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $item['package']->name }}</h3>
                                <span class="bg-[#2e4358] text-white text-xs font-semibold px-2 py-0.5 rounded">Paket</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Rp {{ number_format($item['package']->price, 0, ',', '.') }} per paket</p>
                            <div class="text-xs text-gray-500">
                                <p class="font-semibold mb-1">Isi paket:</p>
                                <ul class="list-disc list-inside">
                                    @foreach($item['package']->items as $packageItem)
                                        <li>{{ $packageItem->product->name }} x{{ $packageItem->qty }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @elseif(isset($item['product']))
                            <h3 class="text-lg font-semibold text-gray-900">{{ $item['product']->name }}</h3>
                            <p class="text-sm text-gray-600">Rp {{ number_format($item['product']->price, 0, ',', '.') }} per item</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-4">
                        <form action="{{ route('cart.update', $key) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <button type="button" onclick="decreaseQuantity('{{ $key }}')" class="bg-gray-200 text-gray-700 px-3 py-1 rounded hover:bg-gray-300">-</button>
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" 
                                   class="w-16 text-center border border-gray-300 rounded px-2 py-1" 
                                   onchange="this.form.submit()">
                            <button type="button" onclick="increaseQuantity('{{ $key }}')" class="bg-gray-200 text-gray-700 px-3 py-1 rounded hover:bg-gray-300">+</button>
                        </form>
                        <span class="text-lg font-semibold text-gray-900 w-24 text-right">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </span>
                        <form action="{{ route('cart.remove', $key) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="p-6 bg-gray-50 border-t">
            <div class="flex justify-between items-center mb-4">
                <span class="text-xl font-semibold text-gray-900">Total:</span>
                <span class="text-2xl font-bold text-[#2e4358]">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('orders.checkout') }}" class="block w-full bg-[#2e4358] text-white text-center py-3 rounded-lg hover:bg-[#1a2a3a] transition-colors font-semibold">
                Checkout
            </a>
        </div>
    </div>
@endif

<script>
    function increaseQuantity(key) {
        const form = event.target.closest('form');
        const input = form.querySelector('input[name="quantity"]');
        input.value = parseInt(input.value) + 1;
        form.submit();
    }

    function decreaseQuantity(key) {
        const form = event.target.closest('form');
        const input = form.querySelector('input[name="quantity"]');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
            form.submit();
        }
    }
</script>
@endsection

