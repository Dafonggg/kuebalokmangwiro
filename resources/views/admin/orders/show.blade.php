@extends('layouts.admin')

@section('title', 'Detail Pesanan - Admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.orders.index') }}" class="text-[#2e4358] hover:text-[#1a2a3a] mb-4 inline-block">
        ← Kembali
    </a>
    <h1 class="text-2xl font-bold text-gray-900">Detail Pesanan: {{ $order->order_code }}</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pesanan</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Kode Pesanan</p>
                    <p class="font-medium text-gray-900">{{ $order->order_code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nama Pemesan</p>
                    <p class="font-medium text-gray-900">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tipe Pesanan</p>
                    <p class="font-medium text-gray-900">{{ ucfirst(str_replace('-', ' ', $order->order_type)) }}</p>
                </div>
                @if($order->table_number)
                    <div>
                        <p class="text-sm text-gray-500">Nomor Meja</p>
                        <p class="font-medium text-gray-900">{{ $order->table_number }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-sm text-gray-500">Tanggal</p>
                    <p class="font-medium text-gray-900">{{ $order->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Item Pesanan</h2>
            <div class="divide-y divide-gray-200">
                @foreach($order->orderItems as $item)
                    <div class="py-4 flex justify-between">
                        <div class="flex-1">
                            @if($item->isPackage() && $item->package)
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="font-medium text-gray-900">{{ $item->package->name }}</p>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-[#2e4358] text-white">Paket</span>
                                </div>
                                @if($item->components)
                                    <div class="text-xs text-gray-600 mt-1 ml-2">
                                        <p class="font-semibold mb-1">Isi paket:</p>
                                        <ul class="list-disc list-inside space-y-0.5">
                                            @foreach($item->components as $component)
                                                <li>{{ $component['name'] }} x{{ $component['qty'] }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @elseif($item->product)
                                <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                            @else
                                <p class="font-medium text-gray-900">Item #{{ $item->id }}</p>
                            @endif
                            <p class="text-sm text-gray-500 mt-1">Qty: {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-semibold text-gray-900 ml-4">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t flex justify-between text-lg font-bold">
                <span>Total:</span>
                <span class="text-[#2e4358]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($order->payments->count() > 0)
            @php
                $payment = $order->payments->first();
            @endphp
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pembayaran</h2>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Metode Pembayaran</p>
                        <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status Pembayaran</p>
                        <p class="font-medium text-gray-900">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $order->payment_status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar' }}
                            </span>
                        </p>
                    </div>
                </div>
                @if($payment->proof_of_payment)
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Bukti Pembayaran</p>
                        <div class="border border-gray-200 rounded-lg p-2 bg-gray-50">
                            <img src="{{ asset('storage/' . $payment->proof_of_payment) }}" 
                                 alt="Bukti Pembayaran" 
                                 class="max-w-full h-auto rounded cursor-pointer"
                                 onclick="window.open('{{ asset('storage/' . $payment->proof_of_payment) }}', '_blank')">
                        </div>
                        <a href="{{ asset('storage/' . $payment->proof_of_payment) }}" 
                           target="_blank" 
                           class="mt-2 inline-block text-sm text-[#2e4358] hover:text-[#1a2a3a]">
                            Buka gambar di tab baru →
                        </a>
                    </div>
                @elseif($payment->payment_method !== 'cash')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-yellow-800">Bukti pembayaran belum diunggah</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            <h2 class="text-lg font-semibold text-gray-900">Aksi</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ubah Status Pesanan</label>
                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <select name="order_status" class="w-full border border-gray-300 rounded-md px-3 py-2 mb-2">
                        <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="ready" {{ $order->order_status === 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="canceled" {{ $order->order_status === 'canceled' ? 'selected' : '' }}>Canceled</option>
                    </select>
                    <button type="submit" class="w-full bg-[#2e4358] text-white px-4 py-2 rounded-lg hover:bg-[#1a2a3a]">
                        Update Status
                    </button>
                </form>
            </div>

            @if($order->payment_status === 'unpaid')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Pembayaran</label>
                    <form action="{{ route('admin.orders.confirmPayment', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <select name="payment_method" class="w-full border border-gray-300 rounded-md px-3 py-2 mb-2">
                            <option value="cash">Cash</option>
                            <option value="qris">QRIS</option>
                            <option value="manual">Manual</option>
                        </select>
                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                            Konfirmasi Pembayaran
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-800 font-medium">Pembayaran sudah dikonfirmasi</p>
                    @if($order->payments->count() > 0)
                        <p class="text-xs text-green-600 mt-1">
                            Metode: {{ ucfirst($order->payments->first()->payment_method) }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

