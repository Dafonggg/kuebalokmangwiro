@extends('layouts.admin')

@section('title', 'Edit Produk - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Edit Produk</h1>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori *</label>
                <select name="category_id" id="category_id" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk *</label>
                <input type="text" name="name" id="name" required
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm"
                       value="{{ old('name', $product->name) }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Harga *</label>
                <input type="number" name="price" id="price" required min="0" step="0.01"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm"
                       value="{{ old('price', $product->price) }}">
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if($product->photo_url)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Foto Saat Ini</label>
                    <img src="{{ asset('storage/' . $product->photo_url) }}" alt="{{ $product->name }}" class="mt-2 h-32 w-32 object-cover rounded">
                </div>
            @endif

            <div>
                <label for="photo" class="block text-sm font-medium text-gray-700">Foto Produk Baru (opsional)</label>
                <input type="file" name="photo" id="photo" accept="image/*"
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#2e4358]/10 file:text-[#2e4358] hover:file:bg-[#2e4358]/20">
                @error('photo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-[#2e4358] shadow-sm focus:border-[#2e4358]/50 focus:ring focus:ring-[#2e4358]/20 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                </label>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.products.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="bg-[#2e4358] text-white px-4 py-2 rounded-lg hover:bg-[#1a2a3a]">
                    Simpan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

