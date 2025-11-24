@extends('layouts.admin')

@section('title', 'Pesanan - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Pesanan</h1>
</div>

<div class="bg-white shadow rounded-lg p-6 mb-6">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex gap-4">
        <select name="status" class="border border-gray-300 rounded-md px-3 py-2">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
        </select>
        <select name="payment_status" class="border border-gray-300 rounded-md px-3 py-2">
            <option value="">Semua Status Pembayaran</option>
            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
            <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
        </select>
        <button type="submit" class="bg-[#2e4358] text-white px-4 py-2 rounded-lg hover:bg-[#1a2a3a]">
            Filter
        </button>
    </form>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <ul class="divide-y divide-gray-200">
        @forelse($orders as $order)
            <li>
                <a href="{{ route('admin.orders.show', $order) }}" class="block hover:bg-gray-50">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $order->order_code }} - {{ $order->customer_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $order->created_at->format('d M Y H:i') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($order->order_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->order_status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->order_status === 'ready') bg-green-100 text-green-800
                                    @elseif($order->order_status === 'completed') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $order->payment_status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar' }}
                                </span>
                                @if($order->payments->count() > 0 && $order->payments->first()->proof_of_payment)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800" title="Bukti pembayaran tersedia">
                                        📎 Bukti
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </li>
        @empty
            <li class="px-4 py-4 text-center text-gray-500">
                Tidak ada pesanan.
            </li>
        @endforelse
    </ul>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection

