@extends('layouts.admin')

@section('title', 'Produk - Admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Produk</h1>
    <a href="{{ route('admin.products.create') }}" class="bg-[#2e4358] text-white px-4 py-2 rounded-lg hover:bg-[#1a2a3a]">
        Tambah Produk
    </a>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <ul class="divide-y divide-gray-200">
        @forelse($products as $product)
            <li>
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            @if($product->photo_url)
                                <img src="{{ asset('storage/' . $product->photo_url) }}" alt="{{ $product->name }}" class="h-16 w-16 object-cover rounded">
                            @else
                                <div class="h-16 w-16 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-sm text-gray-500">{{ $product->category->name }}</div>
                                <div class="text-sm font-semibold text-[#2e4358]">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-[#2e4358] hover:text-[#1a2a3a]">
                                Edit
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="px-4 py-4 text-center text-gray-500">
                Tidak ada produk.
            </li>
        @endforelse
    </ul>
</div>
@endsection

