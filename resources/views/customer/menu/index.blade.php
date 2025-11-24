@extends('layouts.customer')

@section('title', 'Menu - Kue Mang Wiro')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">Menu Kue Balok Mang Wiro</h1>
    
    <div class="flex flex-col md:flex-row gap-4 mb-6">
        <div class="flex-1">
            <input type="text" id="search" placeholder="Cari menu..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2e4358] focus:border-transparent"
                   value="{{ request('search') }}">
        </div>
        <div class="md:w-64">
            <select id="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2e4358] focus:border-transparent">
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

@if($products->isEmpty())
    <div class="text-center py-12">
        <p class="text-gray-500 text-lg">Tidak ada produk ditemukan.</p>
    </div>
@else
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
                    
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="absolute bottom-0 right-0">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" 
                                class="w-11 h-11 md:w-12 md:h-12 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 active:bg-gray-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed shadow-md hover:shadow-lg disabled:shadow-none touch-manipulation"
                                {{ $product->category->stock <= 0 ? 'disabled' : '' }}
                                aria-label="Tambah {{ $product->name }} ke keranjang">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
@endif

<script>
    document.getElementById('search').addEventListener('input', function() {
        filterProducts();
    });

    document.getElementById('category').addEventListener('change', function() {
        filterProducts();
    });

    function filterProducts() {
        const search = document.getElementById('search').value;
        const category = document.getElementById('category').value;
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
</script>
@endsection

