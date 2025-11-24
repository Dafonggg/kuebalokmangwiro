@extends('layouts.customer')

@section('title', 'Detail Pesanan - Kue Mang Wiro')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Detail Pesanan</h1>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Kode Pesanan: {{ $order->order_code }}</h2>
                <p class="text-sm text-gray-600 mt-1">Tanggal: {{ $order->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="text-right">
                @if($order->order_status === 'pending')
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">Menunggu Konfirmasi</span>
                @elseif($order->order_status === 'processing')
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">Sedang Diproses</span>
                @elseif($order->order_status === 'ready')
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Siap Diambil</span>
                @elseif($order->order_status === 'completed')
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">Selesai</span>
                @elseif($order->order_status === 'canceled')
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">Dibatalkan</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-600">Nama Pemesan</p>
                <p class="font-semibold text-gray-900">{{ $order->customer_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Tipe Pesanan</p>
                <p class="font-semibold text-gray-900">
                    @if($order->order_type === 'dine-in')
                        Dine In
                    @elseif($order->order_type === 'takeaway')
                        Takeaway
                    @else
                        Pick Up
                    @endif
                </p>
            </div>
            @if($order->table_number)
                <div>
                    <p class="text-sm text-gray-600">Nomor Meja</p>
                    <p class="font-semibold text-gray-900">{{ $order->table_number }}</p>
                </div>
            @endif
            <div>
                <p class="text-sm text-gray-600">Status Pembayaran</p>
                <p class="font-semibold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $order->payment_status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar' }}
                </p>
            </div>
        </div>

        <div class="border-t pt-4">
            <h3 class="font-semibold text-gray-900 mb-3">Item Pesanan</h3>
            <div class="divide-y divide-gray-200">
                @foreach($order->orderItems as $item)
                    <div class="py-3 flex justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-600">Qty: {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-semibold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t flex justify-between text-xl font-bold">
                <span>Total:</span>
                <span class="text-[#2e4358]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('menu.index') }}" class="inline-block bg-[#2e4358] text-white px-6 py-2 rounded-lg hover:bg-[#1a2a3a]">
            Kembali ke Menu
        </a>
    </div>
</div>
@endsection

