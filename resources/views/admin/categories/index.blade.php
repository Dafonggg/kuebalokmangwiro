@extends('layouts.admin')

@section('title', 'Kategori - Admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Kategori</h1>
    <a href="{{ route('admin.categories.create') }}" class="bg-[#2e4358] text-white px-4 py-2 rounded-lg hover:bg-[#1a2a3a]">
        Tambah Kategori
    </a>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <ul class="divide-y divide-gray-200">
        @forelse($categories as $category)
            <li>
                <div class="px-4 py-4 sm:px-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $category->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                            @if($category->description)
                                <div class="text-sm text-gray-500">{{ $category->description }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-[#2e4358] hover:text-[#1a2a3a]">
                            Edit
                        </a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </div>
                </div>
            </li>
        @empty
            <li class="px-4 py-4 text-center text-gray-500">
                Tidak ada kategori.
            </li>
        @endforelse
    </ul>
</div>
@endsection

