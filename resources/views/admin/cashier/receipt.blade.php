<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $order->order_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                max-width: 100% !important;
                padding: 20px !important;
                box-shadow: none !important;
            }
        }
        .receipt-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="receipt-container">
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold mb-2" style="font-family: 'Arial Black', sans-serif;">Kue Balok Mang Wiro</h1>
            <p class="text-sm text-gray-700">Jl. Ostista no. 50, Subang</p>
            <p class="text-sm text-gray-700">Telp: 0812-2257-2886</p>
        </div>

        <!-- Transaction Details -->
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
                <span class="font-medium">No. Pesanan:</span>
                <span>{{ $order->order_code }}</span>
            </div>
            <div class="flex justify-between text-sm mb-1">
                <span class="font-medium">Tanggal:</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between text-sm mb-1">
                <span class="font-medium">Kasir:</span>
                <span>{{ auth()->user()->name }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="font-medium">Pelanggan:</span>
                <span>{{ $order->customer_name }}</span>
            </div>
        </div>

        <div class="dashed-line"></div>

        <!-- Item List Header -->
        <div class="grid grid-cols-3 gap-2 text-sm font-semibold mb-2">
            <span>Item</span>
            <span class="text-center">Jml</span>
            <span class="text-right">Total</span>
        </div>

        <div class="dashed-line"></div>

        <!-- Items -->
        <div class="mb-4">
            @foreach($order->orderItems as $item)
                <div class="mb-2">
                    <div class="grid grid-cols-3 gap-2 text-sm">
                        <span class="flex-1">
                            @if($item->isPackage() && $item->package)
                                <span class="font-semibold">{{ $item->package->name }}</span>
                                @if($item->components)
                                    <div class="text-xs text-gray-600 mt-0.5">
                                        @foreach($item->components as $component)
                                            <div>• {{ $component['name'] }} x{{ $component['qty'] }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            @elseif($item->product)
                                {{ $item->product->name }}
                            @else
                                Item #{{ $item->id }}
                            @endif
                        </span>
                        <span class="text-center">{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                        <span class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="dashed-line"></div>

        <!-- Summary -->
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-2">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-base font-bold mt-2">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="mb-4">
            <div class="flex justify-between text-sm">
                <span class="font-medium">Metode Bayar:</span>
                <span>
                    @if($order->payment_method === 'cash')
                        Tunai
                    @elseif($order->payment_method === 'qris')
                        QRIS
                    @elseif($order->payment_method === 'bank_transfer')
                        Bank Transfer
                    @else
                        Manual
                    @endif
                </span>
            </div>
        </div>

        <!-- Notes -->
        @if($order->notes)
            <div class="mb-4">
                <div class="text-sm">
                    <span class="font-medium">Catatan:</span>
                    <p class="mt-1 text-gray-700">{{ $order->notes }}</p>
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-6 mb-4">
            <p class="text-sm font-medium mb-2">Terima Kasih Atas Kunjungan Anda!</p>
            <p class="text-sm text-gray-600">@kuebalokmangwiro</p>
        </div>

        <!-- Print Button -->
        <div class="no-print text-center mt-6">
            <button onclick="window.print()" 
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                Cetak Ulang Struk
            </button>
            <div class="mt-4">
                <a href="{{ route('admin.cashier.index') }}" 
                   class="text-gray-600 hover:text-gray-900 text-sm underline">
                    Kembali ke Kasir
                </a>
            </div>
        </div>
    </div>
</body>
</html>

