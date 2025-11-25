@extends('layouts.admin')

@section('title', 'Tambah Paket - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Tambah Paket</h1>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <form action="{{ route('admin.packages.store') }}" method="POST" enctype="multipart/form-data" id="packageForm">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Paket *</label>
                <input type="text" name="name" id="name" required
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm"
                       value="{{ old('name') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Harga Paket *</label>
                <input type="number" name="price" id="price" required min="0" step="0.01"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm"
                       value="{{ old('price') }}">
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="photo" class="block text-sm font-medium text-gray-700">Foto Paket</label>
                <input type="file" name="photo" id="photo" accept="image/*"
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#2e4358]/10 file:text-[#2e4358] hover:file:bg-[#2e4358]/20">
                @error('photo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-[#2e4358] shadow-sm focus:border-[#2e4358]/50 focus:ring focus:ring-[#2e4358]/20 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                </label>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between items-center mb-4">
                    <label class="block text-sm font-medium text-gray-700">Produk dalam Paket *</label>
                    <button type="button" onclick="addProductItem()" class="bg-[#2e4358] text-white px-3 py-1 rounded text-sm hover:bg-[#1a2a3a]">
                        + Tambah Produk
                    </button>
                </div>
                <div id="productItems" class="space-y-3">
                    <!-- Product items will be added here dynamically -->
                </div>
                @error('items')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.packages.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="bg-[#2e4358] text-white px-4 py-2 rounded-lg hover:bg-[#1a2a3a]">
                    Simpan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    let itemIndex = 0;
    const products = @json($products);

    function addProductItem(productId = '', qty = 1) {
        const container = document.getElementById('productItems');
        const itemDiv = document.createElement('div');
        itemDiv.className = 'flex items-center space-x-3 p-3 border border-gray-200 rounded';
        itemDiv.innerHTML = `
            <select name="items[${itemIndex}][product_id]" required
                    class="flex-1 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm">
                <option value="">Pilih Produk</option>
                ${products.map(p => `<option value="${p.id}" ${p.id == productId ? 'selected' : ''}>${p.name} (${p.category.name}) - Rp ${parseInt(p.price).toLocaleString('id-ID')}</option>`).join('')}
            </select>
            <input type="number" name="items[${itemIndex}][qty]" value="${qty}" required min="1"
                   class="w-20 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm"
                   placeholder="Qty">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        container.appendChild(itemDiv);
        itemIndex++;
    }

    // Add one item by default
    document.addEventListener('DOMContentLoaded', function() {
        addProductItem();
    });
</script>
@endsection

