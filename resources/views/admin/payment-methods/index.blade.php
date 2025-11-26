@extends('layouts.admin')

@section('title', 'Metode Pembayaran - Admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Metode Pembayaran</h1>
    <a href="{{ route('admin.payment-methods.create') }}" class="bg-[#2e4358] text-white px-4 py-2 rounded-lg hover:bg-[#1a2a3a]">
        Tambah Metode Pembayaran
    </a>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <ul class="divide-y divide-gray-200">
        @forelse($paymentMethods as $method)
            <li>
                <div class="px-4 py-4 sm:px-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $method->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $method->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                            <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $method->type == 'qris' ? 'QRIS' : 'Transfer Bank' }}
                            </span>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $method->name }}</div>
                            @if($method->type == 'bank_transfer')
                                <div class="text-sm text-gray-500">
                                    @if($method->bank_name) {{ $method->bank_name }} @endif
                                    @if($method->account_number) - {{ $method->account_number }} @endif
                                    @if($method->account_name) ({{ $method->account_name }}) @endif
                                </div>
                            @endif
                            @if($method->qr_code_image)
                                <div class="mt-2">
                                    <img src="{{ storage_url($method->qr_code_image) }}" 
                                         alt="{{ $method->name }}" 
                                         class="w-24 h-24 object-contain border border-gray-200 rounded">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.payment-methods.edit', $method) }}" class="text-[#2e4358] hover:text-[#1a2a3a]">
                            Edit
                        </a>
                        <form action="{{ route('admin.payment-methods.destroy', $method) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus metode pembayaran ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </div>
                </div>
            </li>
        @empty
            <li class="px-4 py-4 text-center text-gray-500">
                Tidak ada metode pembayaran.
            </li>
        @endforelse
    </ul>
</div>
@endsection

