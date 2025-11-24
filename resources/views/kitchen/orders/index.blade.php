@extends('layouts.kitchen')

@section('title', 'Pesanan Processing - Kitchen')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Pesanan yang Perlu Dimasak</h1>
    <p class="text-sm text-gray-600 mt-1">Pesanan dengan status Processing</p>
</div>

@if($orders->isEmpty())
    <div class="text-center py-12 bg-white rounded-lg shadow">
        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <p class="mt-4 text-gray-500 text-lg">Tidak ada pesanan yang perlu dimasak.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($orders as $order)
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $order->order_code }}</h3>
                        <p class="text-sm text-gray-600">{{ $order->customer_name }}</p>
                        <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($order->table_number)
                        <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">
                            Meja {{ $order->table_number }}
                        </span>
                    @endif
                </div>

                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Item:</h4>
                    <ul class="space-y-1">
                        @foreach($order->orderItems as $item)
                            <li class="text-sm text-gray-900">
                                <span class="font-medium">{{ $item->quantity }}x</span> {{ $item->product->name }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="flex justify-between items-center pt-4 border-t">
                    <span class="text-sm font-semibold text-gray-900">
                        Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </span>
                    <form action="{{ route('kitchen.orders.ready', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm font-medium">
                            Tandai Siap
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection

