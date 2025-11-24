@extends('layouts.admin')

@section('title', 'Tambah Kategori - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Tambah Kategori</h1>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori *</label>
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
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-[#2e4358] shadow-sm focus:border-[#2e4358]/50 focus:ring focus:ring-[#2e4358]/20 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                </label>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.categories.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
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

