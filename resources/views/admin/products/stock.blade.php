@extends('layouts.admin')

@section('title', 'Kelola Stok - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Kelola Stok Kategori</h1>
    <p class="text-sm text-gray-600 mt-1">Kelola stok untuk setiap kategori. Stok kategori berlaku untuk semua produk dalam kategori tersebut.</p>
</div>

<form action="{{ route('admin.products.stock.update') }}" method="POST">
    @csrf
    
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Saat Ini</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Baru</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                @if(!$category->is_active)
                                    <span class="text-xs text-red-600">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600">{{ $category->description ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $category->products->count() }} produk</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold {{ $category->stock <= 0 ? 'text-red-600' : ($category->stock < 10 ? 'text-yellow-600' : 'text-gray-900') }}">
                                    {{ $category->stock ?? 0 }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="number" 
                                       name="stocks[{{ $category->id }}]" 
                                       value="{{ $category->stock ?? 0 }}" 
                                       min="0"
                                       class="w-32 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada kategori aktif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="flex justify-end space-x-3 mt-6">
        <a href="{{ route('admin.products.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300">
            Batal
        </a>
        <button type="submit" class="bg-[#2e4358] text-white px-6 py-2 rounded-lg hover:bg-[#1a2a3a]">
            Simpan Perubahan Stok
        </button>
    </div>
</form>
@endsection

